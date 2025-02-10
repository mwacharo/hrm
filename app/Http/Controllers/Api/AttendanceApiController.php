<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;


class AttendanceApiController extends Controller
{
    public function index(Request $request)
    {
        $unit_id = $request->unit_id ?? auth()->user()->unit_id;

        $query = Attendance::with('user.unit')
            ->whereHas('user', function ($query) use ($unit_id) {
                $query->where('unit_id', $unit_id)
                    ->whereNull('deleted_at');
            })
            ->orderBy('created_at', 'desc');

        $this->applyFilters($request, $query);

        $attendances = $query->take(2000)->get();

        $totalEmployees = User::whereNull('deleted_at')
            ->where('super_admin', 0)
            ->where('unit_id', $unit_id)
            ->count();

        $attendanceStats = $this->calculateAttendanceStatistics($attendances, $totalEmployees);

        return response()->json([
            'attendances' => $attendances,
            'average_presence_percentage' => $attendanceStats['average_presence_percentage'],
            'average_clock_in_time' => $attendanceStats['average_clock_in_time'],
            'average_clock_out_time' => $attendanceStats['average_clock_out_time'],
            'average_working_hours' => $attendanceStats['average_working_hours'],
        ]);
    }

    public function attendanceAnalytics()
    {
        $startDate = Carbon::now()->startOfWeek()->subWeek();
        $endDate = $startDate->copy()->addDays(6);
        $attendances = Attendance::whereBetween('attendance_date', [$startDate, $endDate])->get();
        $leaves = Leave::where('status', 'Approved')
            ->where(fn($query) => $query->whereBetween('from', [$startDate, $endDate])
                    ->orWhereBetween('to', [$startDate, $endDate]))
            ->get();

        $attendanceData = [];
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $dayData = $this->getDayAttendanceData($date, $attendances, $leaves);
            $attendanceData[$date->format('l')] = [$dayData];
        }

        return response()->json($attendanceData);
    }

    public function userAttendance(Request $request)
    {
        $attendances = Attendance::where('user_id', $request->user_id)
            ->orderBy('attendance_date', 'desc')
            ->get();

        $attendances->transform(function ($attendance) {
            $attendanceDate = new DateTime($attendance->attendance_date);
            $attendance->attendance_date = $attendanceDate->format('D d M Y');
            return $attendance;
        });

        return response()->json(['attendances' => $attendances]);
    }

    public function store(Request $request)
    {
        try {
            $this->validateAttendanceRequest($request);

            $existingAttendance = Attendance::where('user_id', $request->user_id)
                ->where('attendance_date', $request->attendance_date)
                ->first();

            if ($existingAttendance) {
                return response()->json(['message' => 'Attendance is already marked!'], 400);
            }

            $distanceFromPremise = $this->validateLocation($request->latitude, $request->longitude);

            $notes = $distanceFromPremise ? "Distance from the premise: " . $distanceFromPremise : null;
            $this->processAttendance($request, $notes);

            return response()->json(['message' => 'Record created!'], 200);
        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Attendance $attendance)
    {
        try {
            $this->validateAttendanceRequest($request);
            $attendance->update($request->all());
            return response()->json(['message' => 'Attendance record updated successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error in update method: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:In Time,Late',
            'ids' => 'required|array',
            'ids.*' => 'exists:attendances,id',
        ]);

        Attendance::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json(['message' => 'Attendance deleted successfully']);
    }

    private function applyFilters(Request $request, $query)
    {
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('attendance_date')) {
            $query->whereDate('attendance_date', $request->attendance_date);
        }

        if ($request->has('unit_id')) {
            $query->whereHas('user', fn($userQuery) => $userQuery->where('unit_id', $request->unit_id));
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
    }

    private function calculateAttendanceStatistics($attendances, $totalEmployees)
    {
        $today = date('Y-m-d');
        $todayAttendances = $attendances->filter(fn($attendance) => $attendance->attendance_date == $today);
        $uniqueDailyAttendancesToday = $todayAttendances->groupBy('user_id');

        $totalPresentToday = $uniqueDailyAttendancesToday->filter(fn($group) => $group->first()->is_present)->count();
        $totalMarkedAttendanceToday = $uniqueDailyAttendancesToday->count();

        $averagePresencePercentage = $totalEmployees > 0 ? ($totalMarkedAttendanceToday / $totalEmployees) * 100 : 0;
        $averagePresencePercentage = number_format($averagePresencePercentage, 2);

        $uniqueDailyAttendances = $attendances->groupBy(fn($attendance) => $attendance->attendance_date . '_' . $attendance->user_id);

        $totalClockInTime = $uniqueDailyAttendances->sum(fn($group) => strtotime($group->first()->clock_in_time));
        $countClockOutTime = $uniqueDailyAttendances->filter(fn($group) => $group->first()->clock_out_time)->count();
        $totalClockOutTime = $uniqueDailyAttendances->sum(fn($group) => strtotime($group->first()->clock_out_time) ?: 0);
        $totalWorkingHours = $uniqueDailyAttendances->sum(fn($group) => $group->first()->hours_worked);

        $totalMarkedAttendance = $uniqueDailyAttendances->count();

        $averageClockInTime = $totalMarkedAttendance > 0 ? date('H:i:s', $totalClockInTime / $totalMarkedAttendance) : '00:00:00';
        $averageClockOutTime = $countClockOutTime > 0 ? date('H:i:s', $totalClockOutTime / $countClockOutTime) : '00:00:00';

        $averageClockInSeconds = strtotime($averageClockInTime);
        $averageClockOutSeconds = strtotime($averageClockOutTime);

        $averageWorkingSeconds = $averageClockOutSeconds - $averageClockInSeconds;
        $averageWorkingHoursCalculated = $averageWorkingSeconds > 0 ? $averageWorkingSeconds / 3600 : 9.0;

        $averageWorkingHours = number_format($averageWorkingHoursCalculated, 2);

        return [
            'average_presence_percentage' => $averagePresencePercentage,
            'average_clock_in_time' => $averageClockInTime,
            'average_clock_out_time' => $averageClockOutTime,
            'average_working_hours' => $averageWorkingHours,
        ];
    }

    private function getDayAttendanceData($date, $attendances, $leaves)
    {
        $dayData = [
            'in_time' => 0,
            'late' => 0,
            'on_leave' => 0,
        ];

        foreach ($attendances as $attendance) {
            if (Carbon::parse($attendance->attendance_date)->format('Y-m-d') === $date->format('Y-m-d')) {
                $dayData[$attendance->status === 'In Time' ? 'in_time' : 'late']++;
            }
        }

        foreach ($leaves as $leave) {
            if ($date->between($leave->from, $leave->to)) {
                $dayData['on_leave']++;
            }
        }

        return $dayData;
    }

    private function validateAttendanceRequest(Request $request)
    {
        $request->validate([
            'attendance_type' => 'required|in:clock_in,clock_out',
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
        ]);
    }

    private function validateLocation($userLatitude, $userLongitude)
    {
        $premiseLatitude = -1.3473844528198242;
        $premiseLongitude = 36.901023864746094;

        $distanceInKilometers = $this->haversineDistance($premiseLatitude, $premiseLongitude, $userLatitude, $userLongitude);

        $formattedDistance = number_format($distanceInKilometers, 2) . ' m';

        Log::info('Distance from premise:', ['distance' => $formattedDistance]);

        return $formattedDistance;
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371; // Radius of Earth in kilometers

        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
        cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distance in kilometers
        $distance = $earth_radius * $c;

        return $distance;
    }

    private function processAttendance(Request $request, $notes = null)
    {
        $existingAttendance = Attendance::firstOrNew([
            'user_id' => $request->user_id,
            'attendance_date' => $request->attendance_date,
        ]);

        if ($request->attendance_type === 'clock_in' && !$existingAttendance->clock_in_time) {
            $existingAttendance->clock_in_time = $request->time;
            $existingAttendance->status = $this->isLate($request->time) ? 'Late' : 'In Time';
        } elseif ($request->attendance_type === 'clock_out' && !$existingAttendance->clock_out_time) {
            $existingAttendance->clock_out_time = $request->time;
        }

        if ($notes) {
            $existingAttendance->notes = $notes;
        }

        $existingAttendance->is_present = true;
        $existingAttendance->save();
    }

    private function isLate($clockInTime)
    {
        $lateThreshold = '08:00';
        if (Carbon::parse($clockInTime)->dayOfWeek == Carbon::SATURDAY) {
            $lateThreshold = '08:30';
        }

        return Carbon::parse($clockInTime)->greaterThan(Carbon::parse($lateThreshold));
    }


    public function fetchServerTime(): JsonResponse
{
    // Get the current server time
    $serverTime = now()->toDateTimeString(); // Uses Laravel's `now()` helper

    // Return the response as JSON
    return response()->json([
        'time' => $serverTime,
    ]);
}

    
}
