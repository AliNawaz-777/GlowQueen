@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.blog.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.testimonail.title') }}</h1>
            </div>

            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        {{ __('admin::app.export.export') }}
                    </span>
                </div>

                <a href="{{ route('admin.testimonail.create') }}" class="btn btn-lg btn-primary">
                    {{ __('admin::app.testimonail.add-testimonail-btn-title') }}

                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('blogGrid', 'Webkul\Admin\DataGrids\TestimonailDataGrid')
            {!! $blogGrid->renderBlogs() !!}
        </div>
    </div>

    <modal id="downloadDataGrid" :is-open="modalIds.downloadDataGrid">
        <h3 slot="header">{{ __('admin::app.export.download') }}</h3>
        <div slot="body">
            <export-form></export-form>
        </div>
    </modal>

@stop

@push('scripts')
    @include('admin::export.export', ['gridName' => $blogGrid])
@endpush