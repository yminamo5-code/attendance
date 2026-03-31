<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Breaktime;
use App\Models\AttendanceApplication;
use App\Models\BreaktimeApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // 今日の勤怠状況を確認
    public function attendance()
    {
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', $today)
            ->first();

        $status = 'before_work';

        if ($attendance) {
            if ($attendance->clock_out) {
                $status = 'after_work';
            } else {
                $latestBreak = Breaktime::where('attendance_id', $attendance->id)
                    ->latest()
                    ->first();

                if ($latestBreak && !$latestBreak->break_end) {
                    $status = 'breaking';
                } else {
                    $status = 'working';
                }
            }
        }

        return view('attendance', compact('status'));
    }

    // 出勤登録
    public function clock_in()
    {
        Attendance::create([
            'user_id' => auth()->id(),
            'work_date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        return redirect('/attendance');
    }

    // 退勤登録
    public function clock_out()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', now()->toDateString())
            ->first();

        $attendance->update([
            'clock_out' => now(),
        ]);

        return redirect('/attendance');
    }

    // 休憩開始
    public function break_start()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', now()->toDateString())
            ->first();

        Breaktime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
        ]);

        return redirect('/attendance');
    }

    // 休憩終了
    public function break_end()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', now()->toDateString())
            ->first();

        $latestBreak = Breaktime::where('attendance_id', $attendance->id)
            ->latest()
            ->first();

        $latestBreak->update([
            'break_end' => now(),
        ]);

        return redirect('/attendance');
    }

    // 勤怠一覧（月ごと）
    public function list(Request $request)
    {
        $userId = auth()->id();

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
            ];
        }

        return view('attendance_list', compact('days', 'currentMonth', 'prevMonth', 'nextMonth'));
    }

    // 勤怠詳細（スタッフ用）
    public function detail($id)
    {
        $attendance = Attendance::with('breaktimes')->findOrFail($id);

        // 申請中データがあれば優先
        $application = AttendanceApplication::with('breaktimes')
            ->where('attendance_id', $id)
            ->where('status', 'pending')
            ->first();

        if ($application) {
            $displayData = $application;
            $displayBreaks = $application->breaktimes;
            $status = 'pending';
        } else {
            $displayData = $attendance;
            $displayBreaks = $attendance->breaktimes;
            $status = 'approved';
        }

        return view('attendance_detail', compact('displayData', 'displayBreaks', 'status'));
    }

    public function applyCorrection(Request $request, $id)
    {
        $attendance = Attendance::with('breaktimes')->findOrFail($id);

        // すでに申請中のチェック
        $existingApplication = AttendanceApplication::where('attendance_id', $id)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'すでに申請中です');
        }

        // 勤怠申請作成（フォームから送られた値を使用）
        $application = AttendanceApplication::create([
            'attendance_id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'work_date' => $attendance->work_date,
            'clock_in' => $request->input('clock_in') ? Carbon::parse($request->input('clock_in'))->format('H:i:s') : null,
            'clock_out' => $request->input('clock_out') ? Carbon::parse($request->input('clock_out'))->format('H:i:s') : null,
            'remarks' => $request->input('remarks'),
            'status' => 'pending'
        ]);

        // 休憩申請作成（フォームの値をループで取得）
        $breakStarts = $request->input('break_start', []);
        $breakEnds = $request->input('break_end', []);

        foreach ($breakStarts as $i => $start) {
            // 入力がある場合のみ登録
            if ($start || (!empty($breakEnds[$i]))) {
                BreaktimeApplication::create([
                    'attendance_application_id' => $application->id,
                    'break_start' => $start ? Carbon::parse($start)->format('H:i:s') : null,
                    'break_end' => $breakEnds[$i] ? Carbon::parse($breakEnds[$i])->format('H:i:s') : null,
                ]);
            }
        }

        return redirect()->route('attendance_detail', $id);
    }

    // 申請一覧（スタッフ用）
    public function stampRequests()
    {
        $userId = auth()->id();

        $pendingRequests = AttendanceApplication::where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('work_date', 'desc')
            ->get();

        $approvedRequests = AttendanceApplication::where('user_id', $userId)
            ->where('status', 'approved')
            ->orderBy('work_date', 'desc')
            ->get();

        return view('request', compact('pendingRequests', 'approvedRequests'));
    }
}