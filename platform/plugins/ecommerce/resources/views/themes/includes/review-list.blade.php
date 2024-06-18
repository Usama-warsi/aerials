@foreach ($reviews as $review)
    @continue(! $review->is_approved && auth('customer')->id() != $review->customer_id)

    <div @class(['row pb-3 mb-3 review-item', 'border-bottom' => ! $loop->last, 'opacity-50' => ! $review->is_approved])>
        <div class="col-auto d-none">
            <img class="rounded-circle" src="{{ $review->customer_avatar_url }}" alt="{{ $review->user->name ?: $review->customer_name }}" width="60">
        </div>
        <div class="col">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2 review-item__header">
                <div class="fw-medium">
                    @if(!empty($review->customer_id))
                        {{ $review->user->customer_name }}
                    @else
                        {{ $review->customer_name }}
                    @endif 
                </div>
                <span class="text-muted small ml-2 timey-time">{{ $review->created_at }}</span>
                @if ($review->order_created_at)
                    <div class="text-muted small ml-2">{{ __('✅ Purchased :time', ['time' => $review->order_created_at->diffForHumans()]) }}</div>
                @endif
                @if (! $review->is_approved)
                    <div class="small text-warning">{{ __('Waiting for approval') }}</div>
                @endif
            </div>

            <div class="mb-2 review-item__rating">
                @include(EcommerceHelper::viewPath('includes.rating-star'), ['avg' => $review->star, 'size' => 80])
            </div>

            <div class="review-item__body p-3 rounded bg-light">
                {{ $review->comment }}
            </div>

            @if ($review->images)
                <div class="review-item__images mt-3">
                    <div class="row g-1 review-images">
                        @foreach ($review->images as $image)
                            <a href="{{ RvMedia::getImageUrl($image) }}" class="col-3 col-md-2 col-xl-1 position-relative">
                                <img src="{{ RvMedia::getImageUrl($image, 'thumb') }}" alt="{{ $review->comment }}" class="img-thumbnail">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        @if ($review->reply)
            <div class="review-item__reply mt-4" style="margin-left: 30%;">
                <div class="position-relative row py-3 rounded" style="background: #B0E0E6;">
                    <div class="col-auto d-none">
                        <img class="rounded-circle" src="{{ $review->reply->user->avatar_url }}" alt="{{ $review->reply->user->customer_name }}" width="50">
                    </div>
                    <div class="col">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2 review-item__header">
                            <div class="fw-medium">
                                {{ $review->reply->user->customer_name }}
                            </div>
                            <span class="badge bg-primary">
                                {{ __('Admin') }}
                            </span>
                            <span class="text-muted small ml-2 timey-time">{{ $review->reply->created_at }}</span>
                        </div>

                        <div class="review-item__body">
                            {{ $review->reply->message }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endforeach

{{ $reviews->links() }}
