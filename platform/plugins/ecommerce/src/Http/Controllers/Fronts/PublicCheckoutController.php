<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Enums\DiscountTypeEnum;
use Botble\Ecommerce\Enums\OrderHistoryActionEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingCodStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Facades\Discount;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\GoogleAnalytics\GoogleTagManager;
use Botble\Ecommerce\Http\Requests\ApplyCouponRequest;
use Botble\Ecommerce\Http\Requests\CheckoutRequest;
use Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount as DiscountModel;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Services\Footprints\FootprinterInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Ecommerce\Services\HandleTaxService;
use Botble\Location\Models\Country;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Botble\Base\Facades\EmailHandler;
use Illuminate\Support\Facades\Log;
use Exception;

use Throwable;

class PublicCheckoutController extends BaseController
{
    public function __construct()
    {
        if (class_exists(OptimizerHelper::class)) {
            OptimizerHelper::disable();
        }
    }

    public function getCheckout(
        string $token,
        Request $request,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $applyPromotionsService,
        HandleTaxService $handleTaxService
    ) {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = Order::query()->where(['token' => $token, 'is_finished' => false])->first();

            if (! $order) {
                return $this
                    ->httpResponse()
                    ->setNextUrl(BaseHelper::getHomepageUrl());
            }
        }

        if (
            ! $request->session()->has('error_msg') &&
            $request->input('error') == 1 &&
            $request->input('error_type') == 'payment'
        ) {
            $request->session()->flash('error_msg', __('Payment failed!'));
        }

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);

        $products = Cart::instance('cart')->products();
        if (! $products->count()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('public.cart'));
        }

        foreach ($products as $product) {
            if ($product->isOutOfStock()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setNextUrl(route('public.cart'))
                    ->setMessage(
                        __('Product :product is out of stock!', ['product' => $product->original_product->name])
                    );
            }
        }

        if (EcommerceHelper::isEnabledSupportDigitalProducts() && ! EcommerceHelper::canCheckoutForDigitalProducts(
            $products
        )) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        $handleTaxService->execute($products, $sessionCheckoutData);
       
        $sessionCheckoutData = $this->processOrderData($token, $sessionCheckoutData, $request);
       
        $paymentMethod = null;

        if (is_plugin_active('payment')) {
            $paymentMethod = $request->input(
                'payment_method',
                session('selected_payment_method') ?: PaymentHelper::defaultPaymentMethod()
            );
        }

        if ($paymentMethod) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        add_filter('payment_checkout_country', function ($default) use ($sessionCheckoutData) {
            if ($country = Arr::get($sessionCheckoutData, 'country')) {
                if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation()) {
                    $country = Country::query()
                        ->where('id', $country)
                        ->value('code');
                }

                return $country;
            }

            return $default;
        }, 999);

        if (is_plugin_active('marketplace')) {
            [
                $sessionCheckoutData,
                $shipping,
                $defaultShippingMethod,
                $defaultShippingOption,
                $shippingAmount,
                $promotionDiscountAmount,
                $couponDiscountAmount,
            ] = apply_filters(PROCESS_CHECKOUT_ORDER_DATA_ECOMMERCE, $products, $token, $sessionCheckoutData, $request);
        } else {
            $promotionDiscountAmount = $applyPromotionsService->execute($token);

            $sessionCheckoutData['promotion_discount_amount'] = $promotionDiscountAmount;

            $couponDiscountAmount = 0;
            if (session()->has('applied_coupon_code')) {
                $couponDiscountAmount = Arr::get($sessionCheckoutData, 'coupon_discount_amount', 0);
            }

            $orderTotal = Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount;
            $orderTotal = max($orderTotal, 0);

            $isAvailableShipping = EcommerceHelper::isAvailableShipping($products);

            $shipping = [];
            $defaultShippingMethod = $request->input(
                'shipping_method',
                Arr::get($sessionCheckoutData, 'shipping_method', ShippingMethodEnum::DEFAULT)
            );
            $defaultShippingOption = $request->input(
                'shipping_option',
                Arr::get($sessionCheckoutData, 'shipping_option')
            );
            $shippingAmount = 0;
          
            if ($isAvailableShipping) {
                $origin = EcommerceHelper::getOriginAddress();
                $shippingData = EcommerceHelper::getShippingData(
                    $products,
                    $sessionCheckoutData,
                    $origin,
                    $orderTotal,
                    $paymentMethod
                );
                
                $shipping = $shippingFeeService->execute($shippingData);
                
                foreach ($shipping as $key => &$shipItem) {
                    if (get_shipping_setting('free_ship', $key)) {
                        foreach ($shipItem as &$subShippingItem) {
                            Arr::set($subShippingItem, 'price', 0);
                        }
                    }
                }
             
                if ($shipping) {
                    if (! $defaultShippingMethod) {
                        $defaultShippingMethod = old(
                            'shipping_method',
                            Arr::get($sessionCheckoutData, 'shipping_method', Arr::first(array_keys($shipping)))
                        );
                    }
                    if (! $defaultShippingOption) {
                        $defaultShippingOption = old(
                            'shipping_option',
                            Arr::get($sessionCheckoutData, 'shipping_option', $defaultShippingOption)
                        );

                        if (! $defaultShippingOption) {
                            $defaultShippingOption = Arr::first(array_keys(Arr::first($shipping)));
                        }
                    }
                }
              
                $shippingAmount = Arr::get(
                    $shipping,
                    $defaultShippingMethod . '.' . $defaultShippingOption . '.price',
                    0
                );
              
             

                if (array_key_exists('englandlogistics', $shipping)) {
                    $shippingAmount = Session('shipammount');
                    $newArray = [
                        'name' => Session('shipmethod'),
                        'price' => Session('shipammount')
                    ];
                   array_push($shipping['englandlogistics'], $newArray);
                   Arr::set($sessionCheckoutData, 'shipping_method', Session('shipmethod'));
                   Arr::set($sessionCheckoutData, 'shipping_option', $defaultShippingOption);
                   Arr::set($sessionCheckoutData, 'shipping_amount', $shippingAmount);
                   $shipping_vendor='englandlogistics';
                } else {
                    Arr::set($sessionCheckoutData, 'shipping_method', $defaultShippingMethod);
                    Arr::set($sessionCheckoutData, 'shipping_option', $defaultShippingOption);
                    Arr::set($sessionCheckoutData, 'shipping_amount', $shippingAmount);
                    $shipping_vendor=$defaultShippingMethod;
                }

                OrderHelper::setOrderSessionData($token, $sessionCheckoutData);
            }

            if (session()->has('applied_coupon_code')) {
                if (! $request->input('applied_coupon')) {
                    $discount = $applyCouponService->getCouponData(
                        session('applied_coupon_code'),
                        $sessionCheckoutData
                    );
                    if (empty($discount)) {
                        $removeCouponService->execute();
                    } else {
                        $shippingAmount = Arr::get($sessionCheckoutData, 'is_free_shipping') ? 0 : $shippingAmount;
                    }
                } else {
                    $shippingAmount = Arr::get($sessionCheckoutData, 'is_free_shipping') ? 0 : $shippingAmount;
                }
            }

            $sessionCheckoutData['is_available_shipping'] = $isAvailableShipping;

            if (! $sessionCheckoutData['is_available_shipping']) {
                $shippingAmount = 0;
            }
        }

        $isShowAddressForm = EcommerceHelper::isSaveOrderShippingAddress($products);

        $data = compact(
            'token',
            'shipping',
            'defaultShippingMethod',
            'defaultShippingOption',
            'shippingAmount',
            'promotionDiscountAmount',
            'couponDiscountAmount',
            'sessionCheckoutData',
            'products',
            'isShowAddressForm'
        );

 
        if (auth('customer')->check()) {
            $addresses = auth('customer')->user()->addresses;
            $isAvailableAddress = ! $addresses->isEmpty();

            if (Arr::get($sessionCheckoutData, 'is_new_address')) {
                $sessionAddressId = 'new';
            } else {
                $sessionAddressId = Arr::get(
                    $sessionCheckoutData,
                    'address_id',
                    $isAvailableAddress ? $addresses->first()->id : null
                );
                if (! $sessionAddressId && $isAvailableAddress) {
                    $address = $addresses->firstWhere('is_default') ?: $addresses->first();
                    $sessionAddressId = $address->id;
                }
            }

            $data = array_merge($data, compact('addresses', 'isAvailableAddress', 'sessionAddressId'));
        }

        $discountsQuery = DiscountModel::query()
            ->where('type', DiscountTypeEnum::COUPON)
            ->where('display_at_checkout', true)
            ->active()
            ->available();

        $discounts = apply_filters('ecommerce_checkout_discounts_query', $discountsQuery, $products)->get();

        $rawTotal = Cart::instance('cart')->rawTotal();
        $orderAmount = max($rawTotal - $promotionDiscountAmount - $couponDiscountAmount, 0);
        $orderAmount += (float) $shippingAmount;

        $data = [...$data, 'discounts' => $discounts, 'rawTotal' => $rawTotal, 'orderAmount' => $orderAmount];

        app(GoogleTagManager::class)->beginCheckout(
            $products->all(),
            $orderAmount,
            isset($discount) ? $discount->code : null
        );

        $checkoutView = Theme::getThemeNamespace('views.ecommerce.orders.checkout');

        if (view()->exists($checkoutView)) {
            return view($checkoutView, $data);
        }

        return view('plugins/ecommerce::orders.checkout', $data);
    }

    protected function processOrderData(
        string $token,
        array $sessionData,
        Request $request,
        bool $finished = false
    ): array {
        if ($request->has('billing_address_same_as_shipping_address')) {
            $sessionData['billing_address_same_as_shipping_address'] = $request->boolean(
                'billing_address_same_as_shipping_address'
            );
        }

        if ($request->has('billing_address')) {
            $sessionData['billing_address'] = $request->input('billing_address');
        }

        if ($request->has('address.address_id')) {
            $sessionData['is_new_address'] = $request->input('address.address_id') == 'new';
        }

        if ($request->input('address', [])) {
            if (! isset($sessionData['created_account']) && $request->input('create_account') == 1) {
                $validator = Validator::make($request->input(), [
                    'password' => ['required', 'min:6'],
                    'password_confirmation' => ['required', 'same:password'],
                    'address.email' => ['required', 'max:60', 'min:6', 'email', 'unique:ec_customers,email'],
                    'address.name' => ['required', 'min:3', 'max:120'],
                ]);

                if ($validator->passes()) {
                    $customerId = null;

                    try {
                        /**
                         * @var Customer $customer
                         */
                        $customer = Customer::query()->create([
                            'name' => $request->input('address.name'),
                            'email' => $request->input('address.email'),
                            'phone' => $request->input('address.phone'),
                            'password' => Hash::make($request->input('password')),
                        ]);

                        auth('customer')->loginUsingId($customer->getKey(), true);

                        event(new Registered($customer));

                        $sessionData['created_account'] = true;

                        $customerId = $customer->getKey();
                    } catch (Throwable $exception) {
                        BaseHelper::logError($exception);
                    }

                    $address = Address::query()
                        ->create(
                            array_merge($request->input('address'), [
                                'customer_id' => $customerId,
                                'is_default' => true,
                            ])
                        );

                    $request->merge(['address.address_id' => $address->id]);
                    $sessionData['address_id'] = $address->id;
                }
            }

            if ($finished && auth('customer')->check()) {
                $customer = auth('customer')->user();
                if ($customer->addresses->count() == 0 || $request->input('address.address_id') == 'new') {
                    $address = Address::query()
                        ->create(
                            array_merge($request->input('address', []), [
                                'customer_id' => auth('customer')->id(),
                                'is_default' => $customer->addresses->count() == 0,
                            ])
                        );

                    $request->merge(['address.address_id' => $address->id]);
                    $sessionData['address_id'] = $address->id;
                }
            }
        }

        $address = null;

        if (($addressId = $request->input('address.address_id')) && $addressId !== 'new') {
            $address = Address::query()->find($addressId);
            if ($address) {
                $sessionData['address_id'] = $address->getKey();
            }
        } elseif (auth('customer')->check() && ! Arr::get($sessionData, 'address_id')) {
            $address = Address::query()->where([
                'customer_id' => auth('customer')->id(),
                'is_default' => true,
            ])->first();

            if ($address) {
                $sessionData['address_id'] = $address->id;
            }
        }

        $addressData = [
            'billing_address_same_as_shipping_address' => Arr::get(
                $sessionData,
                'billing_address_same_as_shipping_address',
                true
            ),
            'billing_address' => Arr::get($sessionData, 'billing_address', []),
        ];

        if (! empty($address)) {
            $addressData = [
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'country' => $address->country,
                'state' => $address->state,
                'city' => $address->city,
                'address' => $address->address,
                'zip_code' => $address->zip_code,
                'address_id' => $address->id,
            ];
        } elseif ($addressFromInput = (array) $request->input('address', [])) {
            $addressData = $addressFromInput;
        }

        $addressData = OrderHelper::cleanData($addressData);

        $sessionData = array_merge($sessionData, $addressData);

        Cart::instance('cart')->refresh();

        $products = Cart::instance('cart')->products();

        if (is_plugin_active('marketplace')) {
            $sessionData = apply_filters(
                HANDLE_PROCESS_ORDER_DATA_ECOMMERCE,
                $products,
                $token,
                $sessionData,
                $request
            );

            OrderHelper::setOrderSessionData($token, $sessionData);

            return $sessionData;
        }

        if (! isset($sessionData['created_order'])) {
            $currentUserId = 0;
            if (auth('customer')->check()) {
                $currentUserId = auth('customer')->id();
            }

            $request->merge([
                'amount' => Cart::instance('cart')->rawTotal(),
                'user_id' => $currentUserId,
                'shipping_method' => $request->input('shipping_method', ShippingMethodEnum::DEFAULT),
                'shipping_option' => $request->input('shipping_option'),
                'shipping_amount' => 0,
                'tax_amount' => Cart::instance('cart')->rawTax(),
                'sub_total' => Cart::instance('cart')->rawSubTotal(),
                'coupon_code' => session('applied_coupon_code'),
                'discount_amount' => 0,
                'status' => OrderStatusEnum::PENDING,
                'is_finished' => false,
                'token' => $token,
            ]);

            $order = Order::query()->where(compact('token'))->first();

            $order = $this->createOrderFromData($request->input(), $order);

            $sessionData['created_order'] = true;
            $sessionData['created_order_id'] = $order->getKey();
        }

        if (! empty($address)) {
            $addressData['order_id'] = $sessionData['created_order_id'];
        } elseif ((array) $request->input('address', [])) {
            $addressData = array_merge(
                ['order_id' => $sessionData['created_order_id']],
                (array) $request->input('address', [])
            );
        }

        $sessionData['is_save_order_shipping_address'] = EcommerceHelper::isSaveOrderShippingAddress($products);

        $sessionData = OrderHelper::checkAndCreateOrderAddress($addressData, $sessionData);

        if (! isset($sessionData['created_order_product'])) {
            $weight = Cart::instance('cart')->weight();

            OrderProduct::query()->where(['order_id' => $sessionData['created_order_id']])->delete();

            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = Product::query()->find($cartItem->id);

                if (! $product) {
                    continue;
                }

                $data = [
                    'order_id' => $sessionData['created_order_id'],
                    'product_id' => $cartItem->id,
                    'product_name' => $cartItem->name,
                    'product_image' => $product->original_product->image,
                    'qty' => $cartItem->qty,
                    'weight' => $weight,
                    'price' => $cartItem->price,
                    'tax_amount' => $cartItem->tax,
                    'options' => $cartItem->options,
                    'product_type' => $product?->product_type,
                ];

                if (isset($cartItem->options['options'])) {
                    $data['product_options'] = $cartItem->options['options'];
                }

                OrderProduct::query()->create($data);
            }

            $sessionData['created_order_product'] = Cart::instance('cart')->getLastUpdatedAt();
        }

        OrderHelper::setOrderSessionData($token, $sessionData);

        return $sessionData;
    }

    public function postSaveInformation(
        string $token,
        SaveCheckoutInformationRequest $request,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService
    ) {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = Order::query()->where(['token' => $token, 'is_finished' => false])->first();

            if (! $order) {
                return $this
                    ->httpResponse()
                    ->setNextUrl(BaseHelper::getHomepageUrl());
            }
        }

        if ($paymentMethod = $request->input('payment_method')) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));

            $sessionData = apply_filters(
                PROCESS_POST_SAVE_INFORMATION_CHECKOUT_ECOMMERCE,
                $sessionData,
                $request,
                $token
            );
        } else {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));
            OrderHelper::setOrderSessionData($token, $sessionData);
            if (session()->has('applied_coupon_code')) {
                $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
                if (! $discount) {
                    $removeCouponService->execute();
                }
            }
        }

        $sessionData = $this->processOrderData($token, $sessionData, $request);

        return $this
            ->httpResponse()
            ->setData($sessionData);
    }

    public function postCheckout(
        string $token,
        CheckoutRequest $request,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $handleApplyPromotionsService
    ) {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }

        if (Cart::instance('cart')->isEmpty()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('No products in cart'));
        }

        $products = Cart::instance('cart')->products();

        if (
            EcommerceHelper::isEnabledSupportDigitalProducts() &&
            ! EcommerceHelper::canCheckoutForDigitalProducts($products)
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital/gift product(s), so you need to sign in to continue!'));
        }

        $totalQuality = Cart::instance('cart')->rawTotalQuantity();

        if (($minimumQuantity = EcommerceHelper::getMinimumOrderQuantity()) > 0
            && $totalQuality < $minimumQuantity) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Minimum order quantity is :qty, you need to buy more :more to place an order!', [
                        'qty' => $totalQuality,
                        'more' => $minimumQuantity - $totalQuality,
                    ])
                );
        }

        if (($maximumQuantity = EcommerceHelper::getMaximumOrderQuantity()) > 0
            && $totalQuality > $maximumQuantity) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Maximum order quantity is :qty, please check your cart and retry again!', [
                        'qty' => $maximumQuantity,
                    ])
                );
        }

        if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Minimum order amount is :amount, you need to buy more :more to place an order!', [
                        'amount' => format_price(EcommerceHelper::getMinimumOrderAmount()),
                        'more' => format_price(
                            EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')
                                ->rawSubTotal()
                        ),
                    ])
                );
        }

        $sessionData = OrderHelper::getOrderSessionData($token);

        $sessionData = $this->processOrderData($token, $sessionData, $request, true);

        foreach ($products as $product) {
            if ($product->isOutOfStock()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Product :product is out of stock!', ['product' => $product->original_product->name])
                    );
            }

            $quantityOfProduct = Cart::instance('cart')->rawQuantityByItemId($product->id);

            if ($product->minimum_order_quantity > 0 && $quantityOfProduct < $product->minimum_order_quantity) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Minimum order quantity of product :product is :quantity, you need to buy more :more to place an order! ', [
                            'product' => $product->original_product->name,
                            'quantity' => $product->minimum_order_quantity,
                            'more' => $product->minimum_order_quantity - $quantityOfProduct,
                        ])
                    );
            }

            if ($product->maximum_order_quantity > 0 && $quantityOfProduct > $product->maximum_order_quantity) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Maximum order quantity of product :product is :quantity! ', [
                            'product' => $product->original_product->name,
                            'quantity' => $product->minimum_order_quantity,
                        ])
                    );
            }
        }

        $paymentMethod = $request->input('payment_method', session('selected_payment_method'));

        if ($paymentMethod) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        do_action('ecommerce_post_checkout', $products, $request, $token, $sessionData);

        if (is_plugin_active('marketplace')) {
            return apply_filters(
                HANDLE_PROCESS_POST_CHECKOUT_ORDER_DATA_ECOMMERCE,
                $products,
                $request,
                $token,
                $sessionData,
                $this->httpResponse()
            );
        }

        $isAvailableShipping = EcommerceHelper::isAvailableShipping($products);

        if($request->input('shipping_vendor') == 'englandlogistics'){
        $shippingMethodInput = ShippingMethodEnum::DEFAULT;
            }
            else{
                $shippingMethodInput = $request->input('shipping_method', ShippingMethodEnum::DEFAULT);

            }

          
        $promotionDiscountAmount = $handleApplyPromotionsService->execute($token);
        $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount');
        $rawTotal = Cart::instance('cart')->rawTotal();
        $orderAmount = max($rawTotal - $promotionDiscountAmount - $couponDiscountAmount, 0);

        $shippingAmount = 0;

        $shippingData = [];
        if ($isAvailableShipping) {
            $origin = EcommerceHelper::getOriginAddress();
            $shippingData = EcommerceHelper::getShippingData(
                $products,
                $sessionData,
                $origin,
                $orderAmount,
                $paymentMethod
            );

            $shippingMethodData = $shippingFeeService->execute(
                $shippingData,
                $shippingMethodInput,
                $request->input('shipping_option')
            );

            if($request->input('shipping_vendor') == 'englandlogistics'){
                $shippingMethod = ShippingMethodEnum::DEFAULT;
            } else {
                $shippingMethod = Arr::first($shippingMethodData);
                if (! $shippingMethod) {
                    throw ValidationException::withMessages([
                        'shipping_method' => trans(
                            'validation.exists',
                            ['attribute' => trans('plugins/ecommerce::shipping.shipping_method')]
                        ),
                    ]);
                }        
            }
           
            $shippingAmount = Arr::get($shippingMethod, 'price', 0);

            if (get_shipping_setting('free_ship', $shippingMethodInput)) {
                $shippingAmount = 0;
            }
        }

        if (session()->has('applied_coupon_code')) {
            $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
            if (empty($discount)) {
                $removeCouponService->execute();
            } else {
                $shippingAmount = Arr::get($sessionData, 'is_free_shipping') ? 0 : $shippingAmount;
            }
        }

        $currentUserId = 0;
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->id();
        }

        $orderAmount += (float) $shippingAmount;
        
        $giftCode = $request->input('gift_code');
        $giftValue = $request->input('gift_value');

        $giftDiscountAmount = 0;

        if ($giftCode && $giftValue) {
            $coupon = DiscountModel::where('code', $giftCode)->first();

            if ($coupon) {
                if ($giftValue > $orderAmount) {
                    $giftValue = $giftValue - $orderAmount;
                    $orderAmount = 0;
                } else {
                    $orderAmount -= $giftValue;
                    $giftValue = 0;
                }
                $coupon->value = $giftValue;
                $coupon->save();
            }
        }

        $request->merge([
            'amount' => $orderAmount ?: 0,
            'currency' => $request->input('currency', strtoupper(get_application_currency()->title)),
            'user_id' => $currentUserId,
            'shipping_method' => $isAvailableShipping ? $shippingMethodInput : '',
            'shipping_option' => $isAvailableShipping ? $request->input('shipping_option') : null,
            'shipping_amount' => (float) $shippingAmount,
            'tax_amount' => Cart::instance('cart')->rawTax(),
            'sub_total' => Cart::instance('cart')->rawSubTotal(),
            'coupon_code' => session('applied_coupon_code'),
            'discount_amount' => $promotionDiscountAmount + $couponDiscountAmount,
            'status' => OrderStatusEnum::PENDING,
            'token' => $token,
        ]);
      
        $order = Order::query()->where(compact('token'))->first();

        $order = $this->createOrderFromData($request->input(), $order);

        $p=[];
        
        foreach($products as $f){
            array_push($p,$f->product_type);
        }
        
        if ( in_array("gift", $p) && !in_array("physical", $p) || in_array("digital", $p) && !in_array("physical", $p) ){
            
        } else {
            $ship = $this->postshippment($request->input('address'),$order,$products);
        }

        OrderHistory::query()->create([
            'action' => OrderHistoryActionEnum::CREATE_ORDER_FROM_PAYMENT_PAGE,
            'description' => __('Order was created from checkout page'),
            'order_id' => $order->getKey(),
        ]);

        if ($isAvailableShipping) {
            Shipment::query()->create([
                'order_id' => $order->getKey(),
                'user_id' => 0,
                'weight' => $shippingData ? Arr::get($shippingData, 'weight') : 0,
                'cod_amount' => (is_plugin_active(
                    'payment'
                ) && $order->payment->id && $order->payment->status != PaymentStatusEnum::COMPLETED) ? $order->amount : 0,
                'cod_status' => ShippingCodStatusEnum::PENDING,
                'type' => $order->shipping_method,
                'status' => ShippingStatusEnum::PENDING,
                'price' => $order->shipping_amount,
                'rate_id' => $shippingData ? Arr::get($shippingMethod, 'id', '') : '',
                'shipment_id' => $shippingData ? Arr::get($shippingMethod, 'shipment_id', '') : '',
                'shipping_company_name' => $shippingData ? Arr::get($shippingMethod, 'company_name', '') : '',
            ]);
        }

        if (
            EcommerceHelper::isDisplayTaxFieldsAtCheckoutPage() &&
            $request->boolean('with_tax_information')
        ) {
            $order->taxInformation()->create($request->input('tax_information'));
        }

        if ($appliedCouponCode = session('applied_coupon_code')) {
            Discount::getFacadeRoot()->afterOrderPlaced($appliedCouponCode);
        }

        OrderProduct::query()->where(['order_id' => $order->getKey()])->delete();

        foreach (Cart::instance('cart')->content() as $cartItem) {
            $product = Product::query()->find($cartItem->id);

            if (! $product) {
                continue;
            }

            $data = [
                'order_id' => $order->getKey(),
                'product_id' => $cartItem->id,
                'product_name' => $cartItem->name,
                'product_image' => $product->original_product->image,
                'qty' => $cartItem->qty,
                'weight' => Arr::get($cartItem->options, 'weight', 0),
                'price' => $cartItem->price,
                'tax_amount' => $cartItem->tax,
                'options' => $cartItem->options,
                'product_type' => $product?->product_type,
            ];

            if (isset($cartItem->options['options'])) {
                $data['product_options'] = $cartItem->options['options'];
            }

            OrderProduct::query()->create($data);
        }

        $request->merge([
            'order_id' => $order->getKey(),
        ]);

        do_action('ecommerce_before_processing_payment', $products, $request, $token, $sessionData);

        if (! is_plugin_active('payment') || ! $orderAmount) {
            OrderHelper::processOrder($order->getKey());

            return redirect()->to(route('public.checkout.success', OrderHelper::getOrderSessionToken()));
        }

        $paymentData = [
            'error' => false,
            'message' => false,
            'amount' => (float) format_price($order->amount, null, true),
            'currency' => strtoupper(get_application_currency()->title),
            'type' => $request->input('payment_method'),
            'charge_id' => null,
        ];
        
        foreach (Cart::instance('cart')->content() as $cart) {
            foreach ($products as $product) {
                if ($product->product_type == 'gift') {
                    $id = 'Product_' . $cart->id;
                    $session = session($id);
                    $emails = $session['contact_email'];
                    $email = explode(',', $emails);
                    for ($i = 0; $i < $cart->qty; $i++) {
                        do { $code = 'GIFT'.strtoupper(Str::random(8)); } while (DiscountModel::query()->where('code', $code)->exists());
                        DB::beginTransaction();
                        try {
                            $coupon = new DiscountModel();
                            $coupon->code = $code;
                            $coupon->start_date = now()->format('Y-m-d');
                            $coupon->value = $product->price;
                            $coupon->can_use_with_promotion = 1;
                            $coupon->quantity = 1;
                            $coupon->display_at_checkout = 1;
                            $coupon->save();
                            DB::commit();
                            if(isset($email[$i])){
                                $this->sendemail($email[$i], $code);
                            } else {
                                $this->sendemail($email[0], $code);
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            throw $e;
                        }
                        Session::forget($id);
                    }
                }
            }
        }

        $paymentData = apply_filters(FILTER_ECOMMERCE_PROCESS_PAYMENT, $paymentData, $request);

        if ($checkoutUrl = Arr::get($paymentData, 'checkoutUrl')) {
            return $this
                ->httpResponse()
                ->setError($paymentData['error'])
                ->setNextUrl($checkoutUrl)
                ->setData(['checkoutUrl' => $checkoutUrl])
                ->withInput()
                ->setMessage($paymentData['message']);
        }

        if ($paymentData['error'] || ! $paymentData['charge_id']) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL($token))
                ->withInput()
                ->setMessage($paymentData['message'] ?: __('Checkout error!'));
        }
        
        if (in_array("gift", $p)) {
            return $this->httpResponse()->setData($code)->setNextUrl(PaymentHelper::getRedirectURL($token))->setMessage(__("Checkout successfully!"));
        }

        return $this->httpResponse()->setNextUrl(PaymentHelper::getRedirectURL($token))->setMessage(__('Checkout successfully!'));
    }
    
    protected function sendemail($email, $code)
    {
        try {
            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('download_gift_products')) {
                $mailer->setVariableValue('code', $code);
                EmailHandler::send(
                    $mailer->getTemplateContent('download_gift_products'),
                    $mailer->getTemplateSubject('download_gift_products'),
                    $email ?: $email
                );
            }
            return true;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function getCheckoutSuccess(string $token)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        $order = Order::query()
            ->where('token', $token)
            ->with(['address', 'products', 'taxInformation'])
            ->orderByDesc('id')
            ->first();

        if (! $order) {
            abort(404);
        }

        if (is_plugin_active('payment') && (float) $order->amount && ! $order->payment_id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(__('Payment failed!'));
        }

        if (session('tracked_start_checkout')) {
            app(GoogleTagManager::class)->purchase($order);
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_SUCCESS_IN_ORDER, $token, $this->httpResponse());
        }

        $products = $order->getOrderProducts();

        OrderHelper::clearSessions($token);

        return view('plugins/ecommerce::orders.thank-you', compact('order', 'products'));
    }

    public function postApplyCoupon(ApplyCouponRequest $request, HandleApplyCouponService $handleApplyCouponService)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }
        $result = [
            'error' => false,
            'message' => '',
        ];
        if (is_plugin_active('marketplace')) {
            $result = apply_filters(HANDLE_POST_APPLY_COUPON_CODE_ECOMMERCE, $result, $request);
        } else {
            $result = $handleApplyCouponService->execute($request->input('coupon_code'));
        }

        if ($result['error']) {
            return $this
                ->httpResponse()
                ->setError()
                ->withInput()
                ->setMessage($result['message']);
        }

        $couponCode = $request->input('coupon_code');

        return $this
            ->httpResponse()
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function postRemoveCoupon(Request $request, HandleRemoveCouponService $removeCouponService)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (is_plugin_active('marketplace')) {
            $products = Cart::instance('cart')->products();
            $result = apply_filters(HANDLE_POST_REMOVE_COUPON_CODE_ECOMMERCE, $products, $request);
        } else {
            $result = $removeCouponService->execute();
        }

        if ($result['error']) {
            if ($request->ajax()) {
                return $result;
            }

            return $this
                ->httpResponse()
                ->setError()
                ->setData($result)
                ->setMessage($result['message']);
        }

        return $this
            ->httpResponse()
            ->setMessage(__('Removed coupon :code successfully!', ['code' => session('applied_coupon_code')]));
    }

    public function getCheckoutRecover(string $token, Request $request)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_RECOVER_ECOMMERCE, $token, $request);
        }

        $order = Order::query()
            ->where([
                'token' => $token,
                'is_finished' => false,
            ])
            ->with(['products', 'address'])
            ->first();

        if (! $order) {
            abort(404);
        }

        if (session()->has('tracked_start_checkout') && session('tracked_start_checkout') == $token) {
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        } else {
            session(['tracked_start_checkout' => $token]);
            $sessionCheckoutData = [
                'name' => $order->address->name,
                'email' => $order->address->email,
                'phone' => $order->address->phone,
                'address' => $order->address->address,
                'country' => $order->address->country,
                'state' => $order->address->state,
                'city' => $order->address->city,
                'zip_code' => $order->address->zip_code,
                'shipping_method' => $order->shipping_method,
                'shipping_option' => $order->shipping_option,
                'shipping_amount' => $order->shipping_amount,
            ];
        }

        Cart::instance('cart')->destroy();
        foreach ($order->products as $orderProduct) {
            $request->merge(['qty' => $orderProduct->qty]);

            $product = Product::query()->find($orderProduct->product_id);

            if ($product) {
                OrderHelper::handleAddCart($product, $request);
            }
        }

        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        return $this
            ->httpResponse()
            ->setNextUrl(route('public.checkout.information', $token))
            ->setMessage(__('You have recovered from previous orders!'));
    }

    protected function createOrderFromData(array $data, ?Order $order): Order|null|false
    {
        $data['is_finished'] = false;

        if ($order) {
            $order->fill($data);
            $order->save();
        } else {
            $order = Order::query()->create($data);
        }

        if (! $order->referral()->count()) {
            $referrals = app(FootprinterInterface::class)->getFootprints();

            if ($referrals) {
                $order->referral()->create($referrals);
            }
        }

        do_action('ecommerce_create_order_from_data', $data, $order);

        return $order;
    }



    protected function postshippment($r,$s,$t)
    {

   

$name=$r['name'];
$email=$r['email'];
$phone=$r['phone'];
$country=$r['country'];
$state=$r['state'];
$city=$r['city'];
$address=$r['address'];
$zip_code=$r['zip_code'];

$o = [];

  foreach($t as $g)
{
    
$a=    $g->cartItem->id ;
$b=     $g->cartItem->options->sku;
$c=        $g->cartItem->name;
$d= $g->cartItem->price;
$e=  (int)$g->cartItem->qty;
$f= $g->cartItem->options->weight;
$h =  url($g->cartItem->options->image);

    $oo = [
        'productId' => "$a" ,
        'sku' =>"$b",
        'title' =>"$c",
'price' => "$d",
'quantity' => $e,
'weight' => "$f",
'imgUrl' =>"$h",
'htsNumber' => null,
"countryOfOrigin"=> "US",
"lineId" => "1",
"pickLocation"=> "100-03S"
    ];
 array_push($o,$oo);
}

echo '<pre>';


$api_key = 'CUEbrqNjxQJL1ykWEkcVutli7RA9qbel';
$cus_id = '20601807';
$int_id = '4191';
$base = 'https://englandship.rocksolidinternet.com';
$date = Carbon::now(); 


$date = $s->updated_at->format('Y-m-d');
$b  = session('shipmethod');
$w = session('weightpro');
$endpoint = $base."/restapi/v1/customers/20601807/integrations/4191/orders/$s->id";



$data = [

    "orderId"=> "$s->id",
    "orderDate"=>"$date",
    "dueByDate"=> null,
    "orderNumber"=> "AERI $s->code",
    "fulfillmentStatus"=> "pending",
    "shippingService"=> "$b",
    "shippingTotal"=> "$s->shipping_amount",
    "weightUnit"=> "lb",
    "dimUnit"=> "in",
    "orderGroup"=> null,
    "contentDescription"=> "AERIAL ESSENTIALS Stuff and things",
    "sender"=> [
        "name"=> "AERIAL ESSENTIALS",
        "company"=> "AERIAL ESSENTIALS",
        "address1"=> "4460 W RENO AVE SUITE G",
        "address2"=> "",
        "city"=> "LAS VEGAS",
        "state"=> "Nevada",
        "zip"=> " 89118",
        "country"=> "US",
        "phone"=> "702-907-2359",
        "email"=> "aerialessentials@gmail.com"
    ],
    "receiver"=> [
        "name"=> "$name",
        "company"=> "",
        "address1"=> "$address",
        "address2"=> "",
        "city"=> "$city",
        "state"=> "$state",
        "zip"=> "$zip_code",
        "country"=> "$country",
        "phone"=> "$phone",
        "email"=> "$email"
    ],
    "items"=>$o,
    "packages"=> [
        [
          "weight"=> "$w",
          "length"=> "",
          "width"=> "",
          "height"=> "",
          "insuranceAmount"=> null,
          "declaredValue"=> null
        ]],
    ];



  
    $datas = json_encode($data);

    

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
    'Authorization' => 'Bearer ' . $api_key
])->PUT($endpoint, $data);

if ($response->successful()) {
    $array_data = $response->json();
    Session::forget('shipammount');
    Session::forget('shipmethod');
        return $array_data; 
} else {
   return $response->status();
}
    }



    function shipup($scarrier,$scode,$sptc,$scity,$scountry,$szip,$sweight){

    
        $api_key = 'CUEbrqNjxQJL1ykWEkcVutli7RA9qbel';
        $cus_id = '20601807';
        $int_id = '4191';
        $base = 'https://englandship.rocksolidinternet.com';
        
        $endpoint = $base . '/restapi/v1/customers/' . $cus_id . '/quote';
                $data = [
                    "carrierCode" =>  $scarrier,
                    "serviceCode" => $scode,
                    "packageTypeCode" => $sptc,
                    "sender" => [
                        'zip' => '89118',
                        'country' => 'US'
                    ],
                    "receiver" => [
                        "city" =>  $scity ,
                        "zip" =>  $szip ,
                        "country" =>  $scountry,
                    ],
                    "residential" => false,
                    "signatureOptionCode" => null,
                    "contentDescription" => "stuff and things",
                    "weightUnit" => "lb",
                    "dimUnit" => 'in',
                    "currency" => "USD",
                    "customsCurrency" => "USD",
                    "pieces" => [
                        [
                            "weight" =>  $sweight ,
                            "length" => null,
                            "width" => null,
                            "height" => null,
                            "insuranceAmount" => null,
                            "declaredValue" => null
                        ]
                    ],
                    "billing" => [
                        "party" => "sender"
                    ]
                ];
                
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key
                ])->post($endpoint, $data);
                
                if ($response) {
                    $array_data = $response->json();
                  return $array_data;
                } else {
                    
                    return  $response;
                }
                
            
            }






}