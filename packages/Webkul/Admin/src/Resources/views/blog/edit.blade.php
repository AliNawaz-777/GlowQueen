@extends('admin::layouts.content')

@section('page_title')
    Edit Post
@stop

@section('content')

    <div class="content">
        <form method="POST" action="{{ route('admin.blog.update') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <input type="hidden" value="{{ $blogs->id }}" name="blog_id">
            <input type="hidden" value="{{ $blogs->status }}" name="status">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>

                        Edit Post
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.blog.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()

                    {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.general.before') !!}

                    <accordian :title="'{{ __('admin::app.catalog.attributes.general') }}'" :active="true">
                        <div slot="body">

                            {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.general.controls.before') !!}

                            <div class="control-group" :class="[errors.has('title') ? 'has-error' : '']">
                                <label for="title" class="required">{{ __('admin::app.blog.blog-title') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="title" name="title" value="{{ $blogs->blog_title }}"  data-vv-as="&quot;{{ __('admin::app.blog.blog-title') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                            </div>
                            {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.general.controls.after') !!}
                            {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.general.controls.before') !!}

                            <div class="control-group" :class="[errors.has('url_key') ? 'has-error' : '']">
                                <label for="blog-slug" class="required">{{ __('admin::app.blog.blog-slug') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="url_key" name="url_key" value="{{ $blogs->url_key }}"  data-vv-as="&quot;{{ __('admin::app.blog.blog-slug') }}&quot;" v-code/>
                                <span class="control-error" v-if="errors.has('url_key')">@{{ errors.first('url_key') }}</span>
                            </div>
                            {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.general.controls.after') !!}
                            {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.general.controls.before') !!}

                            <!--<div class="control-group" :class="[errors.has('tags-1') ? 'has-error' : '']">-->
                            <!--    <label for="tags">{{ __('admin::app.blog.blog-tags') }}</label>-->
                            <!--    <input id="form-tags-1" v-validate="'required'" class="control" name="tags-1" type="text" value="url_key" data-vv-as="&quot;{{ __('admin::app.blog.blog-tags') }}&quot;">-->
                            <!--    <span class="control-error" v-if="errors.has('tags-1')">@{{ errors.first('tags-1') }}</span>-->
                            <!--</div>-->

                            <div class="control-group" :class="[errors.has('blog-date') ? 'has-error' : '']">
                                <label for="datepicker" class="required">Blog Date</label>
                                <input type="text" id="datepicker" class="control" name="blog-date" value="{{ $blogs->blog_date }}" v-validate="'required'" data-vv-as="&quot;Blog Date&quot;">
                                <span class="control-error" v-if="errors.has('blog-date')">@{{ errors.first('blog-date') }}</span>
                            </div>

                            {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.general.controls.after') !!}

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.general.after') !!}

                    {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.label.before') !!}

                    <accordian :title="'{{ __('admin::app.blog.description-title') }}'" :active="true">
                        <div slot="body">

                            {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.label.controls.before') !!}

                            <div class="control-group" :class="[errors.has('blog_short_description') ? 'has-error' : '']">
                                <label for="blog_short_description" class="required">Short Description</label>
                                <textarea v-validate="'required'" class="control" id="blog_short_description" name="blog_short_description" data-vv-as="&quot;Short Description&quot;">{{ $blogs->short_description }}</textarea>
                                <span class="control-error" v-if="errors.has('blog_short_description')">@{{ errors.first('blog_short_description') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('blog_description') ? 'has-error' : '']">
                                <label for="blog_description" class="required">{{ __('admin::app.blog.description-title') }}</label>
                                <textarea v-validate="'required'" class="control" id="blog_description" name="blog_description" data-vv-as="&quot;{{ __('admin::app.blog.description-title') }}&quot;">{{ $blogs->blog_description }}</textarea>
                                <span class="control-error" v-if="errors.has('blog_description')">@{{ errors.first('blog_description') }}</span>
                            </div>

                            {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.label.controls.after') !!}

                        </div>
                    </accordian>
                    
                    <accordian :title="'Meta Description'" :active="true">
                        <div slot="body">
                            
                            <div class="control-group" :class="[errors.has('meta_title') ? 'has-error' : '']">
                                <label for="title" class="required">Meta Title</label>
                                <input type="text" v-validate="'required'" class="control" id="meta_title" name="meta_title" value="{{ $blogs->meta_title }}"  data-vv-as="&quot;Meta Title&quot;"/>
                                <span class="control-error" v-if="errors.has('meta_title')">@{{ errors.first('meta_title') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('meta_description') ? 'has-error' : '']">
                                <label for="title" class="required">Meta Description</label>
                                <textarea v-validate="'required'" class="control" id="meta_description" name="meta_description" data-vv-as="&quot;Meta Description&quot;">{{ $blogs->meta_description }}</textarea>
                                <span class="control-error" v-if="errors.has('meta_description')">@{{ errors.first('meta_description') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('meta_keyword') ? 'has-error' : '']">
                                <label for="title" class="required">Meta Keyword</label>
                                <input type="text" v-validate="'required'" class="control" id="meta_keyword" name="meta_keyword" value="{{ $blogs->meta_keyword }}"  data-vv-as="&quot;Meta Keyword&quot;"/>
                                <span class="control-error" v-if="errors.has('meta_keyword')">@{{ errors.first('meta_keyword') }}</span>
                            </div>
                        </div>
                    </accordian>

                    <accordian :title="'{{ __('admin::app.catalog.products.images') }}'" :active="true">
                        <div slot="body">
                    
                            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.images.controls.before') !!}
                    
                            <div class="control-group {!! $errors->has('images.*') ? 'has-error' : '' !!}">
                                <label>{{ __('admin::app.catalog.categories.image') }}</label>
                    
                                <image-wrapper :button-label="'{{ __('admin::app.catalog.products.add-image-btn-title') }}'" input-name="images" :images='@json($images)'></image-wrapper>
                    
                                <span class="control-error" v-if="{!! $errors->has('images.*') !!}">
                                    @php $count=1 @endphp
                                    @foreach ($errors->get('images.*') as $key => $message)
                                        @php echo str_replace($key, 'Image'.$count, $message[0]); $count++ @endphp
                                    @endforeach
                                </span>
                            </div>
                    
                            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.images.controls.after') !!}
                    
                        </div>
                    </accordian>

                    @if ($categories->count())

                        <accordian :title="'{{ __('admin::app.catalog.products.categories') }}'" :active="true">
                            <div slot="body">
                                
                                {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.categories.controls.before') !!}

                                <tree-view behavior="normal" value-field="id" name-field="categories" input-type="checkbox" items='@json($categories)' value='@json($blogs->c_ids)'></tree-view>
                                {{-- <tree-view behavior="normal" value-field="id" name-field="categories" input-type="checkbox" items='@json($categories)' value=''></tree-view> --}}

                                {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.categories.controls.after') !!}

                            </div>
                        </accordian>

                    @endif

                    {!! view_render_event('bagisto.admin.catalog.attribute.create_form_accordian.label.after') !!}

                </div>
            </div>

        </form>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="options-template">
        <div>

            <div class="control-group" v-if="show_swatch">
                <label for="swatch_type">{{ __('admin::app.catalog.attributes.swatch_type') }}</label>
                <select class="control" id="swatch_type" name="swatch_type" v-model="swatch_type">
                    <option value="dropdown">
                        {{ __('admin::app.catalog.attributes.dropdown') }}
                    </option>

                    <option value="color">
                        {{ __('admin::app.catalog.attributes.color-swatch') }}
                    </option>

                    <option value="image">
                        {{ __('admin::app.catalog.attributes.image-swatch') }}
                    </option>

                    <option value="text">
                        {{ __('admin::app.catalog.attributes.text-swatch') }}
                    </option>
                </select>
            </div>

            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th v-if="show_swatch && (swatch_type == 'color' || swatch_type == 'image')">{{ __('admin::app.catalog.attributes.swatch') }}</th>

                            <th>{{ __('admin::app.catalog.attributes.admin_name') }}</th>

                            @foreach (app('Webkul\Core\Repositories\LocaleRepository')->all() as $locale)

                                <th>{{ $locale->name . ' (' . $locale->code . ')' }}</th>

                            @endforeach

                            <th>{{ __('admin::app.catalog.attributes.position') }}</th>

                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="row in optionRows">
                            <td v-if="show_swatch && swatch_type == 'color'">
                                <swatch-picker :input-name="'options[' + row.id + '][swatch_value]'" :color="row.swatch_value" colors="text-advanced" show-fallback />
                            </td>

                            <td v-if="show_swatch && swatch_type == 'image'">
                                <input type="file" accept="image/*" :name="'options[' + row.id + '][swatch_value]'"/>
                            </td>

                            <td>
                                <div class="control-group" :class="[errors.has(adminName(row)) ? 'has-error' : '']">
                                    <input type="text" v-validate="'required'" v-model="row['admin_name']" :name="adminName(row)" class="control" data-vv-as="&quot;{{ __('admin::app.catalog.attributes.admin_name') }}&quot;"/>
                                    <span class="control-error" v-if="errors.has(adminName(row))">@{{ errors.first(adminName(row)) }}</span>
                                </div>
                            </td>

                            @foreach (app('Webkul\Core\Repositories\LocaleRepository')->all() as $locale)
                                <td>
                                    <div class="control-group" :class="[errors.has(localeInputName(row, '{{ $locale->code }}')) ? 'has-error' : '']">
                                        <input type="text" v-validate="'{{ app()->getLocale() }}' == '{{ $locale->code }}' ? 'required': ''"  v-model="row['{{ $locale->code }}']" :name="localeInputName(row, '{{ $locale->code }}')" class="control" data-vv-as="&quot;{{ $locale->name . ' (' . $locale->code . ')' }}&quot;"/>
                                        <span class="control-error" v-if="errors.has(localeInputName(row, '{{ $locale->code }}'))">@{{ errors.first(localeInputName(row, '{!! $locale->code !!}')) }}</span>
                                    </div>
                                </td>
                            @endforeach

                            <td>
                                <div class="control-group" :class="[errors.has(sortOrderName(row)) ? 'has-error' : '']">
                                    <input type="text" v-validate="'required|numeric'" :name="sortOrderName(row)" class="control" data-vv-as="&quot;{{ __('admin::app.catalog.attributes.position') }}&quot;"/>
                                    <span class="control-error" v-if="errors.has(sortOrderName(row))">@{{ errors.first(sortOrderName(row)) }}</span>
                                </div>
                            </td>

                            <td class="actions">
                                <i class="icon trash-icon" @click="removeRow(row)"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-lg btn-primary mt-20" id="add-option-btn" @click="addOptionRow()">
                {{ __('admin::app.catalog.attributes.add-option-btn-title') }}
            </button>
        </div>
    </script>

    <script>
        $(document).ready(function () {
            $('#type').on('change', function (e) {
                if (['select', 'multiselect', 'checkbox'].indexOf($(e.target).val()) === -1) {
                    $('#options').parent().addClass('hide')
                } else {
                    $('#options').parent().removeClass('hide')
                }
            })
        });


        Vue.component('option-wrapper', {

            template: '#options-template',

            inject: ['$validator'],

            data: function() {
                return {
                    optionRowCount: 0,
                    optionRows: [],
                    show_swatch: false,
                    swatch_type: ''
                }
            },

            created: function () {
                var this_this = this;

                $(document).ready(function () {
                    $('#type').on('change', function (e) {
                        if (['select'].indexOf($(e.target).val()) === -1) {
                            this_this.show_swatch = false;
                        } else {
                            this_this.show_swatch = true;
                        }
                    });
                });
            },

            methods: {
                addOptionRow: function () {
                    var rowCount = this.optionRowCount++;
                    var row = {'id': 'option_' + rowCount};

                    @foreach (app('Webkul\Core\Repositories\LocaleRepository')->all() as $locale)
                        row['{{ $locale->code }}'] = '';
                    @endforeach

                    this.optionRows.push(row);
                },

                removeRow: function (row) {
                    var index = this.optionRows.indexOf(row)
                    Vue.delete(this.optionRows, index);
                },

                adminName: function (row) {
                    return 'options[' + row.id + '][admin_name]';
                },

                localeInputName: function (row, locale) {
                    return 'options[' + row.id + '][' + locale + '][label]';
                },

                sortOrderName: function (row) {
                    return 'options[' + row.id + '][sort_order]';
                }
            }
        })
    </script>

<script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

<script>
    $(document).ready(function () {
        tinymce.init({
            selector: 'textarea#blog_description, textarea#blog_short_description, textarea#meta_description',
            height: 200,
            width: "100%",
            plugins: 'image imagetools media wordcount save fullscreen code',
            toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code',
            image_advtab: true
        });
    });
</script>
@endpush
