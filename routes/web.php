<?php

// Định nghĩa routes thông qua Router ($this->router đã được khởi tạo trong App.php)
/** @var Router $router */
$router = $this->router;

// ---------- USER ROUTES ----------
$router->get('/', 'HomeController', 'index');
$router->get('/home', 'HomeController', 'index');

// Ví dụ route cho Movie, User, Authentication...
// $router->get('/movies', 'MovieController', 'index');
// $router->get('/movies/show', 'MovieController', 'show');

// ---------- ADMIN ROUTES ----------
// Ví dụ: $router->get('/admin/movies', 'AdminMovieController', 'index');