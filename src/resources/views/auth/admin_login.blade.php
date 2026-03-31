@extends('layouts.app')

@section('title','ログイン画面(管理者)')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/admin_login.css')  }}">
@endsection

@section('content')

    <div class="header">
        <img src="{{asset('img/COACHTECHヘッダーロゴ.png')}}" alt="ロゴ">
    </div>
    
    <form action="/admin/login" method="post" class="form">
        @csrf
        <input type="hidden" name="login_type" value="admin">
        
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

        <button class="login_button">管理者ログインする</button>
    </form>
@endsection