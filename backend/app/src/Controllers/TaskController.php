<?php

namespace App\Controllers;

use App\Enums\Status;
use App\Framework\Controller;
use App\Models\Task;
use App\Services\ITaskService;
use App\Services\TaskService;

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

            $filters = [
                'search' => $_GET['search'] ?? '',
                'status' => $_GET['status'] ?? '',
                'priority' => $_GET['priority'] ?? '',
                'category_id' => $_GET['category_id'] ?? '',
                'sort' => $_GET['sort'] ?? '',
                'page' => $_GET['page'] ?? 1,
                'limit' => $_GET['limit'] ?? 10,
            ];

            $result = $this->taskService->getDashboardTasks((int)$user->id, $filters);

            return $this->sendSuccessResponse($result);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function dashboardStats()
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $stats = $this->taskService->getDashboardStats((int)$user->id);

            return $this->sendSuccessResponse($stats);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
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

            $task = new Task([
                'id' => $existingTask->id,
                'user_id' => $existingTask->userId,
                'title' => $data['title'] ?? $existingTask->title,
                'description' => $data['description'] ?? $existingTask->description,
                'category_id' => array_key_exists('category_id', $data) ? $data['category_id'] : $existingTask->categoryId,
                'priority' => $data['priority'] ?? $existingTask->priority,
                'status' => $data['status'] ?? $existingTask->status,
                'due_date' => $data['due_date'] ?? $data['dueDate'] ?? $existingTask->dueDate,
            ]);

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

            $this->taskService->delete($task->id, (int)$user->id);

            return $this->sendSuccessResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    private function findOwnedTask(int $taskId, int $userId): ?Task
    {
        if ($taskId <= 0) {
            $this->sendErrorResponse('Invalid task ID', 400);
            return null;
        }

        $task = $this->taskService->getByIdAndUserId($taskId, $userId);

        if (!$task) {
            $this->sendErrorResponse('Task not found', 404);
            return null;
        }

        return $task;
    }
}

