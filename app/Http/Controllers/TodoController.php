<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exports\TodosExport;
use Maatwebsite\Excel\Facades\Excel;

class TodoController extends Controller
{
    public function store(StoreTodoRequest $request): JsonResponse
    {

        $todo = Todo::create($request->validated());

        return response()->json([
            'message' => 'Todo created successfully!',
            'data' => $todo,
        ], 201);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only([
            'title',
            'assignee',
            'due_date_start',
            'due_date_end',
            'time_tracked_min',
            'time_tracked_max',
            'status',
            'priority'
        ]);

        $request->validate([
            'title' => 'nullable|string',
            'assignee' => 'nullable|string',
            'due_date_start' => 'nullable|date_format:Y-m-d',
            'due_date_end' => 'nullable|date_format:Y-m-d|after_or_equal:due_date_start',
            'time_tracked_min' => 'nullable|numeric|min:0',
            'time_tracked_max' => 'nullable|numeric|min:0|gte:time_tracked_min',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
        ]);

        $fileName = 'todos_report_' . date('Ymd_His') . '.xlsx';


        return Excel::download(new TodosExport($filters), $fileName);
    }


    public function getChartData(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', \Illuminate\Validation\Rule::in(['status', 'priority', 'assignee'])],
        ]);

        $type = $request->query('type');
        $data = [];

        switch ($type) {
            case 'status':
                $statusCounts = Todo::select('status')
                    ->selectRaw('COUNT(*) as total_count')
                    ->groupBy('status')
                    ->pluck('total_count', 'status')
                    ->toArray();

                $allStatuses = ['pending', 'open', 'in_progress', 'completed'];
                $statusSummary = array_fill_keys($allStatuses, 0);
                $statusSummary = array_merge($statusSummary, $statusCounts);
                ksort($statusSummary);

                $data = ['status_summary' => $statusSummary];
                break;

            case 'priority':
                $priorityCounts = Todo::select('priority')
                    ->selectRaw('COUNT(*) as total_count')
                    ->groupBy('priority')
                    ->pluck('total_count', 'priority')
                    ->toArray();

                $allPriorities = ['low', 'medium', 'high'];
                $prioritySummary = array_fill_keys($allPriorities, 0);
                $prioritySummary = array_merge($prioritySummary, $priorityCounts);
                ksort($prioritySummary);

                $data = ['priority_summary' => $prioritySummary];
                break;

            case 'assignee':
                $todos = Todo::select('assignee', 'status', 'time_tracked')->get();

                $assigneeSummary = $todos->groupBy(function ($todo) {
                    return $todo->assignee ?? 'Unassigned';
                })
                    ->mapWithKeys(function ($todosByAssignee, $assigneeName) {
                        $totalTodos = $todosByAssignee->count();
                        $totalPendingTodos = $todosByAssignee->where('status', 'pending')->count();
                        $totalTimetrackedCompletedTodos = (int) $todosByAssignee->where('status', 'completed')->sum('time_tracked');

                        return [
                            $assigneeName => [
                                'total_todos' => $totalTodos,
                                'total_pending_todos' => $totalPendingTodos,
                                'total_timetracked_completed_todos' => $totalTimetrackedCompletedTodos,
                            ]
                        ];
                    })->toArray();

                ksort($assigneeSummary);

                $data = ['assignee_summary' => $assigneeSummary];
                break;
        }

        return response()->json($data, 200);
    }

}
