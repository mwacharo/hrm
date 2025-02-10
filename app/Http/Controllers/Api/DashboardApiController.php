<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\Vacancy;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    public function index()
    {
        $todayPresent = Attendance::whereDate('created_at', now()->toDateString())->count();

        // Get today's date
        $today = Carbon::today();

        $unregisteredAttendance = User::where('super_admin', 0)
            ->whereNotIn('id', function ($query) use ($today) {
                $query->select('user_id')
                    ->from('attendances')
                    ->whereDate('created_at', $today);
            })->count();

        $todayOnLeave = Leave::whereDate('from', '<=', now())
            ->whereDate('to', '>=', now())
            ->where('status', 'Approved')
            ->count();

        $newEmployeesThisWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $totalEmployees = User::where('super_admin', 0)->count();

        $percentageTodayPresent = ($totalEmployees > 0) ? ($todayPresent / $totalEmployees) * 100 : 0;
        $unregisteredAttendancePercent = ($totalEmployees > 0) ? ($unregisteredAttendance / $totalEmployees) * 100 : 0;
        $percentageTodayOnLeave = ($totalEmployees > 0) ? ($todayOnLeave / $totalEmployees) * 100 : 0;
        $percentageNewEmployeesThisWeek = ($totalEmployees > 0) ? ($newEmployeesThisWeek / $totalEmployees) * 100 : 0;

        $percentageTodayPresent = number_format($percentageTodayPresent, 2);
        $unregisteredAttendancePercent = number_format($unregisteredAttendancePercent, 2);
        $percentageTodayOnLeave = number_format($percentageTodayOnLeave, 2);
        $percentageNewEmployeesThisWeek = number_format($percentageNewEmployeesThisWeek, 2);

        $employeeStatus = [
            ['label' => 'Today Present', 'value' => $todayPresent, 'percentage' => $percentageTodayPresent, 'color' => 'error'],
            ['label' => 'Unregistered Attendance', 'value' => $unregisteredAttendance, 'percentage' => $unregisteredAttendancePercent, 'color' => 'error'],
            ['label' => 'Today On Leave', 'value' => $todayOnLeave, 'percentage' => $percentageTodayOnLeave, 'color' => 'info'],
            ['label' => 'New Employees (This Week)', 'value' => $newEmployeesThisWeek, 'percentage' => $percentageNewEmployeesThisWeek, 'color' => 'info'],
        ];

        return response()->json([
            'statistics' => $this->getStatistics(),
            'employeeStatus' => $employeeStatus,
        ]);
    }

    private function getStatistics()
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $deletedUsersCount = User::withTrashed()->whereNotNull('deleted_at')->count();
        $newLeavesCount = Leave::whereBetween('created_at', [$startDate, $endDate])->count();
        $newAnnouncementsCount = Announcement::whereBetween('created_at', [$startDate, $endDate])->count();
        $newVacanciesCount = Vacancy::whereBetween('created_at', [$startDate, $endDate])->count();
        $statistics = [
            [
                'icon' => 'mdi-account-group',
                'value' => User::where('super_admin', 0)->count(),
                'label' => 'Employees',
                'color' => 'light',
                'description' => $deletedUsersCount . ' Cleared (This Year)',
            ],
            [
                'icon' => 'mdi-tag',
                'value' => Vacancy::count(),
                'label' => 'Vacancies',
                'color' => 'light',
                'description' => $newVacanciesCount . ' New Vacancies (This Month)',
            ],
            [
                'icon' => 'mdi-bell',
                'value' => Announcement::count(),
                'label' => 'Announcements',
                'color' => 'light',
                'description' => $newAnnouncementsCount . ' New Announcements (This Week)',
            ],
            [
                'icon' => 'mdi-rocket',
                'value' => Leave::whereIn('status', ['Pending', 'Hr Approved', 'Manager Approved'])->count(),
                'label' => 'Leave Requests',
                'color' => 'light',
                'description' => $newLeavesCount . ' Requests (This Week)',
            ],
        ];

        return $statistics;
    }

    public function userStatistics(User $user)
    {
        $teamMembers = User::where('department_id', $user->department_id)->count();

        $userDepartmentId = $user->department_id;

        $totalApprovedLeaves = Leave::where('status', 'LIKE', '%Approved%')
            ->where('user_id', $user->id)
            ->count();

        $totalAppliedLeaves = Leave::where('user_id', $user->id)->count();

        $currentDate = Carbon::now();
        $teamMembersOnLeave = Leave::where('status', 'LIKE', '%Approved%')
            ->where('from', '<=', $currentDate)
            ->where('to', '>=', $currentDate)
            ->whereHas('user', function ($query) use ($userDepartmentId) {
                $query->where('department_id', $userDepartmentId);
            })
            ->count();

        $leaveRequests = Leave::where('status', 'LIKE', '%Pending%')
            ->where('from', '<=', $currentDate)
            ->where('to', '>=', $currentDate)
            ->whereHas('user', function ($query) use ($userDepartmentId) {
                $query->where('department_id', $userDepartmentId);
            })
            ->count();

        $annualLeaveBalance = LeaveBalance::where('user_id', $user->id)
            ->whereHas('leaveType', function ($query) {
                $query->where('name', 'REGEXP', '(Annual|annual|annual[_ ]?leave|Annual[_ ]?Leave)');
            })
            ->first();

        $leaveBalance = $annualLeaveBalance ? $annualLeaveBalance->balance : 0;


        $leaveTaken = Leave::where('user_id', $user->id)
            ->where('status', 'Approved')
            ->whereHas('leave_type', function ($query) {
                $query->where('name', 'REGEXP', '(Annual|annual|annual[_ ]?leave|Annual[_ ]?Leave)');
            })
            ->sum('days') ?? '0';

        $annualLeaveDaysAssigned = LeaveType::where('name', 'REGEXP', '(Annual|annual|annual[_ ]?leave|Annual[_ ]?Leave)')->first()->days ?? '0';

        $notifications = 0;

        $earlyAttendances = Attendance::where('user_id', $user->id)->where('status', 'In Time')->count();
        $lateAttendances = Attendance::where('user_id', $user->id)->where('status', 'Late')->count();
        $hr = User::where('is_hr', 1)->first();
        return response()->json([
            'teamMembers' => $teamMembers,
            'earlyAttendances' => $earlyAttendances,
            'lateAttendances' => $lateAttendances,
            'annualLeaveDaysAssigned' => $annualLeaveDaysAssigned,
            'leaveBalance' => $leaveBalance,
            'leaveTaken' => $leaveTaken,
            'notifications' => $notifications,
            'teamMembersOnLeave' => $teamMembersOnLeave,
            'totalAppliedLeaves' => $totalAppliedLeaves,
            'totalApprovedLeaves' => $totalApprovedLeaves,
            'leaveRequests' => $leaveRequests,
            'hr'=> $hr
        ]);
    }

    public function getUserAnalytics($userId)
    {
        $currentMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $inTimeAttendances = Attendance::where('user_id', $userId)
            ->where('status', 'In Time')
            ->whereBetween('attendance_date', [$currentMonth, $endOfMonth])
            ->count();

        $lateAttendances = Attendance::where('user_id', $userId)
            ->where('status', 'Late')
            ->whereBetween('attendance_date', [$currentMonth, $endOfMonth])
            ->count();

        $leaveDays = Leave::where('user_id', $userId)
            ->whereDate('from', '>=', $currentMonth)
            ->whereDate('to', '<=', $endOfMonth)
            ->count();

        return [
            'inTimeAttendances' => $inTimeAttendances,
            'lateAttendances' => $lateAttendances,
            'leaveDays' => $leaveDays,
        ];
    }

}
