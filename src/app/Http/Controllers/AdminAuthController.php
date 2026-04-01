<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Breaktime;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AttendanceApplication;
use App\Models\BreaktimeApplication;
use App\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;


class AdminAuthController extends Controller
{
    // 管理者ログイン画面
    public function showLoginForm()
    {
        return view('auth.admin_login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['role'] = 1;

        if (Auth::attempt($credentials)) {
            return redirect('/admin/attendance/list');
        }

        return back()->withErrors(['email' => 'ログイン情報が正しくありません']);
    }

    // 管理者勤怠一覧
    public function adminList(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $prevDate = Carbon::parse($date)->subDay()->toDateString();
        $nextDate = Carbon::parse($date)->addDay()->toDateString();

        $attendances = Attendance::with(['user', 'breaktimes'])
            ->where('work_date', $date)
            ->get();

        return view('admin_attendance_list', compact('attendances', 'date', 'prevDate', 'nextDate'));
    }

    // 管理者勤怠詳細
    public function adminDetail($id)
    {
        $attendance = Attendance::with(['user', 'breaktimes'])->findOrFail($id);

        $displayData = $attendance;
        $displayBreaks = $attendance->breaktimes;

        return view('admin_attendance_detail', compact('displayData', 'displayBreaks'));
    }

    // 管理者による修正保存
    public function applyCorrection(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $workDate = $attendance->work_date;

        // 出勤・退勤を HH:mm -> DATETIME に変換
        $clockInTime = $request->input('clock_in');
        $clockOutTime = $request->input('clock_out');
        $attendance->clock_in = $clockInTime ? Carbon::parse("$workDate $clockInTime") : null;
        $attendance->clock_out = $clockOutTime ? Carbon::parse("$workDate $clockOutTime") : null;

        // 備考
        $attendance->remarks = $request->input('remarks');
        $attendance->save();

        // 休憩を削除して再作成
        $attendance->breaktimes()->delete();
        $breakStarts = $request->input('break_start', []);
        $breakEnds = $request->input('break_end', []);

        foreach ($breakStarts as $i => $start) {
            $end = $breakEnds[$i] ?? null;
            if ($start && $end) {
                Breaktime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => Carbon::parse("$workDate $start"),
                    'break_end' => Carbon::parse("$workDate $end"),
                ]);
            }
        }

        return redirect('/admin/attendance/list');
    }

    public function staffList()
    {
        $staffs = User::where('role', 0)->get();
        return view('admin_staff_list', compact('staffs'));
    }

    public function staffAttendance(Request $request, $userId)
    {
        $staff = User::findOrFail($userId);

        $currentMonth = $request->month
            ? Carbon::createFromFormat('Y-m', $request->month)
            : Carbon::now();

        $start = $currentMonth->copy()->startOfMonth();
        $end = $currentMonth->copy()->endOfMonth();

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $attendances = Attendance::with('breaktimes')
            ->where('user_id', $userId)
            ->whereBetween('work_date', [$start, $end])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->work_date)->format('Y-m-d');
            });

        $days = [];
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $key = $date->format('Y-m-d');
            $attendance = $attendances[$key] ?? null;

            $clockIn = '';
            $clockOut = '';
            $breakFormatted = '';
            $totalFormatted = '';

            if ($attendance && $attendance->clock_in) {
                $clockIn = Carbon::parse($attendance->clock_in)->format('H:i');
                $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '';

                $breakTotal = 0;
                foreach ($attendance->breaktimes as $break) {
                    if ($break->break_start && $break->break_end) {
                        $breakTotal += strtotime($break->break_end) - strtotime($break->break_start);
                    }
                }

                $workTotal = $attendance->clock_out ? strtotime($attendance->clock_out) - strtotime($attendance->clock_in) - $breakTotal : 0;

                $breakFormatted = floor($breakTotal / 3600) . ':' . str_pad(floor(($breakTotal % 3600) / 60), 2, '0', STR_PAD_LEFT);
                $totalFormatted = $attendance->clock_out ? floor($workTotal / 3600) . ':' . str_pad(floor(($workTotal % 3600) / 60), 2, '0', STR_PAD_LEFT) : '';
            }

            $days[] = [
                'id' => $attendance->id ?? null,
                'date' => $date->format('m/d') . '(' . ['日','月','火','水','木','金','土'][$date->dayOfWeek] . ')',
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'break' => $breakFormatted,
                'total' => $totalFormatted,
                'detail_url' => $attendance ? route('admin.attendance.detail', $attendance->id) : null, // ここ重要
            ];
        }

        return view('admin_staff_attendance', compact('staff', 'days', 'currentMonth', 'prevMonth', 'nextMonth'));
    }

    public function requestList()
    {
        $pendingRequests = AttendanceApplication::with('user')
            ->where('status', 'pending')
            ->get();

        $approvedRequests = AttendanceApplication::with('user')
            ->where('status', 'approved')
            ->get();

        return view('admin_request', compact('pendingRequests', 'approvedRequests'));
    }

    public function requestDetail($id)
    {
        $application = AttendanceApplication::with(['user', 'breaktimes'])->findOrFail($id);

        return view('admin_approve', compact('application'));
    }

    public function approveRequest($id)
    {
        $application = AttendanceApplication::with('breaktimes')->findOrFail($id);
        $attendance = Attendance::findOrFail($application->attendance_id);

        $workDate = $attendance->work_date;

        $attendance->clock_in = $application->clock_in 
            ? \Carbon\Carbon::parse($workDate . ' ' . $application->clock_in)
            : null;

        $attendance->clock_out = $application->clock_out 
            ? \Carbon\Carbon::parse($workDate . ' ' . $application->clock_out)
            : null;

        $attendance->remarks = $application->remarks;
        $attendance->save();

        // 休憩
        $attendance->breaktimes()->delete();

        foreach ($application->breaktimes as $break) {
            Breaktime::create([
                'attendance_id' => $attendance->id,
                'break_start' => \Carbon\Carbon::parse($workDate . ' ' . $break->break_start),
                'break_end' => \Carbon\Carbon::parse($workDate . ' ' . $break->break_end),
            ]);
        }

        // ステータス更新
        $application->status = 'approved';
        $application->save();

        return redirect()->route('admin.request.detail', $application->id);
    }
}