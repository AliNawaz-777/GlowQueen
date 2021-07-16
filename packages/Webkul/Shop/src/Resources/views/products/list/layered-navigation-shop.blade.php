@inject ('attributeRepository', 'Webkul\Attribute\Repositories\AttributeRepository')

@inject ('productFlatRepository', 'Webkul\Product\Repositories\ProductFlatRepository')

@inject ('productRepository', 'Webkul\Product\Repositories\ProductRepository')

<?php
    $filterAttributes = [];
    foreach ($attributeRepository->getFilterAttributes() as $filterAttribute) 
    {
        if ($filterAttribute ['code'] == 'price') {
            $filterAttributes[] = $filterAttribute;
        }
    }
?>

@push('scripts')

    <script type="text/x-template" id="layered-navigation-template">
        <div>
            <div class="filter-title">
                {{ __('shop::app.products.layered-nav-title') }}
            </div>
            
            <div class="filter-content">

                <div class="filter-attributes">

                    <filter-attribute-item v-for='(attribute, index) in attributes' :attribute="attribute" :key="index" :index="index" @onFilterAdded="addFilters(attribute.code, $event)" :appliedFilterValues="appliedFilters[attribute.code]">
                    </filter-attribute-item>

                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="filter-attribute-item-template">
        <div class="filter-attributes-item" :class="[active ? 'active' : '']">

            <div class="filter-attributes-title" @click="active">
                @{{ attribute.name ? attribute.name : attribute.admin_name }}
                <div class="pull-right">
                    <span class="remove-filter-link" v-if="appliedFilters.length" @click.stop="clearFilters()">
                        {{ __('shop::app.products.remove-filter-link-title') }}
                    </span>
                </div>
            </div>

            <div class="filter-attributes-content">
                <div class="price-range-wrapper" v-if="attribute.type == 'price'">
                    <vue-slider
                        ref="slider"
                        v-model="sliderConfig.value"
                        :process-style="sliderConfig.processStyle"
                        :tooltip-style="sliderConfig.tooltipStyle"
                        :max="sliderConfig.max"
                        :lazy="false"
                        @callback="priceRangeUpdated($event)"
                    ></vue-slider>
                </div>
            </div>

        </div>
    </script>

    <script>
        Vue.component('layered-navigation', {

            template: '#layered-navigation-template',

            data: function() {
                return {
                    attributes: @json($filterAttributes),
                    
                    appliedFilters: {}

                }
            },

            created: function () {
                var urlParams = new URLSearchParams(window.location.search);


                var this_this = this;


                urlParams.forEach(function (value, index) {
                    this_this.appliedFilters[index] = value.split(',');


                });
            },

            methods: {
                addFilters: function (attributeCode, filters) {
                    if (filters.length) {
                        this.appliedFilters[attributeCode] = filters;

                    } else {
                        delete this.appliedFilters[attributeCode];
                    }
                    this.applyFilter()
                },

                applyFilter: function () {
                    var price = '';
                    for(key in this.appliedFilters) 
                    {
                        if (key == 'price') {
                            price = this.appliedFilters[key].join(',');
                        }
                    }
                    price_range = price;
                    getShopCategoryProducts('');
                }
            }
        });

        Vue.component('filter-attribute-item', {

            template: '#filter-attribute-item-template',

            props: ['index', 'attribute', 'appliedFilterValues'],

            data: function() {
                return {
                    appliedFilters: [],

                    active: true,

                    sliderConfig: {
                        value: [
                            0,
                            0
                        ],
                        max: {{ core()->convertPrice($productFlatRepository->getCategoryProductMaximumPrice()) }},

                        processStyle: {
                            "backgroundColor": "#FF6472"
                        },
                        tooltipStyle: {
                            "backgroundColor": "#FF6472",
                            "borderColor": "#FF6472"
                        }
                    }
                }
            },

            created: function () {
                if (!this.index)
                    this.active = true;
            

                if (this.appliedFilterValues && this.appliedFilterValues.length) {
                    this.appliedFilters = this.appliedFilterValues;



                    if (this.attribute.type == 'price') {
                        this.sliderConfig.value = this.appliedFilterValues;
                    }

                    this.active = true;
                }
            },

            methods: {
                addFilter: function (e) {
                    this.$emit('onFilterAdded', this.appliedFilters)


                },

                priceRangeUpdated: function (value) {
                    this.appliedFilters = value;
                    

                    this.$emit('onFilterAdded', this.appliedFilters)
                },

                clearFilters: function () {
                    if (this.attribute.type == 'price') {
                        this.sliderConfig.value = [0, 0];
                    }

                    this.appliedFilters = [];
                    price_range = '';
                    getShopCategoryProducts('');
                    this.$emit('onFilterAdded', this.appliedFilters)
                }
            }

        });


    </script>
    <script>
        $(document).ready(function(){
            $( ".filter-attributes-item.active" ).each(function( index ) {
                // if(index != 0)
                // {
                //     $(this).hide();
                // }
            });
        })
    </script>
@endpush
