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
class CommentRepository extends Repository
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
        return 'Webkul\Product\Models\Comment';
    }
    /**
     * @param $id
     */
    public function getComments($id)
    {
        return $this->model->where(['post_id' => $id, 'status' => 1])->orderBy('comment_date','desc')->get();
    }

    public function getAllComments($id)
    {
        return $this->model->where(['post_id' => $id])->orderBy('comment_date','desc')->get();
    }

    /**
     * @param $id
     */
    public function deleteComment($id)
    {
        return $this->model->where('id', '=', $id)->delete();
    }
    /**
     * @param $data
     */
    public function create(array $data)
    {
        $data['comment_date'] = date('Y-m-d H:i:s');
        return $this->model->create($data);
    }
}