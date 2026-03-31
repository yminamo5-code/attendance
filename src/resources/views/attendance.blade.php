@extends('layouts.app')

@section('title','勤怠登録画面(一般ユーザー)')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

@section('content')

    @if ($status === 'after_work')
        @include('components.header_after_working')
    @else
        @include('components.header_working')
    @endif

    <div class="wrap">

        <div class="status">
            @if ($status === 'before_work')
                勤務外
            @elseif ($status === 'working')
                出勤中
            @elseif ($status === 'breaking')
                休憩中
            @elseif ($status === 'after_work')
                退勤済
            @endif            
        </div>

        <div class="date" id="date"></div>

        <div class="clock" id="clock"></div>

            @if ($status === 'before_work')
                <form class="clock_in_form" method="POST" action="{{ route('clock.in') }}">
                    @csrf
                    <button class="clock_in_button" type="submit">出勤</button>
                </form>
            @elseif ($status === 'working')
                <div class="working_buttons">
                    <form class="clock_out_form" method="POST" action="{{ route('clock.out') }}">
                        @csrf
                        <button class="clock_out_button" type="submit">退勤</button>
                    </form>
                    <form class="break_start_form" method="POST" action="{{ route('break.start') }}">
                        @csrf
                        <button class="break_start_button" type="submit">休憩入</button>
                    </form>
                </div>
            @elseif ($status === 'breaking')
                <form class="break_end_form" method="POST" action="{{ route('break.end') }}">
                    @csrf
                    <button class="break_end_button" type="submit">休憩戻</button>
                </form>
            @elseif ($status === 'after_work')
                <div class="after_work_message">お疲れ様でした。</div>
            @endif  

    </div>

    <script>
        function updateDateTime(){
            const now = new Date();

            const date = now.toLocaleDateString('ja-JP',{
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                weekday:'short'
            });

            const time = now.toLocaleTimeString('ja-JP', {
                hour: '2-digit',
                minute: '2-digit',
            });

            document.getElementById('date').textContent = date;
            document.getElementById('clock').textContent = time;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
@endsection

