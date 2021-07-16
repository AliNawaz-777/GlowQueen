<?php

namespace Webkul\CMS\Http\Controllers\Shop;

use Webkul\CMS\Http\Controllers\Controller;
use Webkul\CMS\Repositories\CMSRepository as CMS;
use Webkul\Core\Repositories\ChannelRepository as Channel;
use Webkul\Core\Repositories\LocaleRepository as Locale;
use Webkul\Product\Models\Blog;
/**
 * PagePresenter controller
 *
 * @author  Arhamsoft (info@arhamsoft.com)
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
 class PagePresenterController extends Controller
{
    /**
     * To hold the request variables from route file
     */
    protected $_config;

    /**
     * To hold the channel reposotry instance
     */
    protected $channel;

    /**
     * To hold the locale reposotry instance
     */
    protected $locale;

    /**
     * To hold the CMSRepository instance
     */
    protected $cms;

    public function __construct(Channel $channel, Locale $locale, CMS $cms)
    {
        /**
         * Channel repository instance
         */
        $this->channel = $channel;

        /**
         * Locale repository instance
         */
        $this->locale = $locale;

        /**
         * CMS repository instance
         */
        $this->cms = $cms;

        $this->_config = request('_config');
    }

    /**
     * To extract the page content and load it in the respective view file\
     *
     * @return view
     */
    public function presenter($slug)
    {
        $currentChannel = core()->getCurrentChannel();
        $currentLocale = app()->getLocale();

        $currentLocale = $this->locale->findOneWhere([
            'code' => $currentLocale
        ]);

        $page = $this->cms->findOneWhere([
            'url_key' => $slug,
            'locale_id' => $currentLocale->id,
            'channel_id' => $currentChannel->id
        ]);
//        dd($page);
        if ($page) {
            if ($page->url_key == "blog") {
                $blogData = false;
                if(isset($_GET['q'])){
                    $blogData = Blog::with('images')
                        ->with('user')
                        ->where('blog_title', 'like', '%' . $_GET['q'] . '%')
                        ->where('status','=','confirm')
                        ->orderBy('id', 'desc')
                            ->paginate(10);
                }else if(isset($_GET['category'])){
                    $category = $_GET['category'];
                    $blogData = Blog::with('images')
                        ->with('user')
                        ->where('category', 'like', '%' . $category . '%')
                        ->where('status','=','confirm')
                        ->orderBy('id', 'desc')
                            ->paginate(10);
                }
                else {
                    $blogData = Blog::with('images')
                        ->orderBy('id', 'desc')
                        ->where('status','=','confirm')
                        ->with('user')
                        ->paginate(10);
                }
//                dd($blogData);
                $getRecentPosts = Blog::orderBy('id', 'desc')->where('status','=','confirm')->take(10)->get();
                return view('shop::blog.index', compact('page','blogData','getRecentPosts'));
            }else{
                return view('shop::cms.page')->with('page', $page);
            }
        }else {
            abort(404);
        }

    }
}