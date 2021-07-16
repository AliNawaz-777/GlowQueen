@if(core()->getConfigData('customer.settings.newsletter.subscription'))

	<section class="featured-products">
		<div class="subscribe-us-container" style="background-image: url('{{ asset('static-images/card.jpg') }}');">
			<div class="subscribe-us-block">
				<h4>Subscribe to our Newsletter to get latest updates about our collections.</h4>
				<div class="subscribe-us-form">
					<div class="form-container">
                        <form action="{{ route('shop.subscribe') }}">
                            <div class="control-group" :class="[errors.has('subscriber_email') ? 'has-error' : '']">
                                <input type="email" class="control subscribe-field" name="subscriber_email" placeholder="Email Address" required><br/>

                                <button class="btn btn-md btn-primary">{{ __('shop::app.subscription.subscribe') }}</button>
                            </div>
                        </form>
                    </div>
				</div>
			</div>
		</div>
	</section>

@endif