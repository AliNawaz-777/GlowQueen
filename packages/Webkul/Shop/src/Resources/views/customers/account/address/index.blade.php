@extends('shop::layouts.master')

@section('page_title')
    {{ __('shop::app.customer.account.address.index.page-title') }}
@endsection

@section('content-wrapper')

<div class="container">

<div class="account-content">

    @include('shop::customers.account.partials.sidemenu')

    <div class="account-layout">

        <div class="account-head">
            <span class="back-icon"><a href="{{ route('customer.account.index') }}"><i class="icon icon-menu-back"></i></a></span>
            <span class="account-heading">{{ __('shop::app.customer.account.address.index.title') }}</span>

            @if (! $addresses->isEmpty())
                <span class="account-action">
                    <a href="{{ route('customer.address.create') }}">{{ __('shop::app.customer.account.address.index.add') }}</a>
                </span>
            @else
                <span></span>
            @endif
            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('bagisto.shop.customers.account.address.list.before', ['addresses' => $addresses]) !!}

        <div class="account-table-content form-des">
            @if ($addresses->isEmpty())
                <div>{{ __('shop::app.customer.account.address.index.empty') }}</div>
                <br/>
                <a href="{{ route('customer.address.create') }}">{{ __('shop::app.customer.account.address.index.add') }}</a>
            @else
                <div class="address-holder">
                    @foreach ($addresses as $address)
                        <div class="address-card">
                            <div class="details">
                                <span class="bold">{{ auth()->guard('customer')->user()->name }}</span>
                                <ul class="address-card-list">
                                    <li class="mt-5">
                                        {{ $address->name }}
                                    </li>

                                    <li class="mt-5">
                                        {{ $address->address1 }},
                                    </li>

                                    @if($address->city != "")
                                        <li class="mt-5">
                                            {{ ucfirst(strtolower($address->city)) }} {{ ($address->postcode) ? ' - '.$address->postcode : '' }},
                                        </li>
                                    @endif

                                    @if($address->state != "")
                                        <li class="mt-5">
                                            {{ $address->state }},
                                        </li>
                                    @endif

                                    <li class="mt-5">
                                        {{ core()->country_name($address->country) }}.
                                    </li>

                                    <li class="mt-10">
                                        {{ __('shop::app.customer.account.address.index.contact') }} : {{ $address->phone }}
                                    </li>
                                </ul>

                                <div class="control-links mt-20">
                                    <span>
                                        <a href="{{ route('customer.address.edit', $address->id) }}">
                                            <!--{{ __('shop::app.customer.account.address.index.edit') }}-->
                                            <img src="{{bagisto_asset ('vendor/webkul/ui/assets/images/edit.svg') }}">
                                        </a>
                                    </span>

                                    <span>
                                        <a class="delete-address-btn" data-url="{{ route('address.delete', $address->id) }}" href="javascript:void(0)">
                                            <!--{{ __('shop::app.customer.account.address.index.delete') }}-->
                                             <img src="{{bagisto_asset ('vendor/webkul/ui/assets/images/trash.svg') }}">
                                             
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {!! view_render_event('bagisto.shop.customers.account.address.list.after', ['addresses' => $addresses]) !!}
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
            {{ __('shop::app.customer.account.address.index.confirm-delete') }}
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
        $("body").on('click','.delete-address-btn',function(e) 
        {
            $('.yes-remove').attr("url-attr",$(this).attr("data-url"));
	        $('#myModal').modal('show');
        })

        $("body").on('click','.yes-remove',function(e) {
            $('#myModal').modal('hide');
            window.location.href = $('.yes-remove').attr("url-attr");
        });
        function deleteAddress(message) {
            if (!confirm(message))
            event.preventDefault();
        }
    </script>
@endpush
