@extends('layouts.app')
@section('title','スタッフ一覧')
@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin_staff_list.css') }}">
@endsection

@include('components.admin_header')

@section('content')
<div class="container">
    <h1>| スタッフ一覧</h1>
    <table>
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
        @foreach($staffs as $staff)
        <tr>
            <td>{{ $staff->name }}</td>
            <td>{{ $staff->email }}</td>
            <td>
                <a href="{{ route('admin.attendance.staff', $staff->id) }}" class="btn">詳細</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection