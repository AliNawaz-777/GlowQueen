<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\API\Http\Resources\Catalog\Category as CategoryResource;

/**
 * Category controller
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class CategoryController extends Controller
{
    /**
     * CategoryRepository object
     *
     * @var array
     */
    protected $categoryRepository;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Category\Repositories\CategoryRepository $categoryRepository
     * @return void
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CategoryResource::collection(
                $this->categoryRepository->getVisibleCategoryTree(request()->input('parent_id'))
            );
    }
}
