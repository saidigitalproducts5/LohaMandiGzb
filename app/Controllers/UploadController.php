<?php

namespace App\Controllers;

/**
 * UploadController — serves files stored under writable/uploads.
 * Keeps user uploads outside the web root while exposing them via URL.
 * GET /uploads/{type}/{filename}
 */
class UploadController extends BaseController
{
    public function show(string $type, string $filename)
    {
        $safeType = basename((string) $type);
        $safeFile = basename((string) $filename);

        // Only allow known sub-folders.
        if (!in_array($safeType, ['products', 'kyc', 'catalog', 'documents', 'ads', 'sellers'], true)) {
            return $this->response->setStatusCode(404);
        }

        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $safeType . DIRECTORY_SEPARATOR . $safeFile;

        if (!is_file($path)) {
            return $this->response->setStatusCode(404);
        }

        $mime = mime_content_type($path);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) filesize($path))
            ->setBody(file_get_contents($path));
    }
}
