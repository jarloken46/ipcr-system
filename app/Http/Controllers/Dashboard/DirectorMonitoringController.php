<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Department;
use App\Models\IpcrSubmission;
use App\Models\OpcrSubmission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DirectorMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $director = $request->user();
        $directorDepartmentId = $director->department_id;

        $departments = Department::query()
            ->orderBy('name')
            ->get();

        $deansByDepartment = User::query()
            ->whereHas('userRoles', function ($query) {
                $query->where('role', 'dean');
            })
            ->with('department:id,name,code')
            ->get()
            ->groupBy('department_id');

        $ipcrSubmissions = IpcrSubmission::query()
            ->where('status', 'submitted')
            ->whereNotNull('submitted_at')
            ->with(['user:id,name,employee_id,department_id', 'user.department:id,name,code'])
            ->orderByDesc('submitted_at')
            ->get();

        $opcrSubmissions = OpcrSubmission::query()
            ->where('status', 'submitted')
            ->whereNotNull('submitted_at')
            ->with(['user:id,name,employee_id,department_id', 'user.department:id,name,code'])
            ->orderByDesc('submitted_at')
            ->get();

        $facultyIpcrSubmissions = IpcrSubmission::query()
            ->where('status', 'submitted')
            ->whereNotNull('submitted_at')
            ->whereHas('user.userRoles', function ($query) {
                $query->where('role', 'faculty');
            })
            ->with(['user:id,name,employee_id,department_id', 'user.department:id,name,code'])
            ->orderByDesc('submitted_at')
            ->get();

        $userRole = $director->getPrimaryRole() ?? 'director';
        $notifications = AdminNotification::active()
            ->forAudience($userRole)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $readNotifIds = DB::table('notification_reads')
            ->where('user_id', $director->id)
            ->whereIn('notification_id', $notifications->pluck('id'))
            ->pluck('notification_id')
            ->toArray();

        $unreadCount = $notifications->whereNotIn('id', $readNotifIds)->count();

        return view('dashboard.director.monitoring', [
            'departments' => $departments,
            'deansByDepartment' => $deansByDepartment,
            'ipcrByDepartment' => $ipcrSubmissions->groupBy('user.department_id'),
            'opcrByDepartment' => $opcrSubmissions->groupBy('user.department_id'),
            'facultyIpcrByDepartment' => $facultyIpcrSubmissions->groupBy('user.department_id'),
            'directorDepartmentId' => $directorDepartmentId,
            'notifications' => $notifications,
            'readNotifIds' => $readNotifIds,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function showIpcr(Request $request, IpcrSubmission $submission): View|JsonResponse
    {
        $submission->load(['user:id,name,employee_id,department_id', 'user.department:id,name,code', 'user.userRoles']);

        $this->ensureSubmitted($submission->status, $submission->submitted_at);

        $isFacultySubmission = $submission->user?->hasRole('faculty') ?? false;
        if (! $isFacultySubmission) {
            $this->ensureDirectorDepartmentAccess($request, $submission->user?->department_id);
        }
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'submission' => $this->buildSubmissionPayload($submission),
            ]);
        }

        return view('dashboard.director.monitoring-show', [
            'submission' => $submission,
            'submissionType' => 'ipcr',
        ]);
    }

    public function showOpcr(Request $request, OpcrSubmission $submission): View
    {
        $submission->load(['user:id,name,employee_id,department_id', 'user.department:id,name,code']);

        $this->ensureSubmitted($submission->status, $submission->submitted_at);
        $this->ensureDirectorDepartmentAccess($request, $submission->user?->department_id);

        return view('dashboard.director.monitoring-show', [
            'submission' => $submission,
            'submissionType' => 'opcr',
        ]);
    }

    private function ensureDirectorDepartmentAccess(Request $request, ?int $submissionDepartmentId): void
    {
        $directorDepartmentId = $request->user()->department_id;

        if (!$directorDepartmentId || !$submissionDepartmentId) {
            abort(403, 'No department assigned.');
        }

        if ((int) $submissionDepartmentId !== (int) $directorDepartmentId) {
            abort(403, 'You do not have access to this submission.');
        }
    }

    private function ensureSubmitted(?string $status, $submittedAt): void
    {
        if ($status !== 'submitted' || ! $submittedAt) {
            abort(404);
        }

        if ($status !== 'submitted' || !$submittedAt) {
            abort(404);
        }
    }
    
    private function buildSubmissionPayload(IpcrSubmission $submission): array
    {
        return [
            'id' => $submission->id,
            'title' => $submission->title,
            'school_year' => $submission->school_year,
            'semester' => $submission->semester,
            'table_body_html' => $submission->table_body_html,
            'noted_by' => $submission->noted_by,
            'approved_by' => $submission->approved_by,
            'submitted_at' => $submission->submitted_at?->format('M d, Y'),
            'user' => [
                'name' => $submission->user?->name,
                'employee_id' => $submission->user?->employee_id,
                'department' => [
                    'code' => $submission->user?->department?->code,
                    'name' => $submission->user?->department?->name,
                ],
            ],
        ];
    }
}
