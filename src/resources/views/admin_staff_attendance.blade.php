@extends('layouts.app')

@section('title','勤怠一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_list.css') }}">
@endsection

@include('components.admin_header')

@section('content')
<div class="container">
    <h1>| {{ $staff->name }} さんの勤怠一覧</h1>

    <div class="month-nav">
        <form method="GET" action="{{ url('/admin/attendance/staff/' . $staff->id) }}">
            <input type="hidden" name="month" value="{{ $prevMonth }}">
            <button type="submit" class="btn-month">← 前月</button>
        </form>

        <span class="current-month">{{ $currentMonth->format('Y年m月') }}</span>

        <form method="GET" action="{{ url('/admin/attendance/staff/' . $staff->id) }}">
            <input type="hidden" name="month" value="{{ $nextMonth }}">
            <button type="submit" class="btn-month">次月 →</button>
        </form>
    </div>

    <table>
        <tr>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>

        @foreach ($days as $day)
        <tr>
            <td>{{ $day['date'] }}</td>
            <td>{{ $day['clock_in'] }}</td>
            <td>{{ $day['clock_out'] }}</td>
            <td>{{ $day['break'] }}</td>
            <td>{{ $day['total'] }}</td>
            <td>
                @if($day['id'])
                    <a href="{{ $day['detail_url'] }}" class="btn">詳細</a>
                @else
                    <span class="btn disabled">詳細</span>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection