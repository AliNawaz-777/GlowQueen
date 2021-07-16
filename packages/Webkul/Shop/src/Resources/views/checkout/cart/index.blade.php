@extends('shop::layouts.master')







@section('page_title')



    {{ __('shop::app.checkout.cart.title') }}



@stop







@section('content-wrapper')



<div class="container">



    @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')



    <section class="cart shopping_cart">



        @if ($cart)



            <div class="title">



                {{ __('shop::app.checkout.cart.title') }}



            </div>







            <div class="cart-content">



                <div class="left-side">



                    <form action="{{ route('shop.checkout.cart.update') }}" method="POST" @submit.prevent="onSubmit">







                        <div class="cart-item-list" style="margin-top: 0">



                             <div class="item shopping_cart_tbl_wrap">



                                    <table class="theme-bordered-tbl product_tbl">



                                        <tbody>



                                            <tr>



                                                <td>Product</td>



                                                <td>Product Name</td>



                                                <td>Quantity</td>



                                                <td>Price</td>



                                                <td>Action</td>



                                            </tr>



                            @csrf



                            @foreach ($cart->items as $key => $item)



                                @php



                                    $productBaseImage = $item->product->getTypeInstance()->getBaseImage($item);



                                @endphp



                               



                                            <tr>



                                                <td>



                                                    <div class="item-image" style="margin-right: 15px;">



                                                        <a href="{{ url()->to('/').'/products/'.$item->product->url_key }}"><img src="{{ $productBaseImage['medium_image_url'] }}" /></a>



                                                    </div>



                                                </td>



                                                <td>



                                                    {!! view_render_event('bagisto.shop.checkout.cart.item.name.before', ['item' => $item]) !!}







                                                    <div class="item-title">



                                                        <a href="{{ url()->to('/').'/products/'.$item->product->url_key }}">



                                                            {{ $item->product->name }}



                                                        </a>



                                                    </div>







                                                    {!! view_render_event('bagisto.shop.checkout.cart.item.name.after', ['item' => $item]) !!}



                                                </td>



                                                <td>



                                                    {!! view_render_event('bagisto.shop.checkout.cart.item.quantity.before', ['item' => $item]) !!}







                                                    <div class="misc">



                                                        <quantity-changer



                                                            :control-name="'qty[{{$item->id}}]'"



                                                            quantity="{{$item->quantity}}">



                                                        </quantity-changer>



                                                    </div>







                                                    {!! view_render_event('bagisto.shop.checkout.cart.item.quantity.after', ['item' => $item]) !!}



                                                </td>



                                                <td>



                                                    {!! view_render_event('bagisto.shop.checkout.cart.item.price.before', ['item' => $item]) !!}







                                                    <div class="price">



                                                        {{ core()->currency($item->base_price) }}



                                                    </div>







                                                    {!! view_render_event('bagisto.shop.checkout.cart.item.price.after', ['item' => $item]) !!}



                                                </td>



                                            <td class="wishlistRow-{{$key}}">



                                                    <span class="remove">



                                                        <a class="delete-review-btn" data-url="{{ route('shop.checkout.cart.remove', $item->id) }}" href="javascript:void(0)">
                                                            <!-- <span class="icon trash-icon"></span> -->
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </a></span>



                                                    @auth('customer')



                                                        <span class="towishlist">



                                                            @if ($item->parent_id != 'null' || $item->parent_id != null)

 

                                                                <a class="add-to-wishlist swap-on-hover" parent-class="wishlistRow-{{$key}}" data-url="{{ route('shop.movetowishlist', $item->id) }}" href="javascript:void(0)">
                                                                    <!-- <span class="icon wishlist-icon"></span> -->
                                                                    <i class="fa fa-heart-o icon-before-hover" aria-hidden="true"></i>
                                                                    <i class="fa fa-heart icon-hover"  aria-hidden="true"></i>
                                                                </a>



                                                            @else



                                                                <a class="add-to-wishlist" parent-class="wishlistRow-{{$key}}" data-url="{{ route('shop.movetowishlist', $item->child->id) }}" href="javascript:void(0)">



                                                                {{ __('shop::app.checkout.cart.move-to-wishlist-success') }}</a>



                                                            @endif



                                                        </span>



                                                    @endauth



                                                </td>



                                            </tr>



                                            @endforeach



                                        </tbody>



                                    </table>



                                </div>    



                                <div class="">



                                    <div class="item-details">



                                        



                                        @if (! cart()->isItemHaveQuantity($item))



                                            <div class="error-message mt-15">



                                                * {{ __('shop::app.checkout.cart.quantity-error') }}



                                            </div>



                                        @endif



                                    </div>







                                </div>



                           



                        </div>







                        {!! view_render_event('bagisto.shop.checkout.cart.controls.after', ['cart' => $cart]) !!}







                        <div class="misc-controls">



                            <a href="{{ route('shop.home.index') }}" class="btn btn-lg btn-primary">{{ __('shop::app.checkout.cart.continue-shopping') }}</a>







                            <div>



                                <button type="submit" class="btn btn-lg btn-primary btn-black lg-important">



                                    {{ __('shop::app.checkout.cart.update-cart') }}



                                </button>







                                @if (! cart()->hasError())



                                    <a href="{{ route('shop.checkout.onepage.index') }}" class="btn btn-lg btn-primary">



                                        {{ __('shop::app.checkout.cart.proceed-to-checkout') }}



                                    </a>



                                @endif



                            </div>



                        </div>







                        {!! view_render_event('bagisto.shop.checkout.cart.controls.after', ['cart' => $cart]) !!}



                    </form>



                </div>







                <div class="right-side">



                    {!! view_render_event('bagisto.shop.checkout.cart.summary.after', ['cart' => $cart]) !!}







                    @include('shop::checkout.total.summary', ['cart' => $cart])







                    {!! view_render_event('bagisto.shop.checkout.cart.summary.after', ['cart' => $cart]) !!}



                </div>



            </div>







            @include ('shop::products.view.cross-sells')







        @else







            <div class="title">



                {{ __('shop::app.checkout.cart.title') }}



            </div>







            <div class="cart-content">



                <p>



                    {{ __('shop::app.checkout.cart.empty') }}



                </p>







                <p style="display: inline-block;">



                    <a style="display: inline-block;" href="{{ route('shop.home.index') }}" class="btn btn-lg btn-primary">{{ __('shop::app.checkout.cart.continue-shopping') }}</a>



                </p>



            </div>







        @endif







    </div>



    </section>





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

                {{ __('shop::app.checkout.cart.cart-remove-action') }}

            </div>

            

            <!-- Modal footer -->

            <div class="modal-footer">

              <button type="button" class="btn btn-default yes-remove" url-attr="">Yes</button>

              <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>

            </div>

            

          </div>

        </div>

      </div>



      <div class="modal" id="moveTowishlistModal">

        <div class="modal-dialog">

          <div class="modal-content">

            <!-- Modal Header -->

            <div class="modal-header">

              <h4 class="modal-title">Warning</h4>

              <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>

            <!-- Modal body -->

            <div class="modal-body move-model-msg">

                {{ __('shop::app.checkout.cart.cart-remove-action') }}

            </div>

            

            <!-- Modal footer -->

            <div class="modal-footer">

              <button type="button" class="btn btn-default yes-move" url-attr="">Yes</button>

              <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>

            </div>

            

          </div>

        </div>

      </div>





@endsection







@push('scripts')







    <script type="text/x-template" id="quantity-changer-template">



        <div class="quantity control-group" :class="[errors.has(controlName) ? 'has-error' : '']">



            <div class="wrap">



                <label>{{ __('shop::app.products.quantity') }}</label>







                <button type="button" class="decrease" @click="decreaseQty()">-</button>







                <input :name="controlName" class="control" :value="qty" v-validate="'required|numeric|min_value:1'" data-vv-as="&quot;{{ __('shop::app.products.quantity') }}&quot;" readonly>







                <button type="button" class="increase" @click="increaseQty()">+</button>







                <span class="control-error" v-if="errors.has(controlName)">@{{ errors.first(controlName) }}</span>



            </div>



        </div>



    </script>



    <script>

        $("body").on('click','.delete-review-btn',function(e) 

        {

            $('.yes-remove').attr("url-attr",$(this).attr("data-url"));

	        $('#myModal').modal('show');

        })



        $("body").on('click','.yes-remove',function(e) {

            $('#myModal').modal('hide');

            window.location.href = $('.yes-remove').attr("url-attr");

        });



        $("body").on('click','.add-to-wishlist',function(e) 

        {

            $('.yes-move').attr("wish-btn-scope",$(this).attr('parent-class'));

            $('.yes-move').attr("url-attr",$(this).attr("data-url"));

	        $('#moveTowishlistModal').modal('show');

        })



        $("body").on('click','.yes-move',function(e) 

        {

            var _this = $(this);

            var row_class = $(this).attr("wish-btn-scope");

            $('#moveTowishlistModal').modal('hide');

            $.ajax({

                url: _this.attr("url-attr"),

                data: {},

                type: "GET",

                success: function (response) 

                {

                    if(response.status == 'succsess')

                    {

                        $(".alert-wrapper").html("<div class='alert alert-success'><p>" + response.msg +"</p></div>");

                        $('.'+row_class).parent().remove();

                    }

                    else if(response.status == 'failed')

                    {

                        $(".alert-wrapper").html("<div class='alert alert-error'><p>" + response.msg +"</p></div>");

                    }

                    setTimeout(function() {

                        $(".alert-wrapper").fadeOut('fast');

                    }, 1000);

                }

            })

        });



    </script>



    <script>



        Vue.component('quantity-changer', {



            template: '#quantity-changer-template',







            inject: ['$validator'],







            props: {



                controlName: {



                    type: String,



                    default: 'quantity'



                },







                quantity: {



                    type: [Number, String],



                    default: 1



                }



            },







            data: function() {



                return {



                    qty: this.quantity



                }



            },







            watch: {



                quantity: function (val) {



                    this.qty = val;







                    this.$emit('onQtyUpdated', this.qty)



                }



            },







            methods: {



                decreaseQty: function() {



                    if (this.qty > 1)



                        this.qty = parseInt(this.qty) - 1;







                    this.$emit('onQtyUpdated', this.qty)



                },







                increaseQty: function() {



                    this.qty = parseInt(this.qty) + 1;







                    this.$emit('onQtyUpdated', this.qty)



                }



            }



        });







        function removeLink(message) {



            if (!confirm(message))



            event.preventDefault();



        }







        function updateCartQunatity(operation, index) {



            var quantity = document.getElementById('cart-quantity'+index).value;







            if (operation == 'add') {



                quantity = parseInt(quantity) + 1;



            } else if (operation == 'remove') {



                if (quantity > 1) {



                    quantity = parseInt(quantity) - 1;



                } else {



                    alert('{{ __('shop::app.products.less-quantity') }}');



                }



            }



            document.getElementById('cart-quantity'+index).value = quantity;



            event.preventDefault();



        }



    </script>



@endpush