<article class="blog-post-wrap">
                    <div class="post_thumnail">
                        @if(isset($article->images[0]))
                        <img src="{{ asset('storage/'.$article->images[0]->path) }}" alt="blog-post">
                        @endif
                    </div>
                    <div class="blog-deatail-wrap">
                        <div class="blog-date">
                            <div class="posted-day-month"><span class="date">{{ date("d", strtotime($article->blog_date))}}</span> <span class="month">{{ date("M", strtotime($article->blog_date))}}</span></div>
                            <span class="posted-year">{{ date("Y", strtotime($article->blog_date))}}</span>
                        </div>
                        <div class="author-wrap">
                            {{-- <div class="img_wrap">
                                <img src="https://secure.gravatar.com/avatar/d74fb84ec5b7bf3f309fa080e6b199ea?s=100&d=mm&r=g" alt="">
                            </div> --}}
                            @php
                                $total_comments = \Webkul\Product\Models\Comment::where(['post_id' => $article->id, 'status'=>1])->get();
                            @endphp
                            <a href="javascript:void(0);" class="author-name">GlowQueen</a>
                            <div class="comments"><img src="{{ bagisto_asset('images/chat-icon.svg') }}" alt=""> {{ count($total_comments) }}</div>
                        </div>
                        <div class="blog-content-outer">
                            <a href="{{ route('shop.blog.detail',['slug' => $article->url_key]) }}" class="title">{{ $article->blog_title }}</a>
                            <p>{!! $article->short_description  !!}</p>
                            <a href="{{ route('shop.blog.detail',['slug' => $article->url_key]) }}" class="continue-reading">Continue Reading</a>
                        </div>
                    </div>
                </article>