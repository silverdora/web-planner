<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\Services\ICategoryService;

class CategoryController extends Controller
{
    private ICategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    public function getAll()
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                return $this->sendErrorResponse('Unauthorized', 401);
            }

            $categories = $this->categoryService->getByUserId($user->id);
            return $this->sendSuccessResponse($categories);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function get($vars = [])
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                return $this->sendErrorResponse('Unauthorized', 401);
            }

            $id = (int)($vars['id'] ?? 0);
            $category = $this->categoryService->getById($id);

            if (!$category) {
                return $this->sendErrorResponse('Category not found', 404);
            }

            if ((int)$category->userId !== (int)$user->id) {
                return $this->sendErrorResponse('Forbidden', 403);
            }

            return $this->sendSuccessResponse($category);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                return $this->sendErrorResponse('Unauthorized', 401);
            }

            $data = $this->getPostData();

            $category = new Category([
                'name' => $data['name'] ?? '',
                'user_id' => $user->id,
            ]);

            $created = $this->categoryService->create($category);

            return $this->sendSuccessResponse($created, 201);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function update($vars = [])
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                return $this->sendErrorResponse('Unauthorized', 401);
            }

            $id = (int)($vars['id'] ?? 0);
            $data = $this->getPostData();

            $category = new Category([
                'id' => $id,
                'name' => $data['name'] ?? '',
                'user_id' => $user->id,
            ]);

            $this->categoryService->update($category);

            return $this->sendSuccessResponse($category);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function delete($vars = [])
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                return $this->sendErrorResponse('Unauthorized', 401);
            }

            $id = (int)($vars['id'] ?? 0);
            $this->categoryService->delete($id, $user->id);

            return $this->sendSuccessResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }
}
