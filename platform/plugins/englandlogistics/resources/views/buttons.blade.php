@if (app(Botble\Englandlogistics\Englandlogistics::class)->canCreateTransaction($shipment))
    @php
        $url = route('ecommerce.shipments.englandlogistics.show', $shipment->id);
        if (!is_in_admin(true) && is_plugin_active('marketplace')) {
            $url = route('marketplace.vendor.orders.englandlogistics.show', $shipment->id);
        }
    @endphp
    <button
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#englandlogistics-view-n-create-transaction"
        data-url="{{ $url }}"
        type="button"
    >
        <img
            src="{{ url('vendor/core/plugins/englandlogistics/images/icon.png') }}"
            alt="englandlogistics"
            height="16"
        >
        <span>{{ trans('plugins/englandlogistics::englandlogistics.transaction.view_and_create') }}</span>
    </button>

    <div
        class="modal fade"
        id="englandlogistics-view-n-create-transaction"
        aria-labelledby="englandlogistics-view-n-create-transaction-label"
        aria-hidden="true"
        tabindex="-1"
    >
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5
                        class="modal-title"
                        id="englandlogistics-view-n-create-transaction-label"
                    >{{ trans('plugins/englandlogistics::englandlogistics.transaction.view_and_create') }}</h5>
                    <button
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endif

@if ($shipment->label_url)
    <a
        class="btn btn-success"
        href="{{ $shipment->label_url }}"
        target="_blank"
        rel="noopener noreferrer"
    >
        <i class="fa fa-print"></i>
        <span>{{ trans('plugins/englandlogistics::englandlogistics.print_label') }}</span>
    </a>
@endif
