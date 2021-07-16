@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.blog.details.view-title', ['blog_title' => $blogs->blog_title]) }}
@stop

@section('content-wrapper')

    <div class="content full-page">

        <div class="page-header">

            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>

                    {{ __('admin::app.blog.details.view-title', ['blog_title' => $blogs->blog_title]) }}
                </h1>
            </div>

            <div class="page-action">
                @if ($blogs->status == 'pending')
                    <a href="{{ route('admin.blog.cancel', $blogs->id) }}" class="btn btn-lg btn-primary" v-alert:message="'{{ __('admin::app.blog.details.cancel-confirm-msg') }}'">
                        {{ __('admin::app.blog.details.cancel-btn-title') }}
                    </a>
                    <a href="{{ route('admin.blog.confirm', $blogs->id) }}" class="btn btn-lg btn-primary" v-alert:message="'{{ __('admin::app.blog.details.confirm-msg') }}'">
                        {{ __('admin::app.blog.details.confirm-btn-title') }}
                    </a>
                    <a href="{{ route('admin.blog.delete', $blogs->id) }}" class="btn btn-lg btn-primary" v-alert:message="'{{ __('admin::app.blog.details.delete-msg') }}'">
                        {{ __('admin::app.blog.details.delete-btn-title') }}
                    </a>
                @elseif($blogs->status == 'confirm')
                    <a href="{{ route('admin.blog.cancel', $blogs->id) }}" class="btn btn-lg btn-primary" v-alert:message="'{{ __('admin::app.blog.details.cancel-confirm-msg') }}'">
                        {{ __('admin::app.blog.details.cancel-btn-title') }}
                    </a>
                    <a href="{{ route('admin.blog.delete', $blogs->id) }}" class="btn btn-lg btn-primary" v-alert:message="'{{ __('admin::app.blog.details.delete-msg') }}'">
                        {{ __('admin::app.blog.details.delete-btn-title') }}
                    </a>
                    <a href="{{ route('admin.blog.edit', $blogs->id) }}" class="btn btn-lg btn-primary">
                        Edit
                    </a>
                @else
                    <a href="{{ route('admin.blog.confirm', $blogs->id) }}" class="btn btn-lg btn-primary" v-alert:message="'{{ __('admin::app.blog.details.confirm-msg') }}'">
                        {{ __('admin::app.blog.details.confirm-btn-title') }}
                    </a>        
                    <a href="{{ route('admin.blog.delete', $blogs->id) }}" class="btn btn-lg btn-primary" v-alert:message="'{{ __('admin::app.blog.details.delete-msg') }}'">
                        {{ __('admin::app.blog.details.delete-btn-title') }}
                    </a>
                    <a href="{{ route('admin.blog.edit', $blogs->id) }}" class="btn btn-lg btn-primary">
                        Edit
                    </a>
                @endif
            </div>
        </div>

        <div class="page-content">

                    <div class="sale-container">

                        <accordian :title="'General'" :active="true">
                            <div slot="body">

                                <div class="sale-section">
                                    <div class="section-content">
                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.blog.details.title') }}
                                            </span>

                                            <span class="value">
                                                {{ $blogs->blog_title }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.blog.details.status') }}
                                            </span>

                                            <span class="value">
                                                {{ $blogs->status }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.blog.details.blog-date') }}
                                            </span>

                                            <span class="value">
                                                {{ $blogs->blog_date }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="sale-section">
                                    <div class="secton-title">
                                        <span>{{ __('admin::app.blog.details.description-title') }}</span>
                                    </div>

                                    <div class="section-content">
                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.blog.details.short-description') }}
                                            </span>

                                            <span class="value">
                                                {{ html_entity_decode(strip_tags($blogs->short_description)) }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.blog.details.long-description') }}
                                            </span>

                                            <span class="value">
                                                {{ html_entity_decode(strip_tags($blogs->blog_description)) }}
                                            </span>
                                        </div>

                                    </div>
                                </div>
                                <div class="sale-section">
                                    <div class="secton-title">
                                        <span>{{ __('admin::app.blog.details.images-title') }}</span>
                                    </div>

                                    <div id="thumbnail-slider" style="display:none;">
                                        <div class="inner">
                                            <ul>
                                                @foreach ($images as $image)
                                                  <li>
                                                    <a class="thumb" href="{{ asset('storage/'.$image) }}"></a>
                                                    </li>  
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div id="closeBtn">CLOSE</div>
                                    </div>

                                    <div class="img-container">
                                        <ul id="myGallery">
                                            @foreach ($images as $image)
                                                <li><img src="{{ asset('storage/'.$image) }}" /></li>
                                            @endforeach
                                        </ul>
                                                                              
                                    </div>
                                </div>
                                <div class="sale-section">
                                    <div class="secton-title">
                                        <span>{{ __('admin::app.blog.details.comment-title') }}</span>
                                    </div>

                                    
                                    <div class="comment-wrapper">
                                        @if (count($comments) > 0)
                                           <ul>
                                                @foreach ($comments as $comment)
                                                <li>
                                                    <div class="comment-block">
                                                        <div class="comment">
                                                            {{ $comment->comment }}
                                                            <div class="posted-by">
                                                                Posted By: <span>{{ $comment->full_name }}, {{ date('d/M/Y', strtotime($comment->comment_date)) }}</span>
                                                            </div>
                                                        </div> 
                                                        <div class="delete-comment">
                                                            <a href="{{ route('admin.blog.comment.delete', $comment->id )}}" title="Delete Comment" v-alert:message="'{{ __('admin::app.blog.details.delete_comment') }}'"><span class="icon trash-icon"></span></a>
                                                            <div class="control-group">
                                                                <label class="switch">
                                                                    @if ($comment->status == 1)
                                                                        @php
                                                                            $selected = 'selected';
                                                                        @endphp
                                                                    @else
                                                                        @php
                                                                            $selected = '';
                                                                        @endphp
                                                                    @endif
                                                                    <input type="checkbox" class="control change_comment_status" id="{{ $comment->id }}" name="comment_status" {{ $selected }} value="{{ $comment->status }}">
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </div>
                                                        </div>                                                       
                                                    </div>
                                                </li>                                                
                                                @endforeach
                                                
                                            </ul> 
                                        @else
                                            <div style="text-align:center;">
                                                <p>There is no comment on this post.</p>
                                            </div>
                                        @endif
                                        
                                    </div>
                                </div>

                            </div>
                        </accordian>

                    </div>
                
        </div>

    </div>
    <script>
        //Note: this script should be placed at the bottom of the page, or after the slider markup. It cannot be placed in the head section of the page.
        var thumbSldr = document.getElementById("thumbnail-slider");
        var closeBtn = document.getElementById("closeBtn");
        var galleryImgs = document.getElementById("myGallery").getElementsByTagName("li");
        for (var i = 0; i < galleryImgs.length; i++) {
            galleryImgs[i].index = i;
            galleryImgs[i].onclick = function (e) {
                var li = this;
                thumbSldr.style.display = "block";
                mcThumbnailSlider.init(li.index);
            };
        }

        thumbSldr.onclick = closeBtn.onclick = function (e) {
            //This event will be triggered only when clicking the area outside the thumbs or clicking the CLOSE button
            thumbSldr.style.display = "none";
        };
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
    <script>
        $(document).on('change','.change_comment_status', function(){
           var id = $(this).attr('id');
           $.ajax({
               url: '{{url("admin/blog/change/comment/status")}}',
               type: 'post',
               data: {id:id},
               headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                }
           })
        })
    </script>
@stop
