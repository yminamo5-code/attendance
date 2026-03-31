@extends('layouts.app')

@section('title','申請一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/request.css')  }}">
@endsection

@include('components.header_working')

@section('content')

<div class="container">
    <h1>|　申請一覧</h1>

    <!-- タブ切り替え -->
    <div class="tabs">
        <button class="tab-button active" onclick="showTab('pending', this)">承認待ち</button>
        <button class="tab-button" onclick="showTab('approved', this)">承認済み</button>
    </div>

    <!-- 承認待ちタブ -->
    <div id="pending" class="tab-content">
        <table>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
            @foreach($pendingRequests as $attendance)
            <tr>
                <td>承認待ち</td>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}</td>
                <td>{{ $attendance->remarks }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->updated_at)->format('Y/m/d') }}</td>
                <td><a href="{{ url('/attendance/detail/' . $attendance->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- 承認済みタブ -->
    <div id="approved" class="tab-content" style="display:none;">
        <table>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
            @foreach($approvedRequests as $attendance)
            <tr>
                <td>承認済み</td>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y/m/d') }}</td>
                <td>{{ $attendance->remarks }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->updated_at)->format('Y/m/d') }}</td>
                <td><a href="{{ url('/attendance/detail/' . $attendance->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

<script>
function showTab(tabId, button) {
    document.getElementById('pending').style.display = tabId === 'pending' ? 'block' : 'none';
    document.getElementById('approved').style.display = tabId === 'approved' ? 'block' : 'none';

    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
}
</script>

@endsection