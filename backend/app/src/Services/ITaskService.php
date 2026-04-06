<?php

namespace App\Services;

use App\Models\Task;

interface ITaskService
{
    /**
     * Get per-category dashboard statistics for the given user.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getDashboardCategoryStats(int $userId): array;

    /**
     * Get upcoming tasks for a user.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getUpcomingTasks(int $userId): array;

    /**
     * Get a task by id for a specific user.
     *
     * @param int $id
     * @param int $userId
     *
     * @return Task|null
     */
    public function getByIdAndUserId(int $id, int $userId): ?Task;

    /**
     * Get all tasks for a user.
     *
     * @param int $userId
     *
     * @return Task[]
     */
    public function getByUserId(int $userId): array;

    /**
     * Get dashboard tasks for a user with filters and pagination.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return array
     */
    public function getDashboardTasks(int $userId, array $filters): array;

    /**
     * Get dashboard summary statistics for a user.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return array
     */
    public function getDashboardStats(int $userId, array $filters = []): array;

    /**
     * Create a new task.
     *
     * @param Task $task
     *
     * @return Task
     */
    public function create(Task $task): Task;

    /**
     * Update an existing task.
     *
     * @param Task $task
     *
     * @return bool
     */
    public function update(Task $task): bool;

    /**
     * Delete a task for a user by id.
     *
     * @param int $id
     * @param int $userId
     *
     * @return void
     */
    public function delete(int $id, int $userId): void;
}
