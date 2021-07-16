<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductDownloadableSampleRepository;
use Webkul\Product\Repositories\ProductDownloadableLinkRepository;

/**
 * Product controller
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class ProductController extends Controller
{

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * ProductRepository object
     *
     * @var array
     */
    protected $productRepository;

    /**
     * ProductAttributeValueRepository object
     *
     * @var array
     */
    protected $productAttributeValueRepository;

    /**
     * ProductDownloadableSampleRepository object
     *
     * @var array
     */
    protected $productDownloadableSampleRepository;

    /**
     * ProductDownloadableLinkRepository object
     *
     * @var array
     */
    protected $productDownloadableLinkRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository                   $productRepository
     * @param  \Webkul\Product\Repositories\productAttributeValueRepository     $productAttributeValueRepository
     * @param  \Webkul\Product\Repositories\ProductDownloadableSampleRepository $productDownloadableSampleRepository
     * @param  \Webkul\Product\Repositories\ProductDownloadableLinkRepository   $productDownloadableLinkRepository
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductAttributeValueRepository $productAttributeValueRepository,
        ProductDownloadableSampleRepository $productDownloadableSampleRepository,
        ProductDownloadableLinkRepository $productDownloadableLinkRepository
    )
    {
        $this->productRepository = $productRepository;

        $this->productAttributeValueRepository = $productAttributeValueRepository;

        $this->productDownloadableSampleRepository = $productDownloadableSampleRepository;

        $this->productDownloadableLinkRepository = $productDownloadableLinkRepository;

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string $slug
     * @return \Illuminate\View\View 
     */
    public function index($slug)
    {
        $product = $this->productRepository->findBySlugOrFail($slug);
        // ->where(function ($query) {
        //     $query->where('product_inventories.qty', '!=',0)
        //     ->orWhere('products.type', '=', 'configurable');
        // })
            
        // if($product->status != 1 || $product->p_qty < 1)
        if($product->status != 1)
        {	
            return redirect('/');

            // $res = \DB::table('products')->where(['type' => 'configurable','id' => $product->id])->first();
            // if(!isset($res->id))
            // {
            //     return redirect('/');
            // }
        }
        $customer = auth()->guard('customer')->user();
        return view($this->_config['view'], compact('product', 'customer'));
    }

    /**
     * Download image or file
     *
     * @param  int $productId, $attributeId
     * @return \Illuminate\Http\Response
     */
    public function download($productId, $attributeId)
    {
        $productAttribute = $this->productAttributeValueRepository->findOneWhere([
            'product_id'   => $productId,
            'attribute_id' => $attributeId
        ]);

        return Storage::download($productAttribute['text_value']);
    }

    /**
     * Download the for the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadSample()
    {
        try {
            if (request('type') == 'link') {
                $productDownloadableLink = $this->productDownloadableLinkRepository->findOrFail(request('id'));

                if ($productDownloadableLink->sample_type == 'file')
                    return Storage::download($productDownloadableLink->sample_file);
                else {
                    $fileName = $name = substr($productDownloadableLink->sample_url, strrpos($productDownloadableLink->sample_url, '/') + 1);

                    $tempImage = tempnam(sys_get_temp_dir(), $fileName);

                    copy($productDownloadableLink->sample_url, $tempImage);

                    return response()->download($tempImage, $fileName);
                }
            } else {
                $productDownloadableSample = $this->productDownloadableSampleRepository->findOrFail(request('id'));

                if ($productDownloadableSample->type == 'file')
                    return Storage::download($productDownloadableSample->file);
                else {
                    $fileName = $name = substr($productDownloadableSample->url, strrpos($productDownloadableSample->url, '/') + 1);

                    $tempImage = tempnam(sys_get_temp_dir(), $fileName);

                    copy($productDownloadableSample->url, $tempImage);

                    return response()->download($tempImage, $fileName);
                }
            }
        } catch(\Exception $e) {
            abort(404);
        }
    }
}
