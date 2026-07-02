<?php
$c = mysqli_connect("localhost", "root", "123456", "movie_ticket_booking", "3308");
$r = mysqli_query($c, "DESCRIBE users");
while ($row = mysqli_fetch_assoc($r)) {
    echo $row["Field"] . " - " . $row["Type"] . PHP_EOL;
}
?>
