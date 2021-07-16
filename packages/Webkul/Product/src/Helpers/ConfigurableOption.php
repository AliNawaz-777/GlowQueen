<?php

namespace Webkul\Product\Helpers;

use Webkul\Attribute\Repositories\AttributeOptionRepository as AttributeOption;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttributeValue;

/**
 * Configurable Option Helper
 *
 * @author Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class ConfigurableOption extends AbstractProduct
{
    /**
     * AttributeOptionRepository object
     *
     * @var array
     */
    protected $attributeOption;

    /**
     * ProductImage object
     *
     * @var array
     */
    protected $productImage;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Attribute\Repositories\AttributeOptionRepository $attributeOption
     * @param  Webkul\Product\Helpers\ProductImage                     $productImage
     * @return void
     */
    public function __construct(
        AttributeOption $attributeOption,
        ProductImage $productImage
    )
    {
        if (isset($attributeOption) && $attributeOption != NULL) {
            $this->attributeOption = $attributeOption;
        }
        
        if (isset($productImage) && $productImage != NULL) {
            $this->productImage = $productImage;
        }

    }

    /**
     * Returns the allowed variants
     *
     * @param Product $product
     * @return float
     */
    public function getAllowedProducts($product)
    {
        static $variants = [];

        if (count($variants))
            return $variants;

        foreach ($product->variants as $variant) {
            if ($variant->isSaleable()) {
                $variants[] = $variant;
            }
        }

        return $variants;
    }

    /**
     * Returns the allowed variants JSON
     *
     * @param Product $product
     * @return array
     */
    public function getConfigurationConfig($product)
    {
        $options = $this->getOptions($product, $this->getAllowedProducts($product));

        $config = [
            'attributes' => $this->getAttributesData($product, $options),
            'index' => isset($options['index']) ? $options['index'] : [],
            'regular_price' => [
                'formated_price' => core()->currency($product->getTypeInstance()->getMinimalPrice()),
                'price' => $product->getTypeInstance()->getMinimalPrice()
            ],
            'variant_prices' => $this->getVariantPrices($product),
            'variant_images' => $this->getVariantImages($product),
            'chooseText' => trans('shop::app.products.choose-option')
        ];

        return $config;
    }

    /**
     * Get allowed attributes
     *
     * @param Product $product
     * @return array
     */
    public function getAllowAttributes($product)
    {
        return $product->product->super_attributes;
    }

    /**
     * Get Configurable Product Options
     *
     * @param Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];

        $allowAttributes = $this->getAllowAttributes($currentProduct);

        foreach ($allowedProducts as $product) {
            if ($product instanceof \Webkul\Product\Models\ProductFlat) {
                $productId = $product->product_id;
            } else {
                $productId = $product->id;
            }

            foreach ($allowAttributes as $productAttribute) {
                $productAttributeId = $productAttribute->id;

                $attributeValue = $product->{$productAttribute->code};

                if ($attributeValue == '' && $product instanceof \Webkul\Product\Models\ProductFlat)
                    $attributeValue = $product->product->{$productAttribute->code};

                $options[$productAttributeId][$attributeValue][] = $productId;

                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }

        return $options;
    }

    /**
     * Get product attributes
     *
     * @param Product $product
     * @param array $options
     * @return array
     */
    public function getAttributesData($product, array $options = [])
    {
        $defaultValues = [];

        $attributes = [];

        $allowAttributes = $this->getAllowAttributes($product);

        foreach ($allowAttributes as $attribute) {

            $attributeOptionsData = $this->getAttributeOptionsData($attribute, $options);

            if ($attributeOptionsData) {
                $attributeId = $attribute->id;

                $attributes[] = [
                    'id' => $attributeId,
                    'code' => $attribute->code,
                    'label' => $attribute->name ? $attribute->name : $attribute->admin_name,
                    'swatch_type' => $attribute->swatch_type,
                    'options' => $attributeOptionsData
                ];
            }
        }

        return $attributes;
    }

    /**
     * @param Attribute $attribute
     * @param array $options
     * @return array
     */
    protected function getAttributeOptionsData($attribute, $options)
    {
        $attributeOptionsData = [];

        foreach ($attribute->options as $attributeOption) {

            $optionId = $attributeOption->id;

            if (isset($options[$attribute->id][$optionId])) {
                $attributeOptionsData[] = [
                    'id' => $optionId,
                    'label' => $attributeOption->label,
                    'swatch_value' => $attribute->swatch_type == 'image' ? $attributeOption->swatch_value_url : $attributeOption->swatch_value,
                    'products' => $options[$attribute->id][$optionId]
                ];
            }
        }

        return $attributeOptionsData;
    }

    /**
     * Get product prices for configurable variations
     *
     * @param Product $product
     * @return array
     */
    protected function getVariantPrices($product)
    {
        $prices = [];

        foreach ($this->getAllowedProducts($product) as $variant) {
            if ($variant instanceof \Webkul\Product\Models\ProductFlat) {
                $variantId = $variant->product_id;
            } else {
                $variantId = $variant->id;
            }

            $prices[$variantId] = $variant->getTypeInstance()->getProductPrices();
        }

        return $prices;
    }

    /**
     * Get product images for configurable variations
     *
     * @param Product $product
     * @return array
     */
    protected function getVariantImages($product)
    {
        $images = [];

        foreach ($this->getAllowedProducts($product) as $variant) {
            if ($variant instanceof \Webkul\Product\Models\ProductFlat) {
                $variantId = $variant->product_id;
            } else {
                $variantId = $variant->id;
            }

            $images[$variantId] = $this->productImage->getGalleryImages($variant);
        }

        return $images;
    }

    public function getProdPrices($type,$product)
    {
        $price = 0;
        $variants = [];
        $price = '';
        $disc = 0;

        if($type)
        {
            foreach ($product->variants as $key => $variant) 
            {
                $discount = $variant->special_price;
                
                $start_date = ($variant->special_price_from != NULL) ? strtotime($variant->special_price_from) : 0;
                $end_date = ($variant->special_price_to != NULL) ? strtotime($variant->special_price_to) : 0;
                $now = strtotime(now());

                if($discount > 0)
                {
                    if($start_date > 0 && $end_date == 0)
                    {
                        if(($now >= $start_date))
                        {
                            $variants[$key] = $discount;
                        }
                        else
                        {
                            $variants[$key] = $variant->price;
                        }
                    }
                    elseif($start_date == 0 && $end_date > 0)
                    {
                        if(($now <= $end_date))
                        {
                            $variants[$key] = $discount;
                        }
                        else
                        {
                            $variants[$key] = $variant->price;
                        }
                    }
                    elseif($start_date == 0 && $end_date == 0)
                    {
                        $variants[$key] = $discount;
                    }
                    elseif($start_date > 0 && $end_date > 0 && ($now >= $start_date && $now <= $end_date))
                    {
                        $variants[$key] = $discount;
                    }
                    else
                    {
                        $variants[$key] = $variant->price;
                    }
                }
                else
                {
                    $variants[$key] = $variant->price;
                }   
            }
            $price = round((isset($variants) && !empty($variants)) ? min($variants) : '');
        }
        else
        {
            $discount_price = $product->special_price;
                
            $start_date = ($product->special_price_from != NULL) ? strtotime($product->special_price_from) : 0;
            $end_date = ($product->special_price_to != NULL) ? strtotime($product->special_price_to) : 0;
            $now = strtotime(now());

            if($discount_price > 0)
            {
                $discount = $product->price - $discount_price;
                $discount = ($discount / $product->price) * 100;
                $disc = round($discount) . '%';

                if($start_date > 0 && $end_date == 0)
                {
                    if(($now >= $start_date))
                    {
                        $price = round($discount_price);
                    }
                    else
                    {
                        $price = round($product->price);
                        $disc = 0;
                    }
                }
                elseif($start_date == 0 && $end_date > 0)
                {
                    if(($now <= $end_date))
                    {
                        $price = round($discount_price);
                    }
                    else
                    {
                        $price = round($product->price);
                        $disc = 0;
                    }
                }
                elseif($start_date == 0 && $end_date == 0)
                {
                    $price = round($discount_price);
                }
                elseif($start_date > 0 && $end_date > 0 && ($now >= $start_date && $now <= $end_date))
                {
                    $price = round($discount_price);
                }
                else
                {
                    $price = round($product->price);
                    $disc = 0;
                }
               
                $price = $price;
            }
            else
            {
                $price = round($product->price);
            }
        }

        $data = [];
        $data['price'] = $price;
        $data['org_price'] = round($product->price);
        $data['discount'] = $disc;
        return $data;
    }
    public function checkProductHasVarient($prod_id)
    {
        $query = \DB::table('products')->where('products.id', $prod_id)->join('product_flat', 'products.id', '=', 'product_flat.product_id')->first();
        if($query->type == 'configurable')
        {
            return $query->url_key;
        }
        else
        {
            return false;
        }
    }
}