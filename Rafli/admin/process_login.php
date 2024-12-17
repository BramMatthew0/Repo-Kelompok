<?php
session_start();
require_once('../config/db_connection.php');

// Function to sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Verifikasi tipe pengguna
    if ($user_type !== 'pemilik') {
        $_SESSION['error'] = "Tipe pengguna tidak valid";
        header("Location: ../index.php");
        exit();
    }

    try {
        // Query ke database untuk admin
        $sql = "SELECT Id_Pemilik, Username_Pemilik, Password_Pemilik, Nama_Pemilik 
                FROM pemilik 
                WHERE Username_Pemilik = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verifikasi password
            if ($password === $row['Password_Pemilik']) { // Ganti dengan password_verify jika menggunakan hash
                // Set session untuk admin
                $_SESSION['user_id'] = $row['Id_Pemilik'];
                $_SESSION['username'] = $row['Username_Pemilik'];
                $_SESSION['nama'] = $row['Nama_Pemilik'];
                $_SESSION['user_type'] = 'admin';

                // Redirect ke dashboard admin
                header("Location: ../admin/admin_dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Username atau password salah";
                header("Location: ../index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Username atau password salah";
            header("Location: ../index.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan dalam proses login: " . $e->getMessage();
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
