@if (core()->getConfigData('general.design.admin_logo.logo_image'))
    <img src="{{ \Illuminate\Support\Facades\Storage::url(core()->getConfigData('general.design.admin_logo.logo_image')) }}" alt="{{ config('app.name') }}" style="width: 110px;"/>
@else
    <img src="{{ bagisto_asset('images/glowQueen.png') }}">
@endif