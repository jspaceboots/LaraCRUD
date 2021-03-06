<?php $route = request()->route()->getName(); ?>
<ul class="nav">
    @if (Auth::check())
        @if (config('crud.useSubnav'))
        <li {{in_array($route, config('crud.routing')) ? "class=active" : ''}}>
            <a data-toggle="collapse" data-target="#coreModelsNav" href="#coreModelsNav" class="collapsed">
                <i class="ti-linux"></i>
                <p>CRUD <b class="caret"></b></p>
            </a>
            <div class="collapse" id="coreModelsNav">
                <ul class="nav">
        @endif
                    <li {{$route == 'dashboard' ? "class=active" : ''}}>
                        <a href="{{route('dashboard')}}">
                            <span class="sidebar-mini">DB</span>
                            <span class="sidebar-normal">Dashboard</span>
                        </a>
                    </li>
                    @foreach(config('crud.routing') as $crudRoute => $meta)
                        <li {{$route == $crudRoute ? "class=active" : ''}}>
                            <a href="{{route($meta && array_key_exists('name', $meta) ? $meta['name'] : $crudRoute)}}">
                                <span class="sidebar-mini">{{strtoupper(substr($crudRoute, 0, 2))}}</span>
                                <span class="sidebar-normal">{{ucwords(str_replace('_', ' ', $crudRoute))}}</span>
                            </a>
                        </li>
                    @endforeach
                    <li {{$route === '_newentity' ? 'class=active' : ''}}>
                        <a href="{{route('_newentity')}}">
                            <span class="sidebar-mini"><i class="ti-plus"></i></span>
                            <span class="sidebar-normal">New Entity</span>
                        </a>
                    </li>
        @if (config('crud.useSubnav'))
                </ul>
            </div>
        </li>
        @endif
    @else
        <li class="active">
            <a href="{{ route('login')  }}">
                <i class="ti-user"></i>
                <p>Login</p>
            </a>
        </li>
    @endif
</ul>