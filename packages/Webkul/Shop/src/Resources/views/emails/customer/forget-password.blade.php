@component('shop::emails.layouts.master')
    <div style="text-align: center;">
        <a href="{{ config('app.url') }}">
            @include ('shop::emails.layouts.logo')
        </a>
    </div>

    <div style="padding: 30px;">
        <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
            
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('shop::app.mail.forget-password.dear', ['name' => $data['full_name']]) }},
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('shop::app.mail.forget-password.info') }}
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                Your new password is <span style="color: #ed1c1c; font-weight: bold;">{{ $data['password'] }}</span>. Use this password to login to <a href="{{ URL('/') }}">GlowQueen.pk</a>
            </p>
            
            <p style="text-align: center;padding: 20px 0;">
                <a href="{{ URL('customer/login') }}" style="padding: 10px 20px;background: #0041FF;color: #ffffff;text-transform: uppercase;text-decoration: none; font-size: 16px">
                    LOGIN TO GlowQueen
                </a>
            </p>

            <!--<p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">-->
            <!--    {{ __('shop::app.mail.forget-password.final-summary') }}-->
            <!--</p>-->

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('shop::app.mail.forget-password.thanks') }}
            </p>

        </div>
    </div>
@endcomponent