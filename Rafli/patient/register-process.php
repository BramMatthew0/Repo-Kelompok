<?php
session_start();
require_once('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input
    $nama = sanitize($_POST['nama']);
    $no_hp = sanitize($_POST['no_hp']);
    $email = sanitize($_POST['email']);
    $alamat = sanitize($_POST['alamat']);
    $kecamatan = sanitize($_POST['kecamatan']);
    $kelurahan = sanitize($_POST['kelurahan']);
    $username = sanitize($_POST['username']);
    $password = hash('sha256', $_POST['password']);
    
    // Database connection
    $conn = connectDB();
    
    // Check if username already exists
    $checkUsername = "SELECT Username FROM Pasien WHERE Username = '$username'";
    $result = sqlsrv_query($conn, $checkUsername);
    
    if(sqlsrv_has_rows($result)) {
        $_SESSION['error'] = "Username sudah digunakan. Silakan pilih username lain.";
        header("Location: register.php");
        exit();
    }
    
    // Insert new patient
    $sql = "INSERT INTO Pasien (Nama, No_HP, Email, Alamat, Kecamatan, Kelurahan, Username, Password) 
            VALUES ('$nama', '$no_hp', '$email', '$alamat', '$kecamatan', '$kelurahan', '$username', '$password')";
    
    $result = sqlsrv_query($conn, $sql);
    
    if($result === false) {
        $_SESSION['error'] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        header("Location: register.php");
        exit();
    }
    
    $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
    header("Location: login.php");
    exit();
} else {
    header("Location: register.php");
    exit();
}
?>
