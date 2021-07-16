@inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

@extends('shop::layouts.master')

@section('page_title')
    {{ __('shop::app.customer.account.review.index.page-title') }}
@endsection

@section('content-wrapper')
<div class="container">
    <div class="account-content">
        @include('shop::customers.account.partials.sidemenu')

        <div class="account-layout">

            <div class="account-head">
                <span class="back-icon"><a href="{{ route('customer.account.index') }}"><i class="icon icon-menu-back"></i></a></span>

                <span class="account-heading">{{ __('shop::app.customer.account.review.index.title') }}</span>
                
                @if (count($reviews) > 1)
                    <div class="account-action">
                        <a class="delete-review-btn" data-url="{{ route('customer.review.deleteall') }}" href="javascript:void(0)">{{ __('shop::app.customer.account.wishlist.deleteall') }}</a>
                    </div>
                @endif

                <span></span>
                <div class="horizontal-rule"></div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.reviews.list.before', ['reviews' => $reviews]) !!}

            <div class="account-items-list">
                @if (! $reviews->isEmpty())
                    @foreach ($reviews as $review)
                        <div class="account-item-card mt-15 mb-15">
                            <div class="media-info">
                                <?php $image = $productImageHelper->getProductBaseImage($review->product); ?>

                                <a href="{{ url()->to('/').'/products/'.$review->product->url_key }}" title="{{ $review->product->name }}">
                                    <img class="media" src="{{ $image['small_image_url'] }}"/>
                                </a>

                                <div class="info">
                                    <div class="product-name">
                                        <a href="{{ url()->to('/').'/products/'.$review->product->url_key }}" title="{{ $review->product->name }}">
                                            {{$review->product->name}}
                                        </a>
                                    </div>

                                    <div class="stars mt-10">
                                        @for($i=0 ; $i < $review->rating ; $i++)
                                            <span class="icon star-icon"></span>
                                        @endfor
                                    </div>

                                    <div class="mt-10">
                                        {{ $review->comment }}
                                    </div>
                                </div>
                            </div>

                            <div class="operations">
                                <a class="mb-50 delete-review-btn" data-url="{{ route('customer.review.delete', $review->id) }}" href="javascript:void(0)"><span class="icon trash-icon"></span></a>
                            </div>
                        </div>
                        <div class="horizontal-rule mb-10 mt-10"></div>
                    @endforeach

                    <div class="bottom-toolbar">
                        {{ $reviews->links()  }}
                    </div>
                @else
                    <div class="empty mt-15">
                        {{ __('customer::app.reviews.empty') }}
                    </div>
                @endif

            </div>

            {!! view_render_event('bagisto.shop.customers.account.reviews.list.after', ['reviews' => $reviews]) !!}
        </div>
    </div>

    <div class="modal" id="myModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Warning</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body delete-model-msg">
                Are you sure to delete this review?
            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-default yes-remove" url-attr="">Yes</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
            </div>
            
          </div>
        </div>
      </div>

</div>
@endsection

@push('scripts')
    <script>
        $("body").on('click','.delete-review-btn',function(e) 
        {
            if($(this).attr('data-type') == 'all')
            {
                $('.delete-model-msg').text('Are you sure to delete these reviews?');
            }
            else
            {
                $('.delete-model-msg').text('Are you sure to delete this review?');
            }
            $('.yes-remove').attr("url-attr",$(this).attr("data-url"));
	        $('#myModal').modal('show');
        })

        $("body").on('click','.yes-remove',function(e) {
            $('#myModal').modal('hide');
            window.location.href = $('.yes-remove').attr("url-attr");
        });
    </script>
@endpush