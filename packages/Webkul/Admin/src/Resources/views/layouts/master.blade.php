<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <title>@yield('page_title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" sizes="16x16" href="{{ asset('vendor/webkul/ui/assets/images/favicon.ico') }}" />
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/admin.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/thumbnail-slider.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/jquery.tagsinput-revisited.css') }}">


        @yield('head')

        @yield('css')

        {!! view_render_event('bagisto.admin.layout.head') !!}

    </head>

    <body @if (core()->getCurrentLocale()->direction == 'rtl') class="rtl" @endif style="scroll-behavior: smooth;">
        {!! view_render_event('bagisto.admin.layout.body.before') !!}

        <div id="app">

            <flash-wrapper ref='flashes'></flash-wrapper>

            {!! view_render_event('bagisto.admin.layout.nav-top.before') !!}

            @include ('admin::layouts.nav-top')

            {!! view_render_event('bagisto.admin.layout.nav-top.after') !!}


            {!! view_render_event('bagisto.admin.layout.nav-left.before') !!}

            @include ('admin::layouts.nav-left')

            {!! view_render_event('bagisto.admin.layout.nav-left.after') !!}


            <div class="content-container">

                {!! view_render_event('bagisto.admin.layout.content.before') !!}

                @yield('content-wrapper')

                {!! view_render_event('bagisto.admin.layout.content.after') !!}

            </div>

        </div>

        <script type="text/javascript">
            window.flashMessages = [];

            @if ($success = session('success'))
                window.flashMessages = [{'type': 'alert-success', 'message': "{{ $success }}" }];
            @elseif ($warning = session('warning'))
                window.flashMessages = [{'type': 'alert-warning', 'message': "{{ $warning }}" }];
            @elseif ($error = session('error'))
                window.flashMessages = [{'type': 'alert-error', 'message': "{{ $error }}" }];
            @elseif ($info = session('info'))
                window.flashMessages = [{'type': 'alert-info', 'message': "{{ $info }}" }];
            @endif

            window.serverErrors = [];
            @if (isset($errors))
                @if (count($errors))
                    window.serverErrors = @json($errors->getMessages());
                @endif
            @endif
        </script>

        <script type="text/javascript" src="{{ asset('vendor/webkul/admin/assets/js/admin.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/webkul/admin/assets/js/thumbnail-slider.js') }}"></script>
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
        <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>
        {{-- <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/thumbnail-slider.js') }}"></script> --}}
        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script>
            $( function() 
            {
            //   $( "#datepicker" ).datepicker();
              var area_id = "{{ !empty($setting) ? $setting->shipper_area : '' }}";
              var city_id = "{{ !empty($setting) ? $setting->shipper_city : '' }}";
             
              getArea(area_id,city_id);
              
              $('#shipper_city').on('change',function()
              {
                if($(this).val())
                {
                   getArea('',$(this).val());
                }
                else{
                    $('#shipper_area').html('');
                }
              })

              $('#track-order-call-courier').on('click',function()
              {
                $.ajax({
                    url: '{{ route("admin.call-courier-setting.track-history") }}',
                    data: {tracking_id:$(this).attr('tracking-id')},
                    type: "GET",
                    success: function (response) 
                    {
                        var html = '<p>';
                        $.each(response[0], function( index, value ) {
                            html += '<p><b>'+index+'</b> : '+value+'</p>';
                        });

                        var link = window.open('', "Order Status", "width=600, height=400, scrollbars=yes");
                        html += '</p>';
                        $(link.document.body).html(html);
                    }
                })
              })

            });

            function getArea(area_id="",city_id="")
            {
                $.ajax({
                    url: '{{ route("admin.call-courier-setting.get-areas") }}',
                    data: {area_id:area_id,city_id:city_id},
                    type: "GET",
                    success: function (response) 
                    {
                        $('#shipper_area').html(response);
                    }
                })
            }
        </script>
       <script type="text/javascript" src="{{ asset('vendor/webkul/admin/assets/js/tags.js') }}"></script>
       <script type="text/javascript">
			$(function() {
                
				$('#form-tags-1').tagsInput();
				
				$('#form-tags-2').tagsInput({
					'onAddTag': function(input, value) {
						console.log('tag added', input, value);
					},
					'onRemoveTag': function(input, value) {
						console.log('tag removed', input, value);
					},
					'onChange': function(input, value) {
						console.log('change triggered', input, value);
					}
				});
				
				$('#form-tags-3').tagsInput({
					'unique': true,
					'minChars': 2,
					'maxChars': 10,
					'limit': 5,
					'validationPattern': new RegExp('^[a-zA-Z]+$')
				});
				
				$('#form-tags-4').tagsInput({
					'autocomplete': {
						source: [
							'apple',
							'banana',
							'orange',
							'pizza'
						]
					} 
				});
				
				$('#form-tags-5').tagsInput({
					'delimiter': ';' 
				});
				
				$('#form-tags-6').tagsInput({
					'delimiter': [',', ';'] 
				});
			});
		</script>
        <script type="text/javascript">
            window.addEventListener('DOMContentLoaded', function() {
                moveDown = 60;
                moveUp =  -60;
                count = 0;
                countKeyUp = 0;
                pageDown = 60;
                pageUp = -60;
                scroll = 0;

                listLastElement = $('.menubar li:last-child').offset();

                if (listLastElement) {
                    lastElementOfNavBar = listLastElement.top;
                }

                navbarTop = $('.navbar-left').css("top");
                menuTopValue = $('.navbar-left').css('top');
                menubarTopValue = menuTopValue;

                documentHeight = $(document).height();
                menubarHeight = $('ul.menubar').height();
                navbarHeight = $('.navbar-left').height();
                windowHeight = $(window).height();
                contentHeight = $('.content').height();
                innerSectionHeight = $('.inner-section').height();
                gridHeight = $('.grid-container').height();
                pageContentHeight = $('.page-content').height();

                if (menubarHeight <= windowHeight) {
                    differenceInHeight = windowHeight - menubarHeight;
                } else {
                    differenceInHeight = menubarHeight - windowHeight;
                }

                if (menubarHeight > windowHeight) {
                    document.addEventListener("keydown", function(event) {
                        if ((event.keyCode == 38) && count <= 0) {
                            count = count + moveDown;

                            $('.navbar-left').css("top", count + "px");
                        } else if ((event.keyCode == 40) && count >= -differenceInHeight) {
                            count = count + moveUp;

                            $('.navbar-left').css("top", count + "px");
                        } else if ((event.keyCode == 33) && countKeyUp <= 0) {
                            countKeyUp = countKeyUp + pageDown;

                            $('.navbar-left').css("top", countKeyUp + "px");
                        } else if ((event.keyCode == 34) && countKeyUp >= -differenceInHeight) {
                            countKeyUp = countKeyUp + pageUp;

                            $('.navbar-left').css("top", countKeyUp + "px");
                        } else {
                            $('.navbar-left').css("position", "fixed");
                        }
                    });

                    $("body").css({minHeight: $(".menubar").outerHeight() + 100 + "px"});

                    window.addEventListener('scroll', function() {
                        documentScrollWhenScrolled = $(document).scrollTop();

                            if (documentScrollWhenScrolled <= differenceInHeight + 200) {
                                $('.navbar-left').css('top', -documentScrollWhenScrolled + 60 + 'px');
                                scrollTopValueWhenNavBarFixed = $(document).scrollTop();
                            }
                    });
                }
            });
        </script>
        @stack('scripts')

        {!! view_render_event('bagisto.admin.layout.body.after') !!}

        <div class="modal-overlay"></div>
    </body>
</html>