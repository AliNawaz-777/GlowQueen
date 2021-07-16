@inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')

{!! view_render_event('bagisto.shop.products.list.toolbar.before') !!}

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


    $products = '';
if (isset($category)) {
    $products = $productRepository->getAll($category->id);
}

?>
<div class="top-toolbar mb-35">
    <div class="row">
        <div class="col-12 d-inline-block d-md-none">
			<div class="text-right filter-icons">
				<span class="sort-icon d-inline-block align-top"></span>
				<span class="filter-icon d-inline-block align-top"></span>
			</div>
		</div>
		<div class="col-lg-5 col-12">
			<div class="layered-filter-wrapper">
				<layered-navigation></layered-navigation>
			</div>
        </div>
        @if ($products->count())
		<div class="col-lg-6 offset-lg-1 col-12">
            <div class="pager">
        
                <div class="view-mode">
                    @if ($toolbarHelper->isModeActive('grid'))
                        <span class="grid-view">
                            <i class="icon grid-view-icon"></i>
                        </span>
                    @else
                        <a href="{{ $toolbarHelper->getModeUrl('grid') }}" class="grid-view">
                            <i class="icon grid-view-icon"></i>
                        </a>
                    @endif
        
                    @if ($toolbarHelper->isModeActive('list'))
                        <span class="list-view">
                            <i class="icon list-view-icon"></i>
                        </span>
                    @else
                        <a href="{{ $toolbarHelper->getModeUrl('list') }}" class="list-view">
                            <i class="icon list-view-icon"></i>
                        </a>
                    @endif
                </div>
        
                <div class="sorter">
                    <label>{{ __('shop::app.products.sort-by') }}</label>
        
                    <select onchange="window.location.href = this.value">
        
                        @foreach ($toolbarHelper->getAvailableOrders() as $key => $order)
        
                            <option value="{{ $toolbarHelper->getOrderUrl($key) }}" {{ $toolbarHelper->isOrderCurrent($key) ? 'selected' : '' }}>
                                {{ __('shop::app.products.' . $order) }}
                            </option>
        
                        @endforeach
        
                    </select>
                </div>
        
                <div class="limiter">
                    <label>{{ __('shop::app.products.show') }}</label>
        
                    <select id="limit-drop-down">
        
                        @foreach ($toolbarHelper->getAvailableLimits() as $limit)
        
                            <option value="{{ $toolbarHelper->getLimitUrl($limit) }}" {{ $toolbarHelper->isLimitCurrent($limit) ? 'selected' : '' }}>
                                {{ $limit }}
                            </option>
        
                        @endforeach
        
                    </select>
                </div>
        
            </div>
        </div>
        @endif
    </div>
</div>

{!! view_render_event('bagisto.shop.products.list.toolbar.after') !!}


<div class="responsive-layred-filter mb-20">
    <layered-navigation></layered-navigation>
</div>