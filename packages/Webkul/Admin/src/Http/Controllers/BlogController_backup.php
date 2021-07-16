<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Repositories\CoreConfigRepository;
use Webkul\Product\Repositories\BlogRepository;
use Webkul\Product\Repositories\BlogImageRepository;
use Webkul\Product\Repositories\CommentRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Core\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webkul\Product\Models\Comment;

/**
 * Configuration controller
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class BlogController extends Controller
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
    protected $categoryRepository;

    /**
     * BlogRepository object
     *
     * @var Object
     */
    protected $blogRepository;

    /**
     * BlogImageRepository object
     *
     * @var Object
     */
    protected $blogImageRepository;

    /**
     * commentRepository object
     *
     * @var Object
     */
    protected $commentRepository;

    /**
     * CustomerRepository object
     *
     * @var Object
    */
    protected $customerRepository;

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
    protected $configTree;

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
        CategoryRepository $categoryRepository,
        BlogRepository $blogRepository,
        BlogImageRepository $blogImageRepository,
        CommentRepository $commentRepository,
        CustomerRepository $customerRepository
        )
    {
        $this->middleware('admin');

        $this->coreConfigRepository = $coreConfigRepository;

        $this->categoryRepository = $categoryRepository;

        $this->blogRepository = $blogRepository;

        $this->blogImageRepository = $blogImageRepository;

        $this->customerRepository = $customerRepository;

        $this->commentRepository = $commentRepository;

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
        $categories = $this->categoryRepository->getCategoryTree();
        return view($this->_config['view'] ,compact('categories'));
    }

    public function store()
    {
        $this->validate(request(), [
            'title' => 'required',
            'url' => 'unique',
        ]);
        $data = request()->all();

        $new_blog['status'] = 'pending';
        $new_blog['blog_title'] = $data['title'];
        $new_blog['short_description'] = $data['blog_short_description'];
        $new_blog['blog_description'] = $data['blog_description'];
        $new_blog['url_key'] = $data['url_key'];
        $new_blog['tags'] = $data['tags-1'];
        $new_blog['admin_id'] = auth()->guard('admin')->user()->id;
        $new_blog['blog_date'] = date('Y-m-d', strtotime($data['blog-date']));
        $new_blog['meta_title'] = $data['meta_title'];
        $new_blog['meta_description'] = $data['meta_description'];
        $new_blog['meta_keyword'] = $data['meta_keyword'];

        $input_categories = $data['categories'];
        $categories = '';
        foreach ($input_categories as $cat) {
            $categories .= $cat.',';
        }
        
        $new_blog['category'] = $categories;

        $url_key = $this->blogRepository->findBySlug($new_blog['url_key']);

        if (COUNT($url_key) > 0) {
            session()->flash('error', trans('admin::app.blog.url-key-fail'));

            return redirect()->back();
        } else {
            $status = $this->blogRepository->create($new_blog);

            if ($status) {
                $id = $status['id'];
                if (isset($data['images'])) {
                    $img_status = $this->blogImageRepository->uploadImages($data['images'], $id);
                }

                session()->flash('success', trans('admin::app.blog.add-success'));

                return redirect('/admin/blog');
            } else {
                session()->flash('error', trans('admin::app.blog.add-fail'));

                return redirect()->back();
            }
            
        }
        
    }

    public function view($id)
    {
        $blogs = $this->blogRepository->findBlog($id);
        
        $blog_images = $this->blogImageRepository->getImages($id);
        
        $comments_records = $this->commentRepository->getAllComments($id);
        $comments = $comments_records;
        $images = [];
        $i = 0;
        
        foreach ($blog_images as $img) {
            $images[$i] = $img['path'];
            $i++;
        }
        
        return view($this->_config['view'], compact('blogs', 'images', 'comments'));
    }

    public function cancelBlog($id)
    {
        $blogs = $this->blogRepository->findBlog($id);
        $status = $blogs['status'];

        $new_status = '';

        if ($status == 'pending' || $status == 'confirm') {
            $new_status = 'cancel';
        }

        $update_status = $this->blogRepository->updateStatus($id, $new_status);

        if ($update_status == 1) {
            session()->flash('success', trans('admin::app.blog.details.cancel-success'));

            return redirect('/admin/blog');
        }
        else{
            session()->flash('error', trans('admin::app.blog.details.status-fail'));

            return redirect()->back();
        }
    }

    public function confirmBlog($id)
    {
        $blogs = $this->blogRepository->findBlog($id);
        $status = $blogs['status'];
    
        $new_status = '';

        if ($status == 'pending' || $status == 'cancel') {
            $new_status = 'confirm';
        }

        $update_status = $this->blogRepository->updateStatus($id, $new_status);

        if ($update_status == 1) {
            session()->flash('success', trans('admin::app.blog.details.confirm-success'));

            return redirect('/admin/blog');
        }
        else{
            session()->flash('error', trans('admin::app.blog.details.status-fail'));

            return redirect()->back();
        }
    }

    public function blogDelete($id)
    {
        $delete_status = $this->blogRepository->deleteBlog($id);

        if ($delete_status == 1) {
            session()->flash('success', trans('admin::app.blog.details.delete-success'));

            return redirect('/admin/blog');
        }
        else{
            session()->flash('error', trans('admin::app.blog.details.status-fail'));

            return redirect()->back();
        }
    }

    public function deleteComment($id)
    {
        $delete_status = $this->commentRepository->deleteComment($id);

        if ($delete_status == 1) {
            session()->flash('success', trans('admin::app.blog.details.delete-comment'));

            return redirect()->back();
        }
        else{
            session()->flash('error', trans('admin::app.blog.details.status-fail'));

            return redirect()->back();
        }
    }

    public function changeComment()
    {
        $id = request()->input('id');
        $comment = Comment::where('id', '=', $id)->get();
        $status = $comment[0]['status'];

        if ($status == 0) {
            $status = 1;
        } else {
            $status = 2;
        }

        Comment::where('id','=', $id)->update(['status'=> $status]);
        
    }
}