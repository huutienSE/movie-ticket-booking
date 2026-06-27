<?php

require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController
{
    public function index()
    {
        // Dữ liệu giả lập, sau này sẽ lấy từ Database/Model
        $data = [
            'title' => 'Trang Chủ',
            'theaters' => [
                ['id' => 1, 'name' => 'Cinema Quốc Thanh (TP.HCM)'],
                ['id' => 2, 'name' => 'Cinema Sinh Viên (TP.HCM)'],
                ['id' => 3, 'name' => 'Cinema Đà Lạt (Lâm Đồng)'],
                ['id' => 4, 'name' => 'Cinema Lâm Đồng (Đức Trọng)'],
            ]
        ];

        // Gọi method view từ BaseController
        $this->view('home/index', $data);
    }
}