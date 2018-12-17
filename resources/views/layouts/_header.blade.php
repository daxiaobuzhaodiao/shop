<header class="shop-header px-4 mb-3">
    <div class="row flex-nowrap justify-content-between align-items-center" style="height:4rem; line-height:2rem">
        <div class="col-4 pt-1">
            <a class="text-muted" href="#">Subscribe</a>
        </div>
        <div class="col-4 text-center">
        <a class="shop-header-logo text-dark" href="#">Large</a>
        </div>
        <div class="col-4 d-flex justify-content-end align-items-center">
        {{-- <a class="text-muted" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-3"><circle cx="10.5" cy="10.5" r="7.5"></circle><line x1="21" y1="21" x2="15.8" y2="15.8"></line></svg>
        </a> --}}
        @guest
            <a class="btn btn-sm btn-outline-secondary mr-3" href="{{ route('login') }}">登陆</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('register') }}">注册</a>
        @else
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span><img src="thumbs/default.jpg" alt="" style="width:40px; height:40px; border-radius:50%"></span>&nbsp;
                {{ auth()->user()->name }}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                </form>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">退出登陆</a>
                <a class="dropdown-item" href="#">Another action</a>
            </div>
        @endguest
        </div>
    </div>
</header>