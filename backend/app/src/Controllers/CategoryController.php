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
            $category = $this->categoryService->getByIdAndUserId($id, $user->id);

            if (!$category) {
                return $this->sendErrorResponse('Category not found', 404);
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

            $created = $this->categoryService->create($user->id, $data);

            return $this->sendSuccessResponse($created, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
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

    public function delete($vars = [])
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                return $this->sendErrorResponse('Unauthorized', 401);
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
