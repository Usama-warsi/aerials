@php
    $status = setting('shipping_englandlogistics_status', 0);
    $testKey = setting('shipping_englandlogistics_api_key') ?: '';
    $prodKey = setting('shipping_englandlogistics_customer_id') ?: '';
    $test = setting('shipping_englandlogistics_sandbox', 1) ?: 0;
    $logging = setting('shipping_englandlogistics_logging', 1) ?: 0;
    $cacheResponse = setting('shipping_englandlogistics_cache_response', 1) ?: 0;
    $webhook = setting('shipping_englandlogistics_webhooks', 1) ?: 0;
@endphp

<x-core::card>
    <x-core::table :striped="false" :hover="false">
        <x-core::table.body>
            <x-core::table.body.cell class="border-end" style="width: 5%">
                <x-core::icon name="ti ti-truck-delivery" />
            </x-core::table.body.cell>
            <x-core::table.body.cell style="width: 20%">
                <img
                    class="filter-black"
                    src="{{ url('vendor/core/plugins/englandlogistics/screenshot.png') }}"
                    alt="englandlogistics"
                >
            </x-core::table.body.cell>
            <x-core::table.body.cell>
                <a href="https://englandship.rocksolidinternet.com/" target="_blank" class="fw-semibold">Englandlogistics</a>
                <p class="mb-0">{{ trans('plugins/englandlogistics::englandlogistics.description') }}</p>
            </x-core::table.body.cell>
            <x-core::table.body.row class="">
                <x-core::table.body.cell colspan="3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div @class(['payment-name-label-group', 'd-none' => ! $status])>
                                <span class="payment-note v-a-t">{{ trans('plugins/payment::payment.use') }}:</span>
                                <label class="ws-nm inline-display method-name-label">Englandlogistics</label>
                            </div>
                        </div>

                        <x-core::button
                            data-bs-toggle="collapse"
                            href="#collapse-shipping-method-englandlogistics"
                            aria-expanded="false"
                            aria-controls="collapse-shipping-method-englandlogistics"
                        >
                            @if ($status)
                                {{ trans('core/base::layouts.settings') }}
                            @else
                                {{ trans('core/base::forms.edit') }}
                            @endif
                        </x-core::button>
                    </div>
                </x-core::table.body.cell>
            </x-core::table.body.row>
            <x-core::table.body.row class="collapse" id="collapse-shipping-method-englandlogistics">
                <x-core::table.body.cell class="border-left" colspan="3">
                    <x-core::form :url="route('ecommerce.shipments.englandlogistics.settings.update')">
                        <div class="row">
                         
                            <div class="col-sm-12">
                         
                           <input type="hidden" value="England Logistics" name="shipping_englandlogistics_name">

                                <x-core::form.text-input
                                    name="shipping_englandlogistics_api_key"
                                    :label="trans('plugins/englandlogistics::englandlogistics.api_key')"
                                    placeholder="<API-KEY>"
                                    :disabled="BaseHelper::hasDemoModeEnabled()"
                                    :value="BaseHelper::hasDemoModeEnabled() ? Str::mask($testKey, '*', 10) : $testKey"
                                />

                                <x-core::form.text-input
                                    name="shipping_englandlogistics_customer_id"
                                    :label="trans('plugins/englandlogistics::englandlogistics.customer_id')"
                                    placeholder="Customer ID"
                                    :disabled="BaseHelper::hasDemoModeEnabled()"
                                    :value="BaseHelper::hasDemoModeEnabled() ? Str::mask($prodKey, '*', 10) : $prodKey"
                                />
                              

                                <x-core::form-group>
                                    <x-core::form.toggle
                                        name="shipping_englandlogistics_status"
                                        :checked="$status"
                                        :label="trans('plugins/englandlogistics::englandlogistics.activate')"
                                    />
                                </x-core::form-group>

                                <x-core::form-group>
                                    <x-core::form.toggle
                                        name="shipping_englandlogistics_logging"
                                        :checked="$logging"
                                        :label="trans('plugins/englandlogistics::englandlogistics.logging')"
                                    />
                                </x-core::form-group>

                                <x-core::form-group>
                                    <x-core::form.toggle
                                        name="shipping_englandlogistics_cache_response"
                                        :checked="$cacheResponse"
                                        :label="trans('plugins/englandlogistics::englandlogistics.cache_response')"
                                    />
                                </x-core::form-group>
                        
                              

                                @if (count($logFiles))
                                    <div class="form-group mb-3">
                                        <p class="mb-0">{{ __('Log files') }}: </p>
                                        <ul>
                                            @foreach ($logFiles as $logFile)
                                                <li><a
                                                        href="{{ route('ecommerce.shipments.englandlogistics.view-log', $logFile) }}"
                                                        target="_blank"
                                                    ><strong>- {{ $logFile }} <i
                                                                class="fa fa-external-link"></i></strong></a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                           
                                @env('demo')
                                    <x-core::alert type="danger">
                                        {{ trans('plugins/englandlogistics::englandlogistics.disabled_in_demo_mode') }}
                                    </x-core::alert>
                                @else
                                    <div class="text-end">
                                        <x-core::button type="submit" color="primary">
                                            {{ trans('core/base::forms.update') }}
                                        </x-core::button>
                                    </div>
                                @endenv
                            </div>
                        </div>
                    </x-core::form>
                </x-core::table.body.cell>
            </x-core::table.body.row>
        </x-core::table.body>
    </x-core::table>
</x-core::card>
