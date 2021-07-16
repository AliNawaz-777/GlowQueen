<?php

namespace Webkul\Product\Repositories;

use DB;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Product\Models\ProductAttributeValue;
use Illuminate\Support\Facades\Storage;

/**
 * Product Repository
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class BlogImageRepository extends Repository
{
    /**
     * AttributeRepository object
     *
     * @var array
     */
    protected $attributeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Attribute\Repositories\AttributeRepository $attributeRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        App $app
    )
    {
        $this->attributeRepository = $attributeRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model() {
        return 'Webkul\Product\Models\BlogImage';
    }
    /**
     * @param array $data
     * @param $id
     */
    public function uploadImages(array $data, $id)
    {
        $id = (int)$id;
        $blogImgs = $this->model->where("blog_id", $id)->get();
        $previousImagesIds = $blogImgs->pluck("id");

        foreach ($data as $images => $img) {
            $file = 'images.'. $images;
            $dir = 'blogs/'. $id;
            
            if(str_contains($images, 'image_')){
                if (request()->hasFile($file)) {
                    $blogimgstatus = $this->model->create([
                        'path' => request()->file($file)->store($dir),
                        'blog_id' => $id
                    ]);
                }
            }
            else{

                if (is_numeric($index = $previousImagesIds->search($images))) {
                    $previousImagesIds->forget($index);
                }

                if (request()->hasFile($file)) {
                    if ($this->model->find($images)) {
                        Storage::delete($this->model->path);
                    }
                    $this->model->where('id', $images)->update([
                            'path' => request()->file($file)->store($dir)
                        ]);
                }
            }         
        }

        foreach ($previousImagesIds as $imageId) {
            if ($this->model->find($imageId)) {
                Storage::delete($this->model->path);

                $this->delete($imageId);
            }
        }        
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getImages($id)
    {
        $images = $this->model->where('blog_id', '=', $id)->get();
        return $images;
    }
}