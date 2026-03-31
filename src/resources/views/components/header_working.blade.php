<link rel="stylesheet" href="{{ asset('/css/header_working.css')  }}">
    
<header class="header">
    <div class="header_inner">
        <div class="header_logo">
            <img src="{{asset('img/COACHTECHヘッダーロゴ.png')}}" alt="ロゴ">
        </div>

        <nav class="header_nav">
            <a href="/attendance" class="nav_btn">勤怠</a>

            <a href="/attendance/list" class="nav_btn">一覧</a>

            <a href="/stamp_correction_request/list" class="nav_btn">申請</a>

            <form action="/logout" method="POST" class="logout_form">
                @csrf
                <button type="submit" class="nav_btn">ログアウト</button>
            </form>
        </nav>
    </div>
</header>