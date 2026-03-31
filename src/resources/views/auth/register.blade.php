@extends('layouts.app')

@section('title','会員登録画面(一般ユーザー)')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/register.css')  }}">
@endsection

@section('content')
<main>
    @include('components.header_working')
    <form action="/register" method="post" class="form">
        @csrf
        
        <h1>会員登録</h1>

        <label for="name" class="name">名前</label>
        <input id="name" type="text" name="name" class="name_input" value="{{old('name')}}">
        <div class="error">
            @error('name')
            {{$message}}
            @enderror
        </div>

        <label for="email" class="email_label">メールアドレス</label>
        <input id="email" type="email" name="email" class="email_input" value="{{old('email')}}">
        <div class="error">
            @error('email')
            {{$message}}
            @enderror
        </div>

        <label for="password" class="password_label">パスワード</label>
        <input id="password" type="password" name="password" class="password_input">
        <div class="error">
            @error('password')
            {{$message}}
            @enderror
        </div>

        <label for="password_confirmation" class="password_confirmation_label">確認用パスワード</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="password_confirm_input">
        <div class="error">
            @error('password_confirmation')
                {{ $message }}
            @enderror
        </div>

        <button class="register_button">会員登録する</button>

        <a href="/login" class="login_link">ログインはこちら</a>
    </form>
</main>
@endsection