<?php

namespace App\Framework;

class Controller
{
    public function __construct()
    {
    }

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

    protected function sendSuccessResponse($data = [], $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

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
     * Maps POST data (JSON) to an instance of the specified class
     *
     * @param string $className The fully qualified class name
     * @return object|null Returns an instance of the class or null if data is invalid
     */
    protected function mapPostDataToClass(string $className): ?object
    {
        $data = $this->getPostData();

        $instance = new $className();

        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }
}