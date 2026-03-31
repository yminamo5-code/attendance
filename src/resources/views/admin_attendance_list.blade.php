@extends('layouts.app')

@section('title','勤怠一覧画面(管理者)')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin_attendance_list.css') }}">
@endsection

@include('components.admin_header')

@section('content')

<div class="container">
    <h1>|　勤怠一覧</h1>

    <div class="month-nav">
        <form method="GET" action="{{ url('/admin/attendance/list') }}">
            <input type="hidden" name="date" value="{{ $prevDate }}">
            <button class="btn-month">← 前日</button>
        </form>

        <span class="current-month">
            {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}
        </span>

        <form method="GET" action="{{ url('/admin/attendance/list') }}">
            <input type="hidden" name="date" value="{{ $nextDate }}">
            <button class="btn-month">翌日 →</button>
        </form>
    </div>

    <table>
        <tr>
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>

@foreach ($attendances as $attendance)
@php
    // 出勤・退勤
    $clockIn = $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in) : null;
    $clockOut = $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out) : null;

    // 休憩合計（分）
    $breakMinutes = 0;

    foreach ($attendance->breaktimes as $break) {
        if ($break->break_start && $break->break_end) {
            $start = \Carbon\Carbon::parse($break->break_start);
            $end = \Carbon\Carbon::parse($break->break_end);
            $breakMinutes += $start->diffInMinutes($end);
        }
    }

    // 勤務時間（分）
    $workMinutes = 0;
    if ($clockIn && $clockOut) {
        $workMinutes = $clockIn->diffInMinutes($clockOut) - $breakMinutes;
    }

    // 表示用フォーマット
    $breakTime = gmdate('H:i', $breakMinutes * 60);
    $workTime = gmdate('H:i', $workMinutes * 60);
@endphp

    <tr>
        <td>{{ $attendance->user->name }}</td>
        <td>{{ optional($clockIn)->format('H:i') }}</td>
        <td>{{ optional($clockOut)->format('H:i') }}</td>
        <td>{{ $breakTime }}</td>
        <td>{{ $workTime }}</td>
        <td>
            <a href="{{ url('admin/attendance/detail/' . $attendance->id) }}" class="btn">詳細</a>
        </td>
    </tr>
@endforeach