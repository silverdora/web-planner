<?php

namespace App\Controllers;

use App\Models\Task;
use App\Services\ITaskService;
use App\Services\TaskService;
use App\Framework\Controller;

class TaskController extends Controller
{
    private ITaskService $taskService;

    public function __construct()
    {
        $this->taskService = new TaskService();
    }

    public function getAll()
    {
        try {
            $tasks = $this->taskService->getAll();
            return $this->sendSuccessResponse($tasks);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function get($vars = [])
    {
        try {
            $id = (int)($vars['id'] ?? 0);
            $task = $this->taskService->getById($id);
            
            if (!$task) {
                return $this->sendErrorResponse('Task not found', 404);
            }
            return $this->sendSuccessResponse($task);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function create()
    {
        // try {
            $task = $this->mapPostDataToClass(Task::class);
            $task = $this->taskService->create($task);
            return $this->sendSuccessResponse($task, 201);
        // } catch (\Exception $e) {
        //     //return $this->sendErrorResponse('Internal server error', 500);
        // }
    }

    public function update($vars = [])
    {
        try {
            $task = $this->mapPostDataToClass(Task::class);
            $id = (int)($vars['id'] ?? 0);
            $task->id = $id;
            $this->taskService->update($task);
            return $this->sendSuccessResponse($task);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function delete($vars = [])
    {
        try {
            $id = (int)($vars['id'] ?? 0);
            $this->taskService->delete($id);
            return $this->sendSuccessResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }


}