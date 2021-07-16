<?php

namespace Webkul\Shop\Http\Controllers;

use http\Env\Request;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Product\Repositories\BlogRepository;
use Webkul\Product\Repositories\CommentRepository;

/**
 * Home page controller
 *
 * @author    Arhamsoft (info@arhamsoft.com)
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
 class BlogController extends Controller
{
    protected $_config;

    /**
     * SliderRepository object
     *
     * @var Object
    */
    protected $blogRepository;
    
    /**
     * commentRepository object
     *
     * @var Object
     */
    protected $commentRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Core\Repositories\SliderRepository $sliderRepository
     * @param  \Webkul\Product\Repositories\CommentRepository $commentRepository
     * @return void
    */
    public function __construct(BlogRepository $blogRepository, CommentRepository $commentRepository)
    {
        $this->_config = request('_config');

        $this->blogRepository = $blogRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * loads the home page for the storefront
     * 
     * @return \Illuminate\View\View 
     */
    public function blogDetails()
    {
        $blogData = $this->blogRepository->getPost(request()->slug);
        $blogRelatedPosts = $this->blogRepository->relatedCategories($blogData->category,$blogData->id);
        $getRecentPosts = $this->blogRepository->getRecentPosts();
        $comments = $this->commentRepository->getComments($blogData->id);
        $i = 0;
        foreach ($comments as $comment) {
            $ago = $this->timeAgo($comment['comment_date']);
            $comments[$i]['comment_date'] = $ago;
            $i++;
        }
        
        for ($i=0; $i < 99; $i++) { 
            $colors[$i] = $this->GenerateRandomColor();
        }
        
        return view('shop::blog.content.detail', compact('blogData','getRecentPosts','blogRelatedPosts', 'comments', 'colors'));
    }

    function timeAgo($time_ago)
    {
        $time_ago = strtotime($time_ago);
        $cur_time   = time();
        $time_elapsed   = $cur_time - $time_ago;
        $seconds    = $time_elapsed ;
        $minutes    = round($time_elapsed / 60 );
        $hours      = round($time_elapsed / 3600);
        $days       = round($time_elapsed / 86400 );
        $weeks      = round($time_elapsed / 604800);
        $months     = round($time_elapsed / 2600640 );
        $years      = round($time_elapsed / 31207680 );
        // Seconds
        if($seconds <= 60){
            return "just now";
        }
        //Minutes
        else if($minutes <=60){
            if($minutes==1){
                return "one minute ago";
            }
            else{
                return "$minutes minutes ago";
            }
        }
        //Hours
        else if($hours <=24){
            if($hours==1){
                return "an hour ago";
            }else{
                return "$hours hrs ago";
            }
        }
        //Days
        else if($days <= 7){
            if($days==1){
                return "yesterday";
            }else{
                return "$days days ago";
            }
        }
        //Weeks
        else if($weeks <= 4.3){
            if($weeks==1){
                return "a week ago";
            }else{
                return "$weeks weeks ago";
            }
        }
        //Months
        else if($months <=12){
            if($months==1){
                return "a month ago";
            }else{
                return "$months months ago";
            }
        }
        //Years
        else{
            if($years==1){
                return "one year ago";
            }else{
                return "$years years ago";
            }
        }
    }

    function GenerateRandomColor()
    {
        $color = '#';
        $colorHexLighter = array("9","A","B","C","D","E","F" );
        for($x=0; $x < 6; $x++):
            $color .= $colorHexLighter[array_rand($colorHexLighter, 1)]  ;
        endfor;
        return substr($color, 0, 7);
    }

    public function previousPost(){
        $blogData = $this->blogRepository->getPost(request()->slug);
        $blogPreviousPost = $this->blogRepository->getPreviousPost($blogData->id);
//        dd($blogPreviousPost);
//        dd($blogPreviousPost->url_key);
        // $blogPreviousPostData = $this->blogRepository->getPost($blogPreviousPost->url_key);
        $blogPreviousPostData = $blogPreviousPost;
        $blogData = $blogPreviousPostData;
        if(empty($blogData)){
            return redirect(url('blog'));
        }
        $blogRelatedPosts = $this->blogRepository->relatedCategories($blogPreviousPostData->category,$blogPreviousPostData->id);
        $getRecentPosts = $this->blogRepository->getRecentPosts();
        $comments = $this->commentRepository->getComments($blogData->id);
        $i = 0;
        foreach ($comments as $comment) {
            $ago = $this->timeAgo($comment['comment_date']);
            $comments[$i]['comment_date'] = $ago;
            $i++;
        }
        
        for ($i=0; $i < 99; $i++) { 
            $colors[$i] = $this->GenerateRandomColor();
        }
        return view('shop::blog.content.detail', compact('blogData','getRecentPosts','blogRelatedPosts','comments','colors'));
    }

    /**
     * loads the home page for the storefront
     */
    public function notFound()
    {
        abort(404);
    }

    public function saveComment()
    {
        
        $result = $this->commentRepository->create(request()->all());
        for ($i=0; $i < 99; $i++) { 
            $colors[$i] = $this->GenerateRandomColor();
        }
        $comments = $this->commentRepository->getComments(request()->input('post_id'));
        $ago = $this->timeAgo( $result['comment_date'] );
        $result['comment_date'] = $ago;
        $comment_list = '<div class="single-comment">
                            <div class="media user">
                                <!--User img-->
                                <div class="media-left"> 
                                    <div class="profile_label" style="background-color: '.$colors[rand(0,99)].'">';
                                       
                                            $full_name = explode(' ', $result['full_name']);
                                            $name_key = '';
                                            
                                            foreach ($full_name as $name) {
                                                $name_key .= substr($name, 0, 1);
                                            }
                                        
        $comment_list .=            $name_key.'</div>
                                </div>
                                <!--comment-->
                                <div class="media-body comment-detail">
                                <h5>'.$result['full_name'].' <span class="comment-date">'.$result['comment_date'].'</span></h5>
                                <p class="comment">'.$result['comment'].'</p>
                                </div>
                            </div>
                        </div>';
        echo json_encode([
            'total_comments' => count($comments),
            'list'           => $comment_list
        ]);
    }
}