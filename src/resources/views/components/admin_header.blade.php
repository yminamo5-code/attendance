<link rel="stylesheet" href="{{ asset('/css/admin_header.css') }}">
<header class="header">
    <div class="header_inner">
        <div class="header_logo">
            <img src="{{asset('img/COACHTECHヘッダーロゴ.png')}}" alt="ロゴ">
        </div>

        <nav class="header_nav">
            <a href="{{ route('admin.attendance.list') }}" class="nav_btn">勤怠一覧</a>
            <a href="{{ route('admin.staff.list') }}" class="nav_btn">スタッフ一覧</a>
            <a href="{{ route('admin.request.list') }}" class="nav_btn">申請一覧</a>
            <form action="{{ route('logout') }}" method="POST" class="logout_form">
                @csrf
                <button type="submit" class="nav_btn">ログアウト</button>
            </form>
        </nav>
    </div>
</header>