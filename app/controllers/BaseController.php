<?php

class BaseController
{
    protected function view($viewPath, $data = [], $layout = 'user')
    {
        // Extract variables to be available in view
        extract($data);

        // Chọn layout dựa trên loại controller (admin vs user)
        if ($layout === 'admin') {
            $header = __DIR__ . '/../views/admin/layouts/header.php';
            $footer = __DIR__ . '/../views/admin/layouts/footer.php';
        } else {
            $header = __DIR__ . '/../views/layouts/header.php';
            $footer = __DIR__ . '/../views/layouts/footer.php';
        }

        // Include header, view and footer
        if (file_exists($header)) require_once $header;
        require_once __DIR__ . '/../views/' . $viewPath . '.php';
        if (file_exists($footer)) require_once $footer;
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($path)
    {
        redirect($path);
    }
}