<?php

namespace App\Framework;

class Controller
{
    public function __construct()
    {
    }

    /**
     * Get the authenticated user from the Authorization bearer token.
     *
     * @return object|null Simple object with id, email and name, or null if unauthenticated/invalid token.
     */
    protected function getAuthenticatedUser()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);

        try {
            $decoded = \Firebase\JWT\JWT::decode(
                $token,
                new \Firebase\JWT\Key(\App\Config::$secretKey, 'HS256')
            );

            $userData = $decoded->data ?? null;

            if (!$userData || !isset($userData->id)) {
                return null;
            }

            return (object)[
                'id' => $userData->id,
                'email' => $userData->email ?? null,
                'name' => $userData->name ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Send a JSON success response.
     *
     * @param mixed $data Payload to serialize as JSON.
     * @param int   $code HTTP status code, defaults to 200.
     *
     * @return void
     */
    protected function sendSuccessResponse($data = [], $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Send a JSON error response.
     *
     * @param string $message Error message.
     * @param int    $code    HTTP status code, defaults to 500.
     *
     * @return void
     */
    protected function sendErrorResponse($message, $code = 500)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode(['error' => $message], JSON_PRETTY_PRINT);
    }

    /**
     * Gets and decodes JSON data from the request body
     *
     * @return array|null Returns decoded JSON data as array or null if invalid
     */
    protected function getPostData(): ?array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    /**
     * Ensure the request is authenticated and return the user.
     *
     * Sends a 401 Unauthorized error response and returns null if no valid
     * token/user is found.
     *
     * @return object|null
     */
    protected function requireAuth()
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            $this->sendErrorResponse('Unauthorized', 401);
            return null;
        }

        return $user;
    }
}