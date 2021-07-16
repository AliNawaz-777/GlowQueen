@inject ('attributeRepository', 'Webkul\Attribute\Repositories\AttributeRepository')

@inject ('productFlatRepository', 'Webkul\Product\Repositories\ProductFlatRepository')

@inject ('productRepository', 'Webkul\Product\Repositories\ProductRepository')



<?php
    $filterAttributes = [];

    if (isset($category)) {
        $products = $productRepository->getAll($category->id);

        if (count($category->filterableAttributes) > 0 && count($products)) {
            $filterAttributes = $category->filterableAttributes;

        } else {
            $categoryProductAttributes = $productFlatRepository->getCategoryProductAttribute($category->id);

            if ($categoryProductAttributes) {
                foreach ($attributeRepository->getFilterAttributes() as $filterAttribute) {
                    if (in_array($filterAttribute->id, $categoryProductAttributes)) {
                        $filterAttributes[] = $filterAttribute;
                    } else  if ($filterAttribute ['code'] == 'price') {
                        $filterAttributes[] = $filterAttribute;
                    }
                }

                $filterAttributes = collect($filterAttributes);
            }
        }
    } else {
        $filterAttributes = $attributeRepository->getFilterAttributes();
    }
?>

<?php 

if (isset($category)) {
    $products = $productRepository->getAll($category->id);

    if (count($products)) {
        ?>
        <div class="layered-filter-wrapper">

            {!! view_render_event('bagisto.shop.products.list.layered-nagigation.before') !!}

            <layered-navigation></layered-navigation>

            {!! view_render_event('bagisto.shop.products.list.layered-nagigation.after') !!}

        </div>
    <?php }
}


?>




@push('scripts')
    <script type="text/x-template" id="layered-navigation-template">
        <div>
            @if ($products->count())
                <div class="filter-title">
                    {{ __('shop::app.products.layered-nav-title') }}
                </div>
                
                <div class="filter-content">

                    <div class="filter-attributes">

                        <filter-attribute-item v-for='(attribute, index) in attributes' :attribute="attribute" :key="index" :index="index" @onFilterAdded="addFilters(attribute.code, $event)" :appliedFilterValues="appliedFilters[attribute.code]">
                        </filter-attribute-item>

                    </div>
                </div>
            @endif
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
                var a = @json($filterAttributes);
                console.log(a);
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
                    var params = [];

                    for(key in this.appliedFilters) {
                        if (key != 'page') {
                            params.push(key + '=' + this.appliedFilters[key].join(','))
                        }
                    }

                    window.location.href = "?" + params.join('&');
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
                        max: {{ core()->convertPrice($productFlatRepository->getCategoryProductMaximumPrice($category)) }},

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
                    alert(e)
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

                    this.$emit('onFilterAdded', this.appliedFilters)
                }
            }

        });

        // $(document).ready(function () {
        //     // Handler for .ready() called.
        //     // $('html, body').animate({
        //     //     scrollTop: $('.filter-attributes-content').offset().top + 100
        //     // }, 500);
        // });

        // $(document).ready(function () {

        //     $(".filter-attributes-title").click(function(){
        //       if($(this).parent().hasClass('active')){
        //         console.log('working');
        //         $('html, body').animate({
        //             scrollTop: $('.filter-attributes-content').offset().top + 300
        //         }, 500);
        //       }
        //     });
        // });


    </script>

@endpush
