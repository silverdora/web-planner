<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\CategoryService;
use App\Services\ICategoryService;

class CategoryController extends Controller
{
    private ICategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    /**
     * Get all categories for the authenticated user.
     *
     * @return void
     */
    public function getAll()
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $categories = $this->categoryService->getByUserId($user->id);
            return $this->sendSuccessResponse($categories);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get a single category for the authenticated user.
     *
     * @param array $vars Route variables, expects 'id'.
     *
     * @return void
     */
    public function get($vars = [])
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $id = (int)($vars['id'] ?? 0);
            if ($id <= 0) {
                return $this->sendErrorResponse('Category ID is required.', 400);
            }
            $category = $this->categoryService->getByIdAndUserId($id, $user->id);

            if (!$category) {
                return $this->sendErrorResponse('Category not found', 404);
            }

            return $this->sendSuccessResponse($category);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Create a new category for the authenticated user.
     *
     * Reads JSON body with category data.
     *
     * @return void
     */
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

            $created = $this->categoryService->create($user->id, $data);

            return $this->sendSuccessResponse($created, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update a category for the authenticated user.
     *
     * @param array $vars Route variables, expects 'id'.
     *
     * @return void
     */
    public function update($vars = [])
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $id = (int)($vars['id'] ?? 0);
            $data = $this->getPostData();
            if (!is_array($data)) {
                return $this->sendErrorResponse('Invalid request body', 400);
            }

            $category = $this->categoryService->update($id, $user->id, $data);

            if (!$category) {
                return $this->sendErrorResponse('Category not found', 404);
            }

            return $this->sendSuccessResponse($category);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete a category for the authenticated user.
     *
     * @param array $vars Route variables, expects 'id'.
     *
     * @return void
     */
    public function delete($vars = [])
    {
        try {
            $user = $this->requireAuth();
            if (!$user) {
                return;
            }

            $id = (int)($vars['id'] ?? 0);
            $deleted = $this->categoryService->delete($id, $user->id);

            if (!$deleted) {
                return $this->sendErrorResponse('Category not found', 404);
            }

            return $this->sendSuccessResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }
}
