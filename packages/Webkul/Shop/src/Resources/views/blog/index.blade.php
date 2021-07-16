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
{{ isset($page) ? $page->meta_title : "" }}
@endsection

@section('head')

@if (isset($homeSEO))
@isset($page->meta_title)
<meta name="title" content="{{ $page->meta_title }}" />
@endisset

@isset($page->meta_description)
<meta name="description" content="{{ $page->meta_description }}" />
@endisset

@isset($page->meta_keywords)
<meta name="keywords" content="{{ $page->meta_keywords }}" />
@endisset
@endif
@endsection

@section('content-wrapper')
{!! view_render_event('bagisto.shop.home.content.before') !!}
@include('shop::blog.blog',['blogData' => $blogData,'getRecentPosts' => $getRecentPosts])

{{ view_render_event('bagisto.shop.home.content.after') }}

@endsection
