@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')

<!--{!! view_render_event('bagisto.shop.products.review.before', ['product' => $product]) !!}-->

<!--@if ($total = $reviewHelper->getTotalReviews($product))-->
    <div class="product-ratings">
        <ul class="list-unstyled">
            @for ($i = 1; $i <= round($reviewHelper->getAverageRating($product)); $i++)
                <li><i class="fa fa-star"></i></li>
            @endfor
        </ul>

        <div class="total-reviews">
            {{ 
                __('shop::app.products.total-rating', [
                        'total_rating' => $reviewHelper->getTotalRating($product),
                        'total_reviews' => $total,
                    ]) 
            }}
        </div>
    </div>
<!--@endif-->

<!--{!! view_render_event('bagisto.shop.products.review.after', ['product' => $product]) !!}-->