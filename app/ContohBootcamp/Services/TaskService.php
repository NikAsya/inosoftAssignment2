<?php

namespace App\ContohBootcamp\Services;

use App\ContohBootcamp\Repositories\TaskRepository;
use Illuminate\Support\Facades\Response;
use MongoDB\BSON\ObjectId;

class TaskService
{
    private TaskRepository $taskRepository;

    public function __construct()
    {
        $this->taskRepository = new TaskRepository();
    }

    /**
     * NOTE: untuk mengambil semua tasks di collection task
     */
    public function getTasks()
    {
        $tasks = $this->taskRepository->getAll();
        return $tasks;
    }

    /**
     * NOTE: menambahkan task
     */
    public function addTask(array $data)
    {
        $taskId = $this->taskRepository->create($data);
        return $taskId;
    }

    /**
     * NOTE: UNTUK mengambil data task
     */
    public function getById(string $taskId)
    {
        $task = $this->taskRepository->getById($taskId);
        return $task;
    }

    /**
     * NOTE: untuk update task
     */
    public function updateTask(array $editTask, array $formData)
    {
        if (isset($formData['title'])) {
            $editTask['title'] = $formData['title'];
        }

        if (isset($formData['description'])) {
            $editTask['description'] = $formData['description'];
        }

        $id = $this->taskRepository->save($editTask);
        return $id;
    }

    /**
     * Note: to Assign Task
     */
    public function assignTask(string $taskId, string $assigned)
    {
        $existTask = $this->taskRepository->getById($taskId);

        if (!$existTask) {
            return Response::json(["message" => "Task $taskId not found"], 401);
        }

        $existTask['assigned'] = $assigned;

        $this->taskRepository->save($existTask);

        return Response::json($existTask);
    }

    /**
     * Note: to Un-Assign Task
     */
    public function unassignTask(string $taskId)
    {
        $existTask = $this->taskRepository->getById($taskId);

        if (!$existTask) {
            return response()->json([
                "message" => "Task " . $taskId . " tidak ada"
            ], 401);
        }

        $existTask['assigned'] = null;

        $this->taskRepository->save($existTask);

        return $this->taskRepository->getById($taskId);
    }

    /**
     * Note: to Delete Task
     */
    public function deleteTask(string $taskId): bool
    {
        return $this->taskRepository->deleteTask($taskId);
    }

    /**
     * Note: to Create-Sub Task
     */
    public function createSubtask(string $taskId, string $title, string $description)
    {
        $existTask = $this->taskRepository->getById($taskId);

        if (!$existTask) {
            return response()->json([
                "message" => "Task " . $taskId . " tidak ada"
            ], 401);
        }

        $subtasks = $this->taskRepository->getSubtasks($existTask);

        $subtasks[] = [
            '_id' => (string) new \MongoDB\BSON\ObjectId(),
            'title' => $title,
            'description' => $description
        ];

        $existTask['subtasks'] = $subtasks;

        $this->taskRepository->save($existTask);

        return $this->taskRepository->getById($taskId);
    }

    /**
     * Note: to Delete Subtask
     */
    public function deleteSubtask(string $taskId, string $subtaskId)
    {
        $existTask = $this->taskRepository->getById($taskId);

        if (!$existTask) {
            return Response::json(["message" => "Task $taskId not found"], 401);
        }

        $subtasks = isset($existTask['subtasks']) ? $existTask['subtasks'] : [];

        // Pencarian dan penghapusan subtask
        $subtasks = array_filter($subtasks, function ($subtask) use ($subtaskId) {
            return $subtask['_id'] != $subtaskId;
        });

        $existTask['subtasks'] = array_values($subtasks);

        $this->taskRepository->save($existTask);

        return Response::json($existTask);
    }
}
