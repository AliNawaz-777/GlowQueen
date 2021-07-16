@extends('shop::layouts.master')

@php
    $channel = core()->getCurrentChannel();

    $homeSEO = $channel->home_seo;

    if (isset($homeSEO)) {
    $homeSEO = json_decode($channel->home_seo);

    $metaTitle = $homeSEO->meta_title;

    $metaDescription = $homeSEO->meta_description;

    $metaKeywords = $homeSEO->meta_keywords;
    }
@endphp

@section('page_title')
    {{ isset($blogData) ? $blogData->meta_title : "" }}
@endsection

@section('head')

    @if (isset($homeSEO))
        @isset($blogData->meta_title)

            <meta name="title" content="{{ $blogData->meta_title }}" />
        @endisset

        @isset($blogData->meta_description)
            <meta name="description" content="{{ $blogData->short_description }}" />
        @endisset

        @isset($blogData->meta_keyword)
            <meta name="keywords" content="{{ $blogData->meta_keyword }}" />
        @endisset
    @endif
    <link rel="stylesheet" href="{{ bagisto_asset('css/blog-style.css') }}">
    <link rel="stylesheet" href="{{ bagisto_asset('css/bootstrap.min.css') }}">
@endsection

@section('content-wrapper')
    {!! view_render_event('bagisto.shop.home.content.before') !!}
    <div class="blog-title">
        <h1 class="title page-title">{{ $blogData->blog_title }}</h1>
        <ul class="trail-items">
            <li class="trail-item trail-begin"><a href="{{ URL('/') }}"><span>Home</span></a></li>
            <li class="trail-item trail-end current"><span>Blog</span></li>
        </ul>
    </div>
    <section class="blog-content-wrap blog-detail-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <article class="blog-post-wrap">
                        <div class="blog-date">
                            <div class="posted-day-month"><span class="date">{{ date("d", strtotime($blogData->blog_date))}}</span> <span class="month">{{ date("M", strtotime($blogData->blog_date))}}</span></div>
                            <span class="posted-year">{{ date("Y", strtotime($blogData->blog_date))}}</span>
                        </div>
                        <div class="post_thumnail">
                            @if(isset($blogData->images[0]) )
                            <img src="{{ asset('storage/'.$blogData->images[0]->path) }}" alt="blog-post">
                            @endif
                        </div>
                        <div class="blog-deatail-wrap">
                            <div class="author-wrap">
                                <div class="img_wrap">
                                    <img src="https://secure.gravatar.com/avatar/d74fb84ec5b7bf3f309fa080e6b199ea?s=100&d=mm&r=g" alt="">
                                </div>
                                <a href="javascript:void(0);" class="author-name">{{ !empty($blogData->user->name) ? $blogData->user->name : 'User' }}</a>
                                <div class="comments"><img src="{{ bagisto_asset('images/chat-icon.svg') }}" alt=""> 0</div>
                            </div>
                            <div class="blog-content-outer">
                                <p>{!! $blogData->blog_description !!}</p>
                                <a class="previous-post" href="{{ route('shop.blog.previousPost',['slug' => $blogData->url_key]) }}" ><img src="{{ bagisto_asset('images/left-arrow.svg') }}" alt="">  Previous Post</a>
                            </div>
                        </div>
                    </article>
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
                        <h2 class="widget-title">Related Posts</h2>
                        <ul class="blog-product-listing">
                            @foreach($blogRelatedPosts as $post)
                            <li>
                                <a class="blog-product-listing-inner" href="{{ route('shop.blog.detail',['slug' => $post->url_key]) }}">
                                    <div class="img-wrap">
                                         @if(isset($post->images[0]) )
                                        <img src="{{ asset('cache/small/'.$post->images[0]->path) }}" alt="">
                                        @endif
                                    </div>
                                    <div class="product-detail">
                                        <span class="product-title">{{ $post->blog_title }}</span>
                                        <span class="product-price">{{ $post->blog_date }}</span>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="blog-widget blog-products">
                        <h2 class="widget-title">Recent Posts</h2>
                        <ul class="recent-posts">
                            @foreach($getRecentPosts as $post)
                                <li><a target="_blank" href="{{ route('shop.blog.detail',['slug' => $post->url_key]) }}">{{ $post->blog_title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="post-comment-section section-gap">
        <div class="container">

            <!--comment form--->  
            <div class="comment-form-wrap wow fadeInRight">
                <form class="comment-form" id="comment_form" method="POST" action="#">
                    <h4>Leave a Reply</h4>
                    <div class="row">
                        <div id="comment_msg" style="background: #2bb435;color: #fff;margin: 5px 15px;padding: 10px;width: 100%;display:none;">Your comment is successfully recieved. It will dispaly after ADMIN approval.</div>
                        <div class="col-md-6">
                            @if (auth()->guard('customer')->check())
                                @php
                                    $name = auth()->guard('customer')->user()->first_name.' '.auth()->guard('customer')->user()->last_name;
                                    $email = auth()->guard('customer')->user()->email;
                                @endphp
                            @else
                                @php
                                    $name = '';
                                    $email = '';
                                @endphp
                            @endif
                            <div class="form-group">
                            <input type="text" class="form-control" id="name" placeholder="Name" name="full_name" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Name'" value="{{ $name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email'" value="{{ $email }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control mb-10" rows="5" name="comment" placeholder="Messege" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Message'" required></textarea>
                    </div>
                    <input type="hidden" name="post_id" value="{{ $blogData->id }}">
                    <div class="text-right">
                        <button type="submit" id="submit_btn" class="continue-reading"><span>Comment</span></button>
                    </div>
                </form>
            </div>
            <!--End of Leave a comment form--->  


            <div class="comments-area wow fadeInLeft">
                <h3 id="total_comment">{{ count($comments) }} Comments</h3>
                <div class="comment-list" id="comment-list">
                    @if (count($comments) > 0)
                    
                        @foreach ($comments as $comment)
                           <!--Single comment-->  
                            <div class="single-comment">
                                <div class="media user">
                                    <!--User img-->
                                    <div class="media-left"> 
                                        <div class="profile_label" style="background-color: {{ $colors[rand(0,99)] }}">
                                            @php
                                                $full_name = explode(' ', $comment->full_name);
                                                $name_key = '';
                                                
                                                foreach ($full_name as $name) {
                                                    $name_key .= substr($name, 0, 1);
                                                }

                                                echo $name_key;
                                            @endphp
                                        </div>
                                    </div>
                                    <!--comment-->
                                    <div class="media-body comment-detail">
                                    <h5>{{ $comment->full_name }} <span class="comment-date">{{ $comment->comment_date }}</span></h5>
                                    <p class="comment">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            </div>
                            <!--End of Single comment--> 
                        @endforeach

                    @else
                        <div id="no-comment" style="text-align:center;">
                            <p>No comment for this post</p>
                        </div>
                    @endif
                </div>
            </div>


        </div>
    </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(document).on('submit','#comment_form', function(e){
            e.preventDefault();
            var data = $('#comment_form').serialize();
            $("#submit_btn").prop('disabled', true);
            $('#comment_form')[0].reset();
            $.ajax({
                type: 'post',
                data: data,
                url: "{{url('comment/save')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response){
                    var result = JSON.parse(response)
                    $('#total_comment').html(result.total_comments+" Comments");
                    if ($("#no-comment").is(":visible")) {
                        $("#no-comment").css('display', 'none')
                    }
                    $('#comment_msg').css('display','block');
                    setTimeout(function(){
                        $('#comment_msg').css('display','none');
                    }, 5000);
                    // $('#comment-list').prepend(result.list);
                }
            })
        })
    </script>

    {{ view_render_event('bagisto.shop.home.content.after') }}
    
@endsection

