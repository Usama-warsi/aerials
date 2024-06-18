<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Enums\DiscountTypeEnum;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Http\Requests\CartRequest;
use Botble\Ecommerce\Http\Requests\UpdateCartRequest;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Botble\Ecommerce\Models\Address;

use Throwable;

class PublicCartController extends BaseController
{
    public function __construct(
        protected HandleApplyPromotionsService $applyPromotionsService,
        protected HandleApplyCouponService $handleApplyCouponService
    ) {
    }

    public function index()
    {
        $promotionDiscountAmount = 0;
        $couponDiscountAmount = 0;
        $shippingfee = 0;

        $products = collect();
        $crossSellProducts = collect();

     
       
    



   

        // echo '<pre>'; print_r($r); exit();

        if (Cart::instance('cart')->isNotEmpty()) {
            [$products, $promotionDiscountAmount, $couponDiscountAmount] = $this->getCartData();

            $crossSellProducts = get_cart_cross_sale_products(
                $products->pluck('original_product.id')->all(),
                (int)theme_option('number_of_cross_sale_product', 4)
            ) ?: collect();
        }

        SeoHelper::setTitle(__('Shopping Cart'));

        Theme::breadcrumb()->add(__('Shopping Cart'), route('public.cart'));

        return Theme::scope(
            'ecommerce.cart',
            compact('promotionDiscountAmount', 'couponDiscountAmount','shippingfee', 'products', 'crossSellProducts'),
            'plugins/ecommerce::themes.cart'
        )->render();
    }

    public function store(CartRequest $request)
    {
        $response = $this->httpResponse();

        $product = Product::query()->find($request->input('id'));

        if (! $product) {
            return $response
                ->setError()
                ->setMessage(__('This product is out of stock or not exists!'));
        }

        if ($product->variations->count() > 0 && ! $product->is_variation) {
            $product = $product->defaultVariation->product;
        }

        if ($product->isOutOfStock()) {
            return $response
                ->setError()
                ->setMessage(
                    __(
                        'Product :product is out of stock!',
                        ['product' => $product->original_product->name ?: $product->name]
                    )
                );
        }

        $maxQuantity = $product->quantity;

        if (! $product->canAddToCart($request->input('qty', 1))) {
            return $response
                ->setError()
                ->setMessage(__('Maximum quantity is :max!', ['max' => $maxQuantity]));
        }

        $product->quantity -= $request->input('qty', 1);

        $outOfQuantity = false;
        foreach (Cart::instance('cart')->content() as $item) {
            if ($item->id == $product->id) {
                $originalQuantity = $product->quantity;
                $product->quantity = (int)$product->quantity - $item->qty;

                if ($product->quantity < 0) {
                    $product->quantity = 0;
                }

                if ($product->isOutOfStock()) {
                    $outOfQuantity = true;

                    break;
                }

                $product->quantity = $originalQuantity;
            }
        }
        
        if($product->product_type == "gift"){
            $id= 'Product_'.$request->input('id');
            Session::forget($id);           
            Session::put($id,$request->input());            
        }

        if (
            EcommerceHelper::isEnabledProductOptions() &&
            $product->original_product->options()->where('required', true)->exists()
        ) {
            if (! $request->input('options')) {
                return $response
                    ->setError()
                    ->setData(['next_url' => $product->original_product->url])
                    ->setMessage(__('Please select product options!'));
            }

            $requiredOptions = $product->original_product->options()->where('required', true)->get();

            $message = null;

            foreach ($requiredOptions as $requiredOption) {
                if (! $request->input('options.' . $requiredOption->id . '.values')) {
                    $message .= trans(
                        'plugins/ecommerce::product-option.add_to_cart_value_required',
                        ['value' => $requiredOption->name]
                    );
                }
            }

            if ($message) {
                return $response
                    ->setError()
                    ->setMessage(__('Please select product options!'));
            }
        }

        if ($outOfQuantity) {
            return $response
                ->setError()
                ->setMessage(
                    __(
                        'Product :product is out of stock!',
                        ['product' => $product->original_product->name ?: $product->name]
                    )
                );
        }

        $cartItems = OrderHelper::handleAddCart($product, $request);

        $response
            ->setMessage(
                __(
                    'Added product :product to cart successfully!',
                    ['product' => $product->original_product->name ?: $product->name]
                )
            );

        $token = OrderHelper::getOrderSessionToken();

        $nextUrl = route('public.checkout.information', $token);

        if (EcommerceHelper::getQuickBuyButtonTarget() == 'cart') {
            $nextUrl = route('public.cart');
        }

        if ($request->input('checkout')) {
            $response->setData(['next_url' => $nextUrl]);

            if ($request->ajax() && $request->wantsJson()) {
                return $response;
            }

            return $response->setNextUrl($nextUrl);
        }

        return $response
            ->setData([
                ...$this->getDataForResponse(),
                'status' => true,
                'content' => $cartItems,
            ]);
    }

    public function update(UpdateCartRequest $request)
    {
        
        
        // if($item['values']['qty'] <= 0){
        //     return $this
        //     ->httpResponse()
        //     ->setError()
        //     ->setData($this->getDataForResponse())
        //     ->setMessage(__('Product Quantity Cannot be less than 0'));
        //   }
        $fweight= 0;
        if ($request->has('checkout')) {
            $token = OrderHelper::getOrderSessionToken();

            return $this
                ->httpResponse()
                ->setNextUrl(route('public.checkout.information', $token));
        }

        $data = $request->input('items', []);

        $outOfQuantity = false;
        foreach ($data as $item) {

            $cartItem = Cart::instance('cart')->get($item['rowId']);

            if (! $cartItem) {
                continue;
            }

            $product = Product::query()->find($cartItem->id);

            if ($product) {
                $originalQuantity = $product->quantity;
                $product->quantity = (int)$product->quantity - (int)Arr::get($item, 'values.qty', 0) + 1;

                if ($product->quantity < 0) {
                    $product->quantity = 0;
                }

                if ($product->isOutOfStock()) {
                    $outOfQuantity = true;
                } else {
                    Cart::instance('cart')->update($item['rowId'], Arr::get($item, 'values'));
                }

                $product->quantity = $originalQuantity;
                $fweight += $product->weight * $cartItem->qty;
              
            }
        }

        if ($outOfQuantity) {
            return $this
                ->httpResponse()
                ->setError()
                ->setData($this->getDataForResponse())
                ->setMessage(__('One or all products are not enough quantity so cannot update!'));
        }
        Session::put('shipammount', 00);
        Session::put('shipweight', $fweight);

        if(session('shipcarrier')){

            if(session('shipcountry') == "US"){
              
                if(session('shipcarrier') == "fedex"){
                    $s =$this->shipup('fedex',"","",session('shipcity'),session('shipcountry'),session('shipzip'),strval(session('shipweight')));
                    Session::put('aaa', $s['quotes']);
                    if(!empty($s['quotes'])){
                        foreach($s as $d){
                            foreach($d as $g){
                                if($g['serviceCode'] == "fedex_ground"){
                                    Session::put('shipammount', $g['totalAmount']);
                                }
                               
                            }
                        }
                    }
                }
                if(session('shipcarrier') == "usps"){
                    $s =$this->shipup('usps',"","",session('shipcity'),session('shipcountry'),session('shipzip'),strval(session('shipweight')));
                    Session::put('aaa', $s['quotes']);
                    if(!empty($s['quotes'])){
                        foreach($s as $d){
                            foreach($d as $g){
                                if($g['serviceCode'] == "usps_priority"){
                                    Session::put('shipammount', $g['totalAmount']);
                                }
                               
                            }
                        }
                    }
                }
   
            }
            else{

                if(session('shipcarrier') == "fedex"){
                    $s =$this->shipup('fedex',"","",session('shipcity'),session('shipcountry'),session('shipzip'),strval(session('shipweight')));
                    Session::put('aaa', $s['quotes']);
                    if(!empty($s['quotes'])){
                        foreach($s as $d){
                            foreach($d as $g){
                                if($g['serviceCode'] == "fedex_international_economy"){
                                    Session::put('shipammount', $g['totalAmount']);
                                }
                               
                            }
                        }
                    }
                }
                if(session('shipcarrier') == "usps"){
                    $s =$this->shipup('usps',"","",session('shipcity'),session('shipcountry'),session('shipzip'),strval(session('shipweight')));
                    Session::put('aaa', $s['quotes']);
                    if(!empty($s['quotes'])){
                        foreach($s as $d){
                            foreach($d as $g){
                                if($g['serviceCode'] == "usps_international_express"){
                                    Session::put('shipammount', $g['totalAmount']);
                                }
                               
                            }
                        }
                    }
                }


            }
          

        }else{
            Session::put('shipammount', 00);
        };
        return $this
            ->httpResponse()
            ->setData($this->getDataForResponse())
            ->setMessage(__('Update cart successfully!'));
    }

    public function destroy(string $id)
    {
        try {
            Cart::instance('cart')->remove($id);
        } catch (Throwable) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Cart item is not existed!'));
        }
        Session::forget('shipammount');
        
        Session::forget('shipmethod');
      
        Session::forget('weightpro');
      
        Session::forget('shipmethod_type');
     
        Session::forget('shipcarrier');
      
        Session::forget('shippackageTypeCode');
       
        Session::forget('shipzip');
      
        Session::forget('shipcity');
 
        Session::forget('shipcountry');
      
        Session::forget('shipstate');
        return $this
            ->httpResponse()
            ->setData($this->getDataForResponse())
            ->setMessage(__('Removed item from cart successfully!'));
    }

    public function empty()
    {
        Cart::instance('cart')->destroy();

      
        Session::forget('shipammount');
        
        Session::forget('shipmethod');
      
        Session::forget('weightpro');
      
        Session::forget('shipmethod_type');
     
        Session::forget('shipcarrier');
      
        Session::forget('shippackageTypeCode');
       
        Session::forget('shipzip');
      
        Session::forget('shipcity');
 
        Session::forget('shipcountry');
      
        Session::forget('shipstate');
        return $this
            ->httpResponse()
            ->setData(Cart::instance('cart')->content())
            ->setMessage(__('Empty cart successfully!'));
    }

    protected function getCartData(): array
    {
        $products = Cart::instance('cart')->products();

        $promotionDiscountAmount = $this->applyPromotionsService->execute();

        $couponDiscountAmount = 0;

        if ($couponCode = session('auto_apply_coupon_code')) {
            $coupon = Discount::query()
                ->where('code', $couponCode)
                ->where('apply_via_url', true)
                ->where('type', DiscountTypeEnum::COUPON)
                ->exists();

            if ($coupon) {
                $couponData = $this->handleApplyCouponService->execute($couponCode);

                if (! Arr::get($couponData, 'error')) {
                    $couponDiscountAmount = Arr::get($couponData, 'data.discount_amount');
                }
            }
        }

        $sessionData = OrderHelper::getOrderSessionData();

        if (session()->has('applied_coupon_code')) {
            $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount', 0);
        }

        return [$products, $promotionDiscountAmount, $couponDiscountAmount];
    }

    protected function getDataForResponse(): array
    {
        return apply_filters('ecommerce_cart_data_for_response', [
            'count' => Cart::instance('cart')->count(),
            'total_price' => format_price(Cart::instance('cart')->rawSubTotal()),
            'content' => Cart::instance('cart')->content(),
        ], $this->getCartData());
    }

    protected function getDataForResponses($a): array
    {
        return apply_filters('ecommerce_cart_data_for_response', [
            'count' => Cart::instance('cart')->count(),
            'total_price' => format_price(Cart::instance('cart')->rawSubTotal()+$a),
            'content' => Cart::instance('cart')->content(),
        ], $this->getCartData());
    }

function uptamount(Request $re){
    // $s = $this->getDataForResponse();
    $a = $re->input('price');
    Session::put('shipammount', $a);
     $b = $re->input('method');
    Session::put('shipmethod', $b);
    $w = $re->input('weight');
    Session::put('weightpro', $w);
    $b = $re->input('method_type');
    Session::put('shipmethod_type', $b);
    $b = $re->input('carrier');
    Session::put('shipcarrier', $b);
    $b = $re->input('packageTypeCode');
    Session::put('shippackageTypeCode', $b);
    $b = $re->input('zip');
    Session::put('shipzip', $b);
    $b = $re->input('city');
    Session::put('shipcity', $b);
    $b = $re->input('country');
    Session::put('shipcountry', $b);
    $b = $re->input('state');
    Session::put('shipstate', $b);

    $token = session('tracked_start_checkout');
    $sessionCheckoutData= OrderHelper::getOrderSessionData($token);
    $addressData = [
        'country' => $re->input('country'),
        'state' => $re->input('state'),
        'city' => $re->input('city'),
        'zip_code' => $re->input('zip'),
    ];
    $sessionCheckoutData = $this->processOrderData($token, $sessionCheckoutData, $addressData);
  return $this
    ->httpResponse()
    ->setData($this->getDataForResponse())
    ->setMessage(__('shipping add successfully!'));

}

    function getshipptingquote(Request $re )
    {

        $w = number_format($re->input('weight'), 2); 

        if($re->input('country') == "US"){
            $f = [];
            $s =$this->shipup('fedex',"","",$re->input('city'),$re->input('country'),$re->input('zip'),$w);
            foreach($s as $d){
                foreach($d as $g){
                
                            if($g['serviceCode'] == "fedex_ground"){
                                array_push($f,$g);
                            }
                }
            }
          
            $s =$this->shipup('usps',"","",$re->input('city'),$re->input('country'),$re->input('zip'),$w);
          
            foreach($s as $d){
                foreach($d as $g){
                
                            if($g['serviceCode'] == "usps_priority"){
                                array_push($f,$g);
                            }
                }
            }
            return $f;
        }
        else{
            $f = [];
            $s =$this->shipup('fedex',"","",$re->input('city'),$re->input('country'),$re->input('zip'),$w);
            foreach($s as $d){
                foreach($d as $g){
                
                            if($g['serviceCode'] == "fedex_international_economy"){
                                array_push($f,$g);
                            }
                }
            }

          
            $s =$this->shipup('usps',"","",$re->input('city'),$re->input('country'),$re->input('zip'),$w);
            foreach($s as $d){
                foreach($d as $g){
                
                            if($g['serviceCode'] == "usps_international_express"){
                                array_push($f,$g);
                            }
                }
            }

            return $f;
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

    protected function processOrderData(
        string $token,
        array $sessionData,
        array $addressFromInputs,
        bool $finished = false
    ): array {
       
       
       if (auth('customer')->check() && ! Arr::get($sessionData, 'address_id')) {
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
        } elseif ($addressFromInput = (array) $addressFromInputs) {
            $addressData = $addressFromInput;
        }

        $addressData = OrderHelper::cleanData($addressData);

        $sessionData = array_merge($sessionData, $addressData);

        Cart::instance('cart')->refresh();

        $products = Cart::instance('cart')->products();


            OrderHelper::setOrderSessionData($token, $sessionData);

            return $sessionData;
        }

        
    
}
