<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Repositories\CoreConfigRepository;
use Webkul\Product\Repositories\TestimonailRepository;
use Webkul\Product\Repositories\TestimonailImageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;




/**
 * Configuration controller
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class TestimonailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * CategoryRepository object
     *
     * @var Object
     */

    /**
     * BlogRepository object
     *
     * @var Object
     */
    protected $testimonailRepository;
    /**
     * BlogImageRepository object
     *
     * @var Object
     */
    protected $testimonailImageRepository;

    /**
     * commentRepository object
     *
     * @var Object
     */
    /**
     * CustomerRepository object
     *
     * @var Object
    */

    /**
     * CoreConfigRepository object
     *
     * @var array
     */
    protected $coreConfigRepository;

    /**
     *
     * @var array
     */

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Category\Repositories\CategoryRepository $categoryRepository
     * @param  \Webkul\Product\Repositories\BlogRepository $blogRepository
     * @param  \Webkul\Product\Repositories\BlogImageRepository $blogImageRepository
     * @param  \Webkul\Product\Repositories\CommentRepository $commentRepository
     * @param  \Webkul\Customer\Repositories\CustomerRepository $customer
     * @param  \Webkul\Core\Repositories\CoreConfigRepository $coreConfigRepository
     * @return void
     */
    public function __construct(
        CoreConfigRepository $coreConfigRepository,
        TestimonailImageRepository $testimonailImageRepository,
        TestimonailRepository $testimonailRepository
        )
    {
        $this->middleware('admin');

        $this->coreConfigRepository = $coreConfigRepository;

        $this->testimonailRepository = $testimonailRepository;

        $this->testimonailImageRepository = $testimonailImageRepository;

        $this->_config = request('_config');

    }

    /**
     * Prepares config tree
     *
     * @return void
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    public function create()
    {

        return view($this->_config['view']);
    }

    public function store()
    {

         $this->validate(request(), [
            'title' => 'required',
            'url' => 'unique',
        ]);
        $data = request()->all(); 

        $new_blog['testimonail_title'] = $data['title'];
        $new_blog['short_description'] = $data['blog_short_description'];
        $new_blog['name'] = $data['name'];
        $new_blog['admin_id'] = auth()->guard('admin')->user()->id;
        
        $status = $this->testimonailRepository->create($new_blog);

        if ($status) {
            $id = $status['id'];
            if (isset($data['images'])) {
                $img_status = $this->testimonailImageRepository->uploadImages($data['images'], $id);
            }

            session()->flash('success', trans('admin::app.blog.add-success'));

            return redirect('/admin/testimonail');
            } else {
                session()->flash('error', trans('admin::app.blog.add-fail'));

                return redirect()->back();
            }            
        }

         public function view($id)
            {
                $blogs = $this->testimonailRepository->findBlog($id);
                
                $blog_images = $this->testimonailImageRepository->getImages($id);
                
                $images = [];
                $i = 0;
                
                foreach ($blog_images as $img) {
                    $images[$i] = $img['path'];
                    $i++;
                }
                
                return view($this->_config['view'], compact('blogs', 'images'));
            }

             public function blogDelete($id)
            {
                $delete_status = $this->testimonailRepository->deleteBlog($id);

                    session()->flash('success', trans('admin::app.blog.details.delete-success'));

                    return redirect('/admin/testimonail');
                
            }
             
       
}