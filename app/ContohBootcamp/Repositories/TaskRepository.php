<?php

namespace App\ContohBootcamp\Repositories;

use App\Helpers\MongoModel;

class TaskRepository
{
    private MongoModel $tasks;
    public function __construct()
    {
        $this->tasks = new MongoModel('tasks');
    }

    /**
     * Untuk mengambil semua tasks
     */
    public function getAll()
    {
        $tasks = $this->tasks->get([]);
        return $tasks;
    }

    /**
     * Untuk mendapatkan task bedasarkan id
     *  */
    public function getById(string $id)
    {
        $task = $this->tasks->find(['_id' => $id]);
        return $task;
    }

    /**
     * Untuk membuat task
     */
    public function create(array $data)
    {
        $dataSaved = [
            'title' => $data['title'],
            'description' => $data['description'],
            'assigned' => null,
            'subtasks' => [],
            'created_at' => time()
        ];

        $id = $this->tasks->save($dataSaved);
        return $id;
    }

    /**
     * Untuk menyimpan task baik untuk membuat baru atau menyimpan dengan struktur bson secara bebas
     *  */
    public function save(array $editedData)
    {
        $id = $this->tasks->save($editedData);
        return $id;
    }

    /**
     * Untuk mendelete task
     */
    public function deleteTask(string $taskId): bool
    {
        $existTask = $this->tasks->find(['_id' => $taskId]);

        if (!$existTask) {
            return false; // Or throw an exception if you prefer
        }

        $this->tasks->deleteQuery(['_id' => $taskId]);

        return true;
    }

    /**
     * Untuk mengambil subtask dari task
     */
    public function getSubtasks(array $task)
    {
        return isset($task['subtasks']) ? $task['subtasks'] : [];
    }

    /**
     * Untuk menghapus subtask berdasarkan id
     */
    public function deleteSubtask(string $taskId, string $subtaskId): bool
    {
        $existTask = $this->tasks->find(['_id' => $taskId]);

        if (!$existTask) {
            return false; // Or throw an exception if you prefer
        }

        $subtasks = isset($existTask['subtasks']) ? $existTask['subtasks'] : [];

        // Pencarian dan penghapusan subtask
        $subtasks = array_filter($subtasks, function ($subtask) use ($subtaskId) {
            return $subtask['_id'] != $subtaskId;
        });

        $existTask['subtasks'] = array_values($subtasks);

        $this->tasks->save($existTask);

        return true;
    }
}
