@extends('shop::layouts.master')

@section('page_title')
    {{ __('shop::app.customer.account.wishlist.page-title') }}
@endsection

@section('content-wrapper')
<div class="container">
    <div class="account-content">
        @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

        @include('shop::customers.account.partials.sidemenu')

        @inject ('reviewHelper', 'Webkul\Product\Helpers\Review')

        <div class="account-layout">

            <div class="account-head mb-15">
                <span class="account-heading">{{ __('shop::app.customer.account.wishlist.title') }}</span>

                @if (count($items))
                    <div class="account-action del-all-d" style="display:none;">
                        <a class="wishlist-d removeall-btn" data-type="all" data-url="{{ route('customer.wishlist.removeall') }}" href="javascript:void()">{{ __('shop::app.customer.account.wishlist.deleteall') }}</a>
                    </div>
                @endif

                <div class="horizontal-rule"></div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.wishlist.list.before', ['wishlist' => $items]) !!}

            <div class="account-items-list">
                
                @if ($items->count())
                    @php
                        $u=0;
                    @endphp
                    @foreach ($items as $key => $item)
                    @php
                        $productStatus = app('Webkul\Customer\Repositories\WishlistRepository')->checkActiveProductWhishlist($item->product->id);
                        //print_r($productStatus);
                        @endphp

                        @if(!empty($productStatus))
                        @php 
                            $u++;
                        @endphp 
                            <div class="account-item-card mt-15 mb-15 wishlist-style wishlist-{{$key}}">
                                <div class="media-info">
                                    <div class="img-holder">
                                    @php
                                        $image = $item->product->getTypeInstance()->getBaseImage($item);
                                    @endphp

                                    <img class="media" src="{{ $image['small_image_url'] }}" />
                                    </div>

                                    <div class="info">
                                        <div class="product-name">
                                            {{ $item->product->name }}
                                            <span class="short-desc-wishlist">
                                               {!! $item->product->short_description !!}
                                            </span>

                                            @if (isset($item->additional['attributes']))
                                                <div class="item-options">

                                                    @foreach ($item->additional['attributes'] as $attribute)
                                                        <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                                    @endforeach

                                                </div>
                                            @endif
                                        </div>

                                        <span class="stars" style="display: inline">
                                            @for ($i = 1; $i <= $reviewHelper->getAverageRating($item->product); $i++)
                                                <span class="icon star-icon"></span>
                                            @endfor
                                        </span>
                                    </div>
                                </div>

                                <div class="operations">
                                    <!-- <a class="mb-50" href="{{ route('customer.wishlist.remove', $item->id) }}">
                                        <span class="icon trash-icon"></span>
                                    </a> -->
                                    <a data-type="single" class="mb-50 wishlist-d" href="javascript:void(0)" data-url="{{ route('customer.wishlist.remove', $item->id) }}">
                                        <span class="icon trash-icon wishlist-d" data-type="single" data-url="{{ route('customer.wishlist.remove', $item->id) }}"></span>
                                    </a>
                                    @inject ('configurableOptionHelper', 'Webkul\Product\Helpers\ConfigurableOption')
                                    @php $hasVarient = $configurableOptionHelper->checkProductHasVarient($item->product_id);@endphp
                                    @if($hasVarient)
                                        <a href="{{ route('shop.products.index', $hasVarient) }}" class="btn btn-primary btn-md">
                                            {{ __('shop::app.customer.account.wishlist.move-to-cart') }}
                                        </a>
                                    @else 
                                    <a href="javascript:void(0)" parent-class="wishlist-{{$key}}" data-url="{{ route('customer.wishlist.move', $item->id) }}" class="btn btn-primary btn-md move-to-cart">
                                            {{ __('shop::app.customer.account.wishlist.move-to-cart') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="horizontal-rule mb-10 mt-10"></div>
                        @endif
                    @endforeach

                    @if($u < 1)
                        <div class="empty">
                            {{ __('customer::app.wishlist.empty') }}
                        </div>
                    @endif

                    <div class="bottom-toolbar">
                        {{ $items->links()  }}
                    </div>
                @else
                    <div class="empty">
                        {{ __('customer::app.wishlist.empty') }}
                    </div>
                @endif
            </div>

            {!! view_render_event('bagisto.shop.customers.account.wishlist.list.after', ['wishlist' => $items]) !!}

        </div>
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
         Are you sure you want to remove?
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default yes-remove" url-attr="">Yes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
        </div>
        
      </div>
    </div>
  </div>

  <div class="modal" id="myModalForMove">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Warning</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body move-model-msg">
            Do you really want to do this ?
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default yes-move" url-attr="">Yes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
        </div>
        
      </div>
    </div>
  </div>
  
</div>
@endsection


@push('scripts')
<script>

 $("body").on('click','.wishlist-d',function(e) {
    if($(this).attr('data-type') == 'all')
    {
        $('.delete-model-msg').text('Are you sure you want to remove all?'); 
    }
    else
    {
        $('.delete-model-msg').text('Are you sure you want to remove?'); 
    }
    $('.yes-remove').attr("url-attr",$(this).attr("data-url"));
	$('#myModal').modal('show');
 });

 $("body").on('click','.yes-remove',function(e) {
	$('#myModal').modal('hide');
	window.location.href = $('.yes-remove').attr("url-attr");
 });
 
 $("body").on('click','.move-to-cart',function(e) 
 {
    $('.yes-move').attr("url-attr",$(this).attr("data-url"));
    $('.yes-move').attr("wish-btn-scope",$(this).attr("parent-class"));
	$('#myModalForMove').modal('show');
 });

$("body").on('click','.yes-move',function(e) 
{
    $('#myModalForMove').modal('hide');
    var _this = $(this);
    var url = _this.attr("url-attr");
    var row_class = $(this).attr("wish-btn-scope");

    $('#moveTowishlistModal').modal('hide');
    $.ajax({
        url: url,
        data: {},
        type: "GET",
        success: function (response) 
        {
            if(response.status == 'success')
            {
                $(".alert-wrapper").html("<div class='alert alert-success'><p>" + response.msg +"</p></div>");
                $('.'+row_class).remove();
                var count = $(".account-items-list .account-item-card").length;
    
                if(count == 0)
                {
                    $("<div class='empty'>You Don't Have Any Items In Your Wishlist</div>").prependTo(".account-items-list");
                    $('.removeall-btn').remove();
                }
            }
            if(response.status == 'failed')
            {
                $(".alert-wrapper").html("<div class='alert alert-error'><p>" + response.msg +"</p></div>");
                window.location.href = response.url;
            }
            
            setTimeout(function() {
                $(".alert-wrapper").fadeOut('fast');
            }, 1000);
        }
    })
});

setTimeout(() => {
    if($('.account-item-card').length > 0) {
        $('.del-all-d').show();
    }
}, 1000);
</script>
@endpush
