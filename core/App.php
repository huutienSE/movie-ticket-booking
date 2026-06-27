<?php

class App
{
    protected $router;

    public function __construct()
    {
        // Khởi tạo session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Load helper files
        require_once __DIR__ . '/../app/helpers/url_helper.php';

        // Khởi tạo Router
        require_once __DIR__ . '/Router.php';
        $this->router = new Router();
        
        // Khởi tạo Database kết nối (singleton)
        require_once __DIR__ . '/Database.php';
    }

    public function run()
    {
        // Load routes
        require_once __DIR__ . '/../routes/web.php';

        // Lấy URI và Method hiện tại
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Dispatch route
        $this->router->dispatch($uri, $method);
    }
}
