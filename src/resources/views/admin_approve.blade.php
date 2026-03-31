@extends('layouts.app')

@section('title','修正申請承認画面(管理者)')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@include('components.admin_header')

@section('content')
<div class="container">
    <h1>| 勤怠詳細</h1>

    <form method="POST" action="{{ route('admin.request.approve', $application->id) }}">
        @csrf

        <table class="table table-bordered">

            <!-- 名前 -->
            <tr>
                <th>名前</th>
                <td colspan="3">
                    <input type="text" value="{{ $application->user->name }}" readonly>
                </td>
            </tr>

            <!-- 日付 -->
            <tr>
                <th>日付</th>
                <td colspan="3">
                    <input type="text" value="{{ \Carbon\Carbon::parse($application->work_date)->format('Y/m/d') }}" readonly>
                </td>
            </tr>

            <!-- 出勤・退勤 -->
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time"
                        value="{{ $application->clock_in ? \Carbon\Carbon::parse($application->clock_in)->format('H:i') : '' }}"
                        readonly>
                </td>
                <td>～</td>
                <td>
                    <input type="time"
                        value="{{ $application->clock_out ? \Carbon\Carbon::parse($application->clock_out)->format('H:i') : '' }}"
                        readonly>
                </td>
            </tr>

            <!-- 休憩 -->
            @foreach($application->breaktimes as $i => $break)
            <tr>
                <th>休憩{{ $i + 1 }}</th>
                <td>
                    <input type="time"
                        value="{{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '' }}"
                        readonly>
                </td>
                <td>～</td>
                <td>
                    <input type="time"
                        value="{{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '' }}"
                        readonly>
                </td>
            </tr>
            @endforeach

            <!-- 備考 -->
            <tr>
                <th>備考</th>
                <td colspan="3">
                    <textarea readonly>{{ $application->remarks }}</textarea>
                </td>
            </tr>

        </table>

<div class="btn-area">
    @if($application->status === 'pending')
        <button class="btn btn-approve" type="submit">承認</button>
    @else
        <button class="btn btn-approved" type="button" disabled>承認済み</button>
    @endif
</div>

    </form>
</div>
@endsection