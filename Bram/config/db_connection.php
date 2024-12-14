<?php
    $db_server ="localhost";
    $db_user ="root";
    $db_pass ="";
    $db_name ="tokokapi";
    $conn ="";

    // Buat koneksi
    $conn =mysqli_connect($db_server, $db_user, $db_pass, $db_name);
   
    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    // Atur karakter encoding ke UTF-8
    $conn->set_charset("utf8");
?>