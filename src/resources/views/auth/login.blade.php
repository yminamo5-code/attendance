@extends('layouts.app')

@section('title','ログイン画面(一般ユーザー)')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/login.css')  }}">
@endsection

@section('content')

    <div class="header">
        <img src="{{asset('img/COACHTECHヘッダーロゴ.png')}}" alt="ロゴ">
    </div>
    
    <form action="/login" method="post" class="form">
        @csrf
        <input type="hidden" name="login_type" value="staff">
        
        <h1>ログイン</h1>

        <label for="email" class="email_label">メールアドレス</label>
        <input id="email" type="email" name="email" class="email_input" value="{{old('email')}}">
        <div class="error">
            @error('email')
            {{$message}}
            @enderror
        </div>

        <label for="password" class="password_label">パスワード</label>
        <input type="password" name="password" class="password_input">
        <div class="error">
            @error('password')
            {{$message}}
            @enderror
        </div>

        <button class="login_button">ログインする</button>

        <a href="/register" class="register_link">会員登録はこちら</a>
    </form>
@endsection