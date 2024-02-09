<?php

namespace App\Http\Controller;

use App\ContohBootcamp\Services\TaskService;
use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class TaskController extends Controller
{
    private TaskService $taskService;
    public function __construct()
    {
        $this->taskService = new TaskService();
    }

    public function showTasks()
    {
        try {
            $tasks = $this->taskService->getTasks();
            return response()->json(['data' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3',
            'description' => 'required|string'
        ]);

        $data = [
            'title' => $request->post('title'),
            'description' => $request->post('description')
        ];

        $dataSaved = [
            'title' => $data['title'],
            'description' => $data['description'],
            'assigned' => null,
            'subtasks' => [],
            'created_at' => time()
        ];

        $id = $this->taskService->addTask($dataSaved);
        $task = $this->taskService->getById($id);

        return response()->json($task);
    }


    public function updateTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|string',
            'title' => 'string',
            'description' => 'string',
            'assigned' => 'string',
            'subtasks' => 'array',
        ]);

        $taskId = $request->post('task_id');
        $formData = $request->only('title', 'description', 'assigned', 'subtasks');
        $task = $this->taskService->getById($taskId);

        $this->taskService->updateTask($task, $formData);

        $task = $this->taskService->getById($taskId);

        return response()->json($task);
    }

    // NEW: deleteTask()
    public function deleteTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required',
        ]);

        $taskId = $request->input('task_id');

        try {
            $success = $this->taskService->deleteTask($taskId);

            if ($success) {
                return response()->json(['message' => "Successfully deleted task $taskId"]);
            } else {
                return response()->json(['message' => "Task $taskId not found"], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    //NEW: AssignTask()
    public function assignTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required',
            'assigned' => 'required',
        ]);

        $taskId = $request->get('task_id');
        $assigned = $request->post('assigned');

        $response = $this->taskService->assignTask($taskId, $assigned);

        return response()->json($response);
    }

    // NEW: unassignTask()
    public function unassignTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required',
        ]);

        $taskId = $request->post('task_id');
        $response = $this->taskService->unassignTask($taskId);

        return response()->json($response);
    }

    // NEW: createSubtask()
    public function createSubtask(Request $request)
    {
        $request->validate([
            'task_id' => 'required',
            'title' => 'required|string',
            'description' => 'required|string'
        ]);

        $taskId = $request->post('task_id');
        $title = $request->post('title');
        $description = $request->post('description');

        try {
            $createdSubtask = $this->taskService->createSubtask($taskId, $title, $description);

            return response()->json($createdSubtask);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // NEW: deleteSubTask()
    public function deleteSubtask(Request $request)
    {
        $request->validate([
            'task_id' => 'required',
            'subtask_id' => 'required'
        ]);

        $taskId = $request->post('task_id');
        $subtaskId = $request->post('subtask_id');

        $success = $this->taskService->deleteSubtask($taskId, $subtaskId);

        if (!$success) {
            return response()->json(["message" => "Task $taskId not found"], 401);
        }

        $task = $this->taskService->getById($taskId);

        return response()->json($task);
    }
}
