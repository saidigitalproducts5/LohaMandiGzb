<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

/**
 * ApiController — shared helpers for all JSON API controllers.
 */
abstract class ApiController extends ResourceController
{
    protected function respondOK($data = null, string $message = 'OK'): ResponseInterface
    {
        return $this->respond([
            'status'  => 'success',
            'code'    => ResponseInterface::HTTP_OK,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function respondCreated($data = null, string $message = 'Created'): ResponseInterface
    {
        return $this->respond([
            'status'  => 'success',
            'code'    => ResponseInterface::HTTP_CREATED,
            'message' => $message,
            'data'    => $data,
        ], ResponseInterface::HTTP_CREATED);
    }

    protected function respondValidationErrors($errors, string $message = 'Validation failed'): ResponseInterface
    {
        return $this->failValidationErrors([
            'message' => $message,
            'errors'  => $errors,
        ]);
    }

    protected function respondNotFound(string $message = 'Resource not found'): ResponseInterface
    {
        return $this->failNotFound($message);
    }

    protected function respondUnauthorized(string $message = 'Unauthorized'): ResponseInterface
    {
        return $this->failUnauthorized($message);
    }

    protected function respondForbidden(string $message = 'Forbidden'): ResponseInterface
    {
        return $this->failForbidden($message);
    }

    protected function respondServerError(string $message = 'Internal server error'): ResponseInterface
    {
        return $this->failServerError($message);
    }

    /**
     * Current authenticated user id from JWT payload.
     */
    protected function userId(): ?int
    {
        $jwt = $this->request->jwt ?? null;

        return isset($jwt['sub']) ? (int) $jwt['sub'] : null;
    }

    protected function userRole(): ?string
    {
        $jwt = $this->request->jwt ?? null;

        return $jwt['role'] ?? null;
    }

    /**
     * Parse CSV-ish filter list "1,2,3" into int array.
     */
    protected function intList($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        return array_values(array_filter(array_map('intval', explode(',', (string) $value))));
    }

    /**
     * Convert uploaded file to a stored path under public/uploads.
     */
    protected function storeUpload($file, string $folder): ?string
    {
        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/' . $folder, $newName);

        return $folder . '/' . $newName;
    }
}
