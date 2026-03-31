@extends('layouts.app')

@section('title','メール認証誘導画面')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/verify_email.css')  }}">
@endsection

    <div class="header">
        <img src="{{asset('img/COACHTECHヘッダーロゴ.png')}}" alt="ロゴ">
    </div>

@section('content')

    <div class="message">
        <p>登録いただいたメールアドレスに認証メールを送付しました。</p>
        <p>メール認証を完了してください</p>
    </div>

    <a href="https://mailtrap.io/inboxes" target="_blank" rel="noopener" class="button">認証はこちらから</a>
    <a href="" class="resend">認証メールを再送する</a>

@endsection