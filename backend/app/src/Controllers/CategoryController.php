<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\CategoryService;
use App\Services\ICategoryService;
use InvalidArgumentException;

class CategoryController extends Controller
{
    private ICategoryService $categoryService;
    public function __construct()
    {
        $this->categoryService = new CategorxyService();
    }

    public function index(): void
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            $this->respondWithError('Unauthorized', 401);
            return;
        }

        $categories = $this->categoryService->getByUserId($user->id);

        $this->respond($categories, 200);
    }

    public function store(): void
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            $this->respondWithError('Unauthorized', 401);
            return;
        }

        $data = $this->getJsonInput();

        try {
            $category = $this->categoryService->create($user->id, $data);
            $this->respond($category, 201);
        } catch (InvalidArgumentException $e) {
            $this->respondWithError($e->getMessage(), 400);
        }
    }

    public function update(int $id): void
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            $this->respondWithError('Unauthorized', 401);
            return;
        }

        $data = $this->getJsonInput();

        try {
            $category = $this->categoryService->update($id, $user->id, $data);

            if (!$category) {
                $this->respondWithError('Category not found', 404);
                return;
            }

            $this->respond($category, 200);
        } catch (InvalidArgumentException $e) {
            $this->respondWithError($e->getMessage(), 400);
        }
    }

    public function destroy(int $id): void
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            $this->respondWithError('Unauthorized', 401);
            return;
        }

        $deleted = $this->categoryService->delete($id, $user->id);

        if (!$deleted) {
            $this->respondWithError('Category not found', 404);
            return;
        }

        $this->respond(['success' => true], 200);
    }
}
