<div class="navbar-left">
    <ul class="menubar">

        @foreach ($menu->items as $menuItem)
            @if($menuItem['url'] != url('admin/pwa/pushnotification/create'))
            <li class="menu-item {{ $menu->getActive($menuItem) }}">
                <a href="{{ count($menuItem['children']) ? current($menuItem['children'])['url'] : $menuItem['url'] }}">
                    @if ($menuItem['url'] == url('admin/blog'))
                        @php
                            $comments = \Webkul\Product\Models\Comment::where('status','=', 0)->get();
                        @endphp
                        @if (count($comments) > 0)
                            <span class="notifications">*</span>    
                        @endif
                    @endif
                    <span class="icon {{ $menuItem['icon-class'] }}"></span>
                    
                    <span>{{ trans($menuItem['name']) }}</span>
                </a>
            </li>
            @endif
        @endforeach
    </ul>
</div>