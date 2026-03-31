@extends('layouts.app')

@section('title','勤怠詳細画面(管理者)')
@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@include('components.admin_header')

@section('content')
<div class="container">
    <h1>| 勤怠詳細</h1>

    <form method="POST" action="{{ route('admin.attendance.apply_correction', $displayData->id) }}">
        @csrf
        <table class="table table-bordered">

            <!-- 名前 -->
            <tr>
                <th>名前</th>
                <td colspan="3">
                    <input type="text" value="{{ $displayData->user->name }}" readonly>
                </td>
            </tr>

            <!-- 日付 -->
            <tr>
                <th>日付</th>
                <td colspan="3">
                    <input type="text" value="{{ \Carbon\Carbon::parse($displayData->work_date)->format('Y/m/d') }}" readonly>
                </td>
            </tr>

            <!-- 出勤・退勤 -->
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="clock_in" 
                        value="{{ $displayData->clock_in ? \Carbon\Carbon::parse($displayData->clock_in)->format('H:i') : '' }}">
                </td>
                <td>～</td>
                <td>
                    <input type="time" name="clock_out" 
                        value="{{ $displayData->clock_out ? \Carbon\Carbon::parse($displayData->clock_out)->format('H:i') : '' }}">
                </td>
            </tr>

            <!-- 休憩 -->
            @foreach($displayBreaks as $i => $break)
            <tr>
                <th>休憩{{ $i + 1 }}</th>
                <td>
                    <input type="time" name="break_start[{{ $i }}]" 
                        value="{{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '' }}">
                </td>
                <td>～</td>
                <td>
                    <input type="time" name="break_end[{{ $i }}]" 
                        value="{{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '' }}">
                </td>
            </tr>
            @endforeach

            <!-- 空の休憩行 -->
            <tr>
                <th>休憩{{ count($displayBreaks) + 1 }}</th>
                <td><input type="time" name="break_start[{{ count($displayBreaks) }}]"></td>
                <td>～</td>
                <td><input type="time" name="break_end[{{ count($displayBreaks) }}]"></td>
            </tr>

            <!-- 備考 -->
            <tr>
                <th>備考</th>
                <td colspan="3">
                    <textarea name="remarks">{{ $displayData->remarks ?? '' }}</textarea>
                </td>
            </tr>
        </table>

        <button class="btn btn-primary" type="submit">修正</button>
    </form>
</div>
@endsection