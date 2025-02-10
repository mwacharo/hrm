<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\SMSUtil;
use App\Http\Controllers\Util\TZSMSUtil;
use App\Http\Controllers\Util\UGSMSUtil;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveCreatedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class LeaveApiController extends Controller
{

  public function index(Request $request)
  {

    $query = Leave::with('leave_type', 'user')
      ->whereHas('user', function ($query) {
        $query->whereNull('deleted_at');
      });

    // if ($request->has('user_ids') && is_array($request->input('user_ids'))) {
    //   // dd($request->user_ids);
    //   $query->whereIn('user_id', $request->input('user_ids'));
    // }



if ($request->has('user_ids') && is_array($request->input('user_ids'))) {
  $userIds = collect($request->input('user_ids'))->flatten()->toArray();
  $query->whereIn('user_id', $userIds);
}

    if ($request->has('unit_ids') && is_array($request->input('unit_ids'))) {

      $query->whereHas('user', function ($query) use ($request) {
        $query->whereIn('unit_id', $request->input('unit_ids'));
      });
    }

    if ($request->has('leave_type_ids') && is_array($request->input('leave_type_ids'))) {
      $query->whereIn('leave_type_id', $request->input('leave_type_ids'));
    }

    if ($request->has('application_date')) {
      $applicationDate = $request->input('application_date');
      $query->whereDate('created_at', $applicationDate);
    }


    if ($request->has('status')) {
      $status = $request->input('status');
      $query->where('status', $status);
    }

    if ($request->has('from')) {
      $startDate = $request->input('from');
      $query->whereDate('from', $startDate);
    }

    if ($request->has('statuses') && is_array($request->input('statuses'))) {
      $query->whereIn('status', $request->input('statuses'));
    }

    $leaves = $query->orderBy('created_at', 'desc')->get();

    $leaves = $leaves->map(function ($leave) {
      $leave->user->name = $leave->user->firstname . ' ' . $leave->user->lastname;
      return $leave;
    });

    return response()->json(['leaves' => $leaves]);
  }

  public function userLeaves(Request $request)
  {

    $leaves = Leave::with('leave_type')
      ->orderBy('created_at', 'desc')
      ->where('user_id', $request->user_id)
      ->get();

    return response()->json(['leaves' => $leaves]);
  }

  public function leaveBalances(Request $request)
  {
    $query = LeaveBalance::with('user', 'leaveType');

    if ($request->has('user_ids')) {
      $userIds = explode(',', $request->get('user_ids'));
      $query->whereIn('user_id', $userIds);
    }

    if ($request->has('leave_type_ids')) {
      $leaveTypeIds = explode(',', $request->get('leave_type_ids'));
      $query->whereIn('leave_type_id', $leaveTypeIds);
    }

    if ($request->has('country_ids')) {
      $countryIds = explode(',', $request->get('country_ids'));
      $query->whereIn('user_id', function ($subQuery) use ($countryIds) {
        $subQuery->select('id')
          ->from('users')
          ->whereIn('unit_id', $countryIds);
      });
    }

    $leaveBalances = $query->get();

    return response()->json(['leaveBalances' => $leaveBalances]);
  }

  public function createLeaveBalances(Request $request)
  {

    $validatedData = $request->validate([
      'user_id' => 'required|exists:users,id',
      'leave_type_id' => 'required|exists:leave_types,id',
      'balance' => 'required',
    ]);

    $leaveBalance = LeaveBalance::create($validatedData);

    return response()->json(['message' => 'Leave balance created successfully', 'data' => $leaveBalance]);
  }

  public function deteteLeaveBalance(Request $request)
  {
    $leaveBalanceId = $request->id;

    $leaveBalance = LeaveBalance::find($leaveBalanceId);

    if (!$leaveBalance) {
      return response()->json(['message' => 'Leave balance not found.'], 404);
    }

    try {
      $leaveBalance->delete();
      return response()->json(['message' => 'Leave balance deleted successfully.'], 200);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Failed to delete leave balance.'], 500);
    }
  }

  public function updateLeaveBalance(Request $request, $id)
  {
    $validatedData = $request->validate([
      'user_id' => 'required|exists:users,id',
      'leave_type_id' => 'required|exists:leave_types,id',
      'allocated' => 'required|numeric|min:0',
      'taken' => 'required|numeric|min:0',
      'balance' => 'required',
    ]);

    $leaveBalance = LeaveBalance::findOrFail($id);

    $leaveBalance->update($validatedData);

    return response()->json(['message' => 'Leave balance updated successfully', 'data' => $leaveBalance]);
  }

  public function teamLeaves(Request $request)
  {
    $userId = $request->input('userId');
    $user = User::find($userId);
    $departmentId = $user->department_id;

    $leaves = Leave::with(['user', 'leave_type'])
      ->whereHas('user', function ($query) use ($departmentId) {
        $query->where('department_id', $departmentId);
      })
      ->get();

    return response()->json(['leaves' => $leaves]);
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'user_id' => 'required',
      'leave_type_id' => 'required',
      'from' => 'required|date',
      'to' => 'required|date|after_or_equal:from',
      'phone' => 'required|string|min:10',
      'days' => 'required|integer',
      'hod' => 'required|exists:users,id',
      'manager' => 'required|exists:users,id',
      'document' => 'nullable|file|mimes:pdf,doc,docx',
    ]);

    $startDate = new DateTime($request->from);
    $endDate = new DateTime($request->to);

    // Define holidays (can be moved to a database for flexibility)
    $holidays = [
      '12-25', // Christmas
      '12-26', // Boxing Day
      '01-01', // New Year's Day
    ];

    $leaveDays = 0;

    while ($startDate <= $endDate) {
      $isSunday = $startDate->format('N') == 7; // Sunday

      // Include all days except Sundays and holidays
      if (!$isSunday && !in_array($startDate->format('m-d'), $holidays)) {
        $leaveDays++;
      }

      $startDate->modify('+1 day');
    }

    // Validate the number of leave days
    if ($leaveDays != $request->days) {
      return response()->json(['error' => 'The number of leave days does not match the provided days.'], 400);
    }

    // Validate HOD and Manager
    $hodUser = User::find($request->hod);
    $managerUser = User::find($request->manager);

    if (!$hodUser || !$managerUser) {
      return response()->json(['error' => 'HOD or Manager not found.'], 404);
    }

    $hrUser = User::where('is_hr', 1)->first();

    if (!$hrUser) {
      return response()->json(['error' => 'HR user not found.'], 404);
    }

    // Handle document upload
    $documentName = null;
    if ($request->hasFile('document')) {
      $documentName = time() . '.' . $request->file('document')->extension();
      $request->file('document')->storeAs('leave/documents', $documentName, 'public');
    }

    // Create the leave record
    $leave = Leave::create([
      'user_id' => $request->user_id,
      'leave_type_id' => $request->leave_type_id,
      'from' => $request->from,
      'to' => $request->to,
      'days' => $leaveDays,
      'phone' => $request->phone,
      'comment' => $request->comment,
      'status' => 'Pending',
      'document' => $documentName,
    ]);

    // Update the leave balance
    $leaveType = LeaveType::find($request->leave_type_id);

    if ($leaveType) {
      $userLeaveBalance = LeaveBalance::firstOrCreate([
        'user_id' => $request->user_id,
        'leave_type_id' => $request->leave_type_id,
      ], [
        'balance' => $leaveType->days,
        'taken' => 0,
      ]);

      $userLeaveBalance->decrement('balance', $leaveDays);
      $userLeaveBalance->increment('taken', $leaveDays);
    } else {
      return response()->json(['error' => 'Leave type not found.'], 404);
    }

    // Log the leave creation action
    $this->logLeaveAction($leave, 'created', $request->user_id);

    // Notify relevant users
    Queue::push(function () use ($leave, $hodUser, $managerUser, $hrUser) {
      $usersToNotify = collect([$hodUser->email, $managerUser->email, $hrUser->email]);
      foreach ($usersToNotify as $email) {
        $user = User::where('email', $email)->first();
        if ($user) {
          $user->notify(new LeaveCreatedNotification($leave));
        }
      }
    });

    return response()->json(['message' => 'Leave application submitted successfully!']);
  }




  public function approveLeave(Request $request, Leave $leave)
  {

    try {

      Log::info('Approve Leave Request', [
        'userId' => $request->input('userId'),
        'leaveId' => $leave->id,
        'requestData' => $request->all()
    ]);

      $userId = $request->input('userId');
      $approver = User::find($userId);

      if ($approver) {
        if (
          ($approver->designation_id === 1 && $leave->status === 'Pending') ||
          ($approver->is_hr === 1 && $leave->status === 'Pending' && $approver->department_id === 1)
        ) {
          $leave->status = 'Manager Approved';
          $leave->save();

          $this->logLeaveAction($leave, 'Manager Approved', $userId);

          return response()->json(['message' => 'Leave approved successfully'], 200);
        } elseif ($approver->is_hr === 1 && $leave->status === 'Manager Approved') {
          $leave->status = 'Hr Approved';
          $leave->save();

          $this->logLeaveAction($leave, 'Hr Approved', $userId);

          return response()->json(['message' => 'Leave approved successfully'], 200);
        } elseif ($approver->is_hod === 1 && $leave->status === 'Hr Approved') {
          $leave->status = 'Approved';
          $leave->save();

          $this->logLeaveAction($leave, 'Approved', $userId);

          $employeePhone = $leave->phone;
          $approvedMessage = "Hello {$leave->user->firstname}, Your leave request from {$leave->from} to {$leave->to} has been approved.";

          $employee_unit_id = $leave->user->unit_id;

          $sms_util = new SMSUtil();
          $ug_sms_util = new UGSMSUtil();
          $tz_sms_util = new TZSMSUtil();

          if ($employee_unit_id == 2) {
            $ug_sms_util->sendSMS($employeePhone, $approvedMessage);
          } else if ($employee_unit_id == 3) {
            $tz_sms_util->sendSMS($employeePhone, $approvedMessage);
          } else {

            $sms_util->sendSMS($employeePhone, $approvedMessage);
          }
          return response()->json(['message' => 'Leave approved successfully'], 200);
        } else {
          return response()->json(['error' => 'Unauthorized or invalid status for approval'], 403);
        }
      } else {
        return response()->json(['error' => 'Approver not found'], 404);
      }
    } catch (\Exception $e) {
      Log::error($e);

      return response()->json(['error' => 'Failed to approve leave'], 500);
    }
  }

  public function cancelLeave(Request $request, Leave $leave)
  {
    try {
      if ($leave->status === 'Cancelled') {
        return response()->json(['error' => 'Leave is already Cancelled'], 400);
      }

      if (!$leave->user) {
        Log::error('Error: Attempting to cancel leave with no associated user');
        throw new \Exception('Error: Attempting to cancel leave with no associated user');
      }

      $leave->status = 'Cancelled';
      $leave->is_active == 0;
      $leave->comment = $request->comment;
      $leave->save();

      $userLeaveBalance = LeaveBalance::where('user_id', $leave->user_id)
        ->where('leave_type_id', $leave->leave_type_id)
        ->first();

      if ($userLeaveBalance) {
        $userLeaveBalance->increment('balance', $leave->days);
      }

      $this->logLeaveAction($leave, 'Cancelled', $request->input('userId'));

      $sender_phone = $leave->phone;
      $employeeName = $leave->user->firstname;

      $admin = User::find($request->userId);
      $firstName = $admin ? $admin->firstname : 'anonymous';

      $cancelMessage = "Hi $employeeName, Your Leave application from $leave->from to $leave->to has been Cancelled by " . $firstName . ", Reason: " . $request->comment;

      $sms_util = new SMSUtil();
      $sms_util->sendSMS($sender_phone, $cancelMessage);

      return response()->json(['message' => 'Leave canceled successfully'], 200);
    } catch (\Exception $e) {
      Log::error($e);
      return response()->json(['error' => 'Failed to cancel leave'], 500);
    }
  }

  public function userLeaveCancel(Request $request, Leave $leave)
  {
    $user_id = $leave->user_id;

    try {

      $leave->status = 'Cancelled';
      $leave->comment = $request->comment;
      $leave->is_active == 0;
      $leave->save();

      $userLeaveBalance = LeaveBalance::where('user_id', $leave->user_id)
        ->where('leave_type_id', $leave->leave_type_id)
        ->first();

      if ($userLeaveBalance) {
        $userLeaveBalance->increment('balance', $leave->days);
      }

      $this->logLeaveAction($leave, 'Cancelled', $user_id);

      return response()->json(['message' => 'Leave canceled successfully'], 200);
    } catch (\Exception $e) {
      Log::debug($e);
      return response()->json(['error' => 'Failed to cancel leave'], 500);
    }
  }

  protected function logLeaveAction($leave, $action, $userId)
  {
    $leave->logs()->create([
      'action' => $action,
      'user_id' => $userId,
    ]);
  }

  public function leaveLogs(Leave $leave)
  {
    $logs = $leave->logs()->with('user')->get();
    $formattedLogs = $logs->map(function ($log) {
      if ($log->user) {
        $fullName = $log->user->firstname . ' ' . $log->user->lastname;

        return [
          'user' => $fullName,
          'action' => $log->action,
          'time' => $log->created_at->toDateTimeString(),
        ];
      } else {
        return [
          'user' => 'Unknown User',
          'action' => $log->action,
          'time' => $log->created_at->toDateTimeString(),
        ];
      }
    });

    return response()->json(['logs' => $formattedLogs]);
  }

  public function statistics()
  {
    $users = User::all();

    $leaveTypes = LeaveType::all();

    $allUsersLeaveStatistics = [];

    foreach ($users as $user) {
      $userLeaves = Leave::where('user_id', $user->id)->get();

      $leaveBalances = LeaveBalance::where('user_id', $user->id)->get();

      $userLeaveStatistics = [];

      foreach ($leaveTypes as $leaveType) {
        $userLeaveStatistics[] = [
          'leave_type' => $leaveType->name,
          'leave_taken' => $userLeaves->where('leave_type_id', $leaveType->id)->count(),
          'remaining_balance' => $leaveBalances->where('leave_type_id', $leaveType->id)->first()->balance ?? 0,
        ];
      }

      $allUsersLeaveStatistics[] = [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'leave_statistics' => $userLeaveStatistics,
      ];
    }

    return response()->json(['leave_statistics' => $allUsersLeaveStatistics]);
  }

  public function update(Request $request, Leave $leave)
  {
    // Validate the request data
    $validatedData = $request->validate([
      'leave_type_id' => 'required',
      'from' => 'required|date',
      'to' => 'required|date',
      'days' => 'required|numeric',
      'comment' => 'nullable|string',
    ]);

    $leave->update($validatedData);

    return response()->json(['message' => 'Leave updated successfully'], 200);
  }

  public function destroy(Leave $leave)
  {
    $leave->delete();

    return response()->json(['message' => 'Leave deleted successfully']);
  }

  public function leaveForm(Request $request)
  {
    try {
      $leaveData = $request->leave;

      $user = User::with(['department', 'leaveBalances'])->findOrFail($leaveData['user']['id']);

      $pdf = PDF::loadView('leaves.leave_form', compact('leaveData', 'user'));

      return $pdf->download('invoice.pdf');
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function generateExcel(Request $request)
  {
    $request->input('leaves');

    return response()->json(['message' => 'Excel file generated successfully']);
  }

  public function generatePdf(Request $request)
  {

    return response()->json(['message' => 'PDF file generated successfully']);
  }
}
