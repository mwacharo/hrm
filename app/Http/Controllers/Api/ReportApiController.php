<?php

namespace App\Http\Controllers\Api;

use App\Exports\AssetReportExport;
use App\Exports\AttendanceExport;
use App\Exports\LeaveExport;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportApiController extends Controller
{
    private $crm_email = "";
    private $crm_password = "";
    public function leaveReport(Request $request)
    {
        $validated = $request->validate([
            'employee' => 'nullable|exists:users,id',
            'leaveType' => 'nullable|exists:leave_types,id',
            'leaveStatus' => 'nullable|string|in:Approved,Pending,Rejected,Cancelled',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);

        $query = Leave::with('user', 'leave_type');

        if (!empty($validated['employee'])) {
            $query->where('user_id', $validated['employee']);
        }

        if (!empty($validated['leaveType'])) {
            $query->where('leave_type_id', $validated['leaveType']);
        }

        if (!empty($validated['leaveStatus'])) {
            $query->where('status', $validated['leaveStatus']);
        }

        if (!empty($validated['start'])) {
            $query->where('from', '>=', $validated['start']);
        }

        if (!empty($validated['end'])) {
            $query->where('to', '<=', $validated['end']);
        }

        $leaveRecords = $query->orderBy('created_at', 'desc')->get();

        $statuses = [
            'total' => $leaveRecords->count(),
            'approved' => $leaveRecords->where('status', 'Approved')->count(),
            'pending' => $leaveRecords->where('status', 'Pending')->count(),
            'rejected' => $leaveRecords->where('status', 'Rejected')->count(),
            'cancelled' => $leaveRecords->where('status', 'Cancelled')->count(),
        ];

        return response()->json([
            'leaveReport' => $leaveRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'created_at' => $record->created_at,
                    'fullName' => $record->user ? $record->user->firstname . ' ' . $record->user->lastname : 'deleted user',
                    'leaveType' => $record->leave_type->name,
                    'from' => $record->from,
                    'to' => $record->to,
                    'status' => $record->status,
                    'days' => $record->days,
                    'notes' => $record->comment,
                ];
            }),
            'leaveReportStatuses' => $statuses,
        ]);
    }

    public function attendanceReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee' => 'nullable|exists:users,id',
                'attendanceStatus' => 'nullable|string|in:Present,Absent,Late,Excused',
                'start' => 'nullable|date',
                'end' => 'nullable|date',
            ]);

            $query = Attendance::query();

            $query->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            });

            if (!empty($validated['employee'])) {
                $query->where('user_id', $validated['employee']);
            }

            if (!empty($validated['attendanceStatus'])) {
                $query->where('status', $validated['attendanceStatus']);
            }

            if (!empty($validated['start'])) {
                $query->where('attendance_date', '>=', $validated['start']);
            }

            if (!empty($validated['end'])) {
                $query->where('attendance_date', '<=', $validated['end']);
            }

            $attendanceRecords = $query->orderBy('created_at', 'desc')->get();

            $statuses = [
                'total' => $attendanceRecords->count(),
                'in_time' => $attendanceRecords->where('status', 'In Time')->count(),
                'late' => $attendanceRecords->where('status', 'Late')->count(),
            ];

            return response()->json([
                'attendanceReport' => $attendanceRecords->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'created_at' => $record->created_at->format('D d M Y H:i'),
                        'clock_in' => $record->clock_in_time,
                        'clock_out' => $record->clock_out_time,
                        'name' => $record->user ? $record->user->firstname . ' ' . $record->user->lastname : 'deleted user',
                        'status' => $record->status,
                        'notes' => $record->notes,
                        'attendance_date' => $record->attendance_date,
                    ];
                }),
                'attendanceReportStatuses' => $statuses,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch attendance report: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch attendance report',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function attendanceExcelReport(Request $request)
    {
        $attendances = json_decode($request->input('attendances'));
        return Excel::download(new AttendanceExport($attendances), 'attendance_report.xlsx');

    }

    public function leaveExcelReport(Request $request)
    {
        $leaves = json_decode($request->input('leaves'));
        return Excel::download(new LeaveExport($leaves), 'leave_report.xlsx');

    }

    // public function  assetReport(){


    //     $reportdata =Asset::all();

    //     return response()->json($reportdata);

    // }


    public function assetReport()
    {
        return Excel::download(new AssetReportExport, 'asset_report.xlsx');
    }
}




