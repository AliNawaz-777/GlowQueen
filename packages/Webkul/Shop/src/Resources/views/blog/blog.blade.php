<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 1/1/2020
 * Time: 1:22 PM
 */
?>
<link rel="stylesheet" href="{{ bagisto_asset('css/blog-style.css') }}">
<link rel="stylesheet" href="{{ bagisto_asset('css/bootstrap.min.css') }}">

<div class="blog-title">
    <h1 class="title page-title">{{ $page->page_title }}</h1>
    <ul class="trail-items">
        <li class="trail-item trail-begin"><a href="{{ URL('/') }}"><span>Home</span></a></li>
        <li class="trail-item trail-end current"><span>Blog</span></li>
    </ul>
</div>
<section class="blog-content-wrap">
    <div class="container">
        <div class="row align-items-start">
            <div class="col-lg-8 col-md-8 col-sm-12">
                {{--@dump(json_encode($blogData))--}}
                @if (count($blogData) > 0)
                    @foreach($blogData as $article)
                        @include('shop::blog.content.article',['article' => $article])
                    @endforeach
                @else
                    <h4 style="text-align:center;">No Posts</h4>
                @endif
                
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="blog-search blog-widget">
                    <div class="input-wrap">
                        <form method="get" action="{{ url('/blog') }}" >
                        <input type="text" name="q" placeholder="Search Posts...."/>
                        <button type="submit"><img src="{{ bagisto_asset('images/icon-search-white.svg') }}" alt="icon"></button>
                        </form>
                    </div>
                </div>
                <div class="blog-widget blog-products">
                    <h2 class="widget-title">Recent Posts</h2>
                    <ul class="recent-posts">
                        @foreach($getRecentPosts as $post)
                        <li><a target="_blank" href="{{ route('shop.blog.detail',['slug' => $post->url_key]) }}">{{ $post->blog_title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="blog-widget blog-products hide">
                    <h2 class="widget-title">Categories</h2>
                    <ul class="blog-product-listing  category-listing-wrap">
                        @php
                            $categories = \Webkul\Category\Models\Category::where(['parent_id'=>NULL])->get();
                        @endphp
                        @foreach ($categories as $cat)
                            @php
                                $ct = \Webkul\Category\Models\CategoryTranslation::where('id','=',$cat->id)->where('slug','!=','root')->get();
                            @endphp
                            @foreach ($ct as $c)
                            <li>
                                <a class="blog-product-listing-inner category-listing" href="?category={{ $c->id }}">
                                    {{ $c->name }}
                                </a>
                            </li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            </div>
                {{ $blogData->links() }}
        </div>
    </div>
</section>
