<?php

$page = $_GET['page'] ?? 'movie';


switch ($page) {

    case 'movie':
        require_once "../app/views/booking/movie_list.php";
        break;


    case 'showtime':
        require_once "../app/views/booking/select_showtime.php";
        break;


    case 'seat':
        require_once "../app/views/booking/select_seat.php";
        break;


    default:
        require_once "../app/views/booking/movie_list.php";
}

?>