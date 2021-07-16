<country-state></country-state>

@push('scripts')

    <script type="text/x-template" id="country-state-template">
        <div>
            <div class="control-group" :class="[errors.has('country') ? 'has-error' : '']">
                <label for="country" class="required">
                    {{ __('shop::app.customer.account.address.create.country') }}
                </label>

                <select type="text" v-validate="'required'" class="control custom-readonly-select" id="country" name="country" v-model="country" data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.country') }}&quot;">
                    <option value=""></option>
                    @foreach (core()->countries() as $country)
                        @if( $country->code == 'PK')
                            <option value="{{ $country->code }}">{{ $country->name }}</option>
                        @endif
                    @endforeach
                </select>

                <span class="control-error" v-if="errors.has('country')">
                    @{{ errors.first('country') }}
                </span>
            </div>

            <div class="control-group" :class="[errors.has('state') ? 'has-error' : '']">
                <label for="state">
                    {{ __('shop::app.customer.account.address.create.state') }}
                </label>

                <input type="text" class="control" id="state" name="state" v-model="state" v-if="!haveStates()" data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.state') }}&quot;"/>
                <select class="control" id="state" name="state" v-model="state" v-if="haveStates()" data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.state') }}&quot;">

                    <option value="">{{ __('shop::app.customer.account.address.create.select-state') }}</option>

                    <option v-for='(state, index) in countryStates[country]' :value="state.code">
                        @{{ state.default_name }}
                    </option>

                </select>

                <span class="control-error" v-if="errors.has('state')">
                    @{{ errors.first('state') }}
                </span>
            </div>
        </div>
    </script>

    <script>
        Vue.component('country-state', {

            template: '#country-state-template',

            inject: ['$validator'],

            data() {
                return {
                    country: "PK",

                    state: "{{ $stateCode  }}",

                    countryStates: @json(core()->groupedStatesByCountries())
                }
            },

            methods: {
                haveStates() {
                    if (this.countryStates[this.country] && this.countryStates[this.country].length)
                        return true;

                    return false;
                },
            }
        });
    </script>
@endpush