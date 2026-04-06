<?php

namespace App\Controllers;
use App\Models\User;
use App\Models\UserDTO;
use App\Exceptions\UserAlreadyExistsException;
use App\Services\IAuthService;
use App\Services\AuthService;
use App\Framework\Controller;

class AuthController extends Controller
{
    private IAuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
        parent::__construct();
    }

    /**
     * Handle user login.
     *
     * Reads JSON credentials from the request body, authenticates the user
     * and returns a JWT token together with a user DTO on success.
     *
     * @return void
     */
    public function login()
    {
        try {
            $userData = $this->getPostData();

            if (empty($userData['email']) || empty($userData['password'])) {
                return $this->sendErrorResponse('Email and password are required', 400);
            }

            // call the auth service to authenticate the user
            $user = $this->authService->authenticate($userData['email'], $userData['password']);

            if (!$user) {
                return $this->sendErrorResponse('Invalid credentials', 401);
            }

            // A DTO is used to return the user data to the client
            $userDTO = new UserDTO($user);
            $token = $this->authService->generateToken($user);

            return $this->sendSuccessResponse([
                'user' => $userDTO->toArray(),
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    /**
     * Handle user registration.
     *
     * Creates a new user from JSON payload and returns a DTO on success.
     *
     * @return void
     */
    public function register()
    {
        try {
            $userData = $this->getPostData();
            $user = new User(
                null,
                $userData['name'] ?? '',
                $userData['email'] ?? '',
                $userData['password'] ?? ''
            );


            // basic, partial validation
            if (empty($user->email) || empty($user->password) || empty($user->name)) {
                return $this->sendErrorResponse('Email, username, and password are required', 400);
            }

            $user = $this->authService->register($user);

            // Return user data (excluding password for security)
            // DTOs (data transfer objects) are preferred when returning data to the client
            $userDTO = new UserDTO($user);
            return $this->sendSuccessResponse($userDTO, 201);

        } catch (UserAlreadyExistsException $e) {
            return $this->sendErrorResponse($e->getMessage(), 409); // 409: Conflict
        }
//        catch (\Exception $e) {
//            return $this->sendErrorResponse('Internal server error', 500);
//        }
    }

    /**
     * Logout endpoint.
     *
     * This does not invalidate tokens server-side but returns a success
     * message so the client can clear its local state.
     *
     * @return void
     */
    public function logout()
    {
        return $this->sendSuccessResponse(['message' => 'Logged out']);
    }
}
