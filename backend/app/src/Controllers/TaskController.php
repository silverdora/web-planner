<?php

namespace App\Controllers;

use App\Enums\Status;
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

    public function dashboard()
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $tasks = $this->taskService->getByUserId($user->id);

            return $this->sendSuccessResponse($tasks);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function get($vars = [])
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $task = $this->findOwnedTask((int)($vars['id'] ?? 0), (int)$user->id);
            if (!$task) {
                return;
            }

            return $this->sendSuccessResponse($task);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function create()
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $data = $this->getPostData();
            if (!is_array($data)) {
                return $this->sendErrorResponse('Invalid request body', 400);
            }

            $task = new Task($data);
            $task->userId = $user->id;
            $task->status = Status::Created->value;

            $createdTask = $this->taskService->create($task);

            return $this->sendSuccessResponse($createdTask, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function update($vars = [])
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $existingTask = $this->findOwnedTask((int)($vars['id'] ?? 0), (int)$user->id);
            if (!$existingTask) {
                return;
            }

            $data = $this->getPostData();
            if (!is_array($data)) {
                return $this->sendErrorResponse('Invalid request body', 400);
            }

            $task = new Task($data);
            $task->id = $existingTask->id;
            $task->userId = $existingTask->userId;

            $this->taskService->update($task);

            return $this->sendSuccessResponse($task);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function changeTaskStatus()
    {
        try {
            $data = $this->getPostData();
            if (!is_array($data)) {
                return $this->sendErrorResponse('Invalid request body', 400);
            }

            $status = (string)($data['status'] ?? '');
            $taskId = (int)($data['taskId'] ?? $data['task_id'] ?? 0);

            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $task = $this->findOwnedTask($taskId, (int)$user->id);
            if (!$task) {
                return;
            }


            $allowedStatuses = array_column(Status::cases(), 'value');

            if (!in_array($status, $allowedStatuses, true)) {
                return $this->sendErrorResponse('Invalid status', 400);
            }

            $task->status = $status;
            $this->taskService->update($task);

            return $this->sendSuccessResponse($task);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function delete($vars = [])
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $task = $this->findOwnedTask((int)($vars['id'] ?? 0), (int)$user->id);
            if (!$task) {
                return;
            }

            $this->taskService->delete($task->id);

            return $this->sendSuccessResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    private function findOwnedTask(int $id, int $userId)
    {
        if ($id <= 0) {
            $this->sendErrorResponse('Task ID is required.', 400);
            return null;
        }

        $task = $this->taskService->getById($id);

        if (!$task) {
            $this->sendErrorResponse('Task not found', 404);
            return null;
        }

        if ((int)($task->userId ?? 0) !== $userId) {
            $this->sendErrorResponse('Forbidden', 403);
            return null;
        }

        return $task;
    }
}

