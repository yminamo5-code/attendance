@extends('layouts.app')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@include('components.header_working')

@section('content')
<div class="container">
    <h1>|　勤怠詳細</h1>

    <!-- ✅ スタッフは修正申請 -->
    <form method="POST" action="{{ $status === 'approved' ? route('attendance.apply_correction', $displayData->id) : '#' }}">
        @csrf

        <table class="table table-bordered">

            <!-- 名前 -->
            <tr>
                <th>名前</th>
                <td class="start">
                    <input type="text" value="{{ $displayData->user->name }}" readonly>
                </td>
                <td class="separator"></td>
                <td class="end"></td>
            </tr>

            <!-- 日付 -->
            <tr>
                <th>日付</th>
                <td class="start">
                    <input type="text" value="{{ \Carbon\Carbon::parse($displayData->work_date)->format('Y年') }}" readonly>
                </td>
                <td class="separator"></td>
                <td class="end">
                    <input type="text" value="{{ \Carbon\Carbon::parse($displayData->work_date)->format('m月d日') }}" readonly>
                </td>
            </tr>

            <!-- 出勤・退勤 -->
            <tr>
                <th>出勤・退勤</th>
                <td class="start">
                    <input type="time" name="clock_in"
                        value="{{ $displayData->clock_in ? \Carbon\Carbon::parse($displayData->clock_in)->format('H:i') : '' }}"
                        @if($status !== 'approved') readonly @endif>
                </td>
                <td class="separator">～</td>
                <td class="end">
                    <input type="time" name="clock_out"
                        value="{{ $displayData->clock_out ? \Carbon\Carbon::parse($displayData->clock_out)->format('H:i') : '' }}"
                        @if($status !== 'approved') readonly @endif>
                </td>
            </tr>

            <!-- 休憩 -->
            @foreach($displayBreaks as $i => $break)
            <tr>
                <th>休憩{{ $i + 1 }}</th>
                <td class="start">
                    <input type="time" name="break_start[{{ $i }}]"
                        value="{{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '' }}"
                        @if($status !== 'approved') readonly @endif>
                </td>
                <td class="separator">～</td>
                <td class="end">
                    <input type="time" name="break_end[{{ $i }}]"
                        value="{{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '' }}"
                        @if($status !== 'approved') readonly @endif>
                </td>
            </tr>
            @endforeach

            <!-- 空の休憩行（申請可能なときだけ） -->
            @if($status === 'approved')
            <tr>
                <th>休憩{{ count($displayBreaks) + 1 }}</th>
                <td class="start">
                    <input type="time" name="break_start[{{ count($displayBreaks) }}]">
                </td>
                <td class="separator">～</td>
                <td class="end">
                    <input type="time" name="break_end[{{ count($displayBreaks) }}]">
                </td>
            </tr>
            @endif

            <!-- 備考 -->
            <tr>
                <th>備考</th>
                <td colspan="3">
                    <textarea name="remarks" @if($status !== 'approved') readonly @endif>
{{ $displayData->remarks ?? '' }}
                    </textarea>
                </td>
            </tr>

        </table>

        {{-- ボタン --}}
        @if($status === 'approved')
            <button class="btn btn-primary" type="submit">修正申請</button>
        @else
            <div class="alert-info">※承認待ちのため修正はできません。</div>
        @endif

    </form>
</div>
@endsection