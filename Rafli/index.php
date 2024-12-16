<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Furnitur Kapi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #000000 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            display: flex;
            gap: 20px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            align-items: center;
            max-width: 900px;
            width: 100%;
        }

        .product-item {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-item img {
            width: 500px;
            height: 500px;
            object-fit: cover;
            border-radius: 8px;
        }

        .login-box {
            flex: 2;
            max-width: 450px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #3498db;
            outline: none;
        }

        .password-input {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 14px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background: #2980b9;
        }

        .form-links {
            text-align: center;
            margin-top: 15px;
        }

        .form-links a {
            color: #3498db;
            text-decoration: none;
        }

        .form-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 20px;
            }

            .product-item {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="product-item">
            <img src="./img/logoNew.jpeg" alt="Logo Kapi">
        </div>

        <div class="login-box">
            <!-- Form login -->
            <form action="./patient/process_login.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="user_type">Login Sebagai:</label>
                    <select name="user_type" id="user_type" required onchange="updateFormAction()">
                        <option value="pelanggan">Pelanggan</option>
                        <option value="pemilik">Pemilik</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Login</button>
                </div>

                <!-- Link untuk pelanggan -->
                <div class="form-links" id="register-link">
                    <a href="./patient/register.php">Daftar Akun Baru</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateFormAction() {
            const form = document.querySelector('.login-form');
            const userType = document.getElementById('user_type').value;
            const registerLink = document.getElementById('register-link');

            if (userType === 'pemilik') {
                form.action = './admin/process_login.php';
                registerLink.style.display = 'none'; // Hide register link for admin
            } else {
                form.action = './patient/process_login.php';
                registerLink.style.display = 'block'; // Show register link for pelanggan
            }
        }

        // Set initial action when page loads
        document.addEventListener('DOMContentLoaded', updateFormAction);
    </script>
</body>
</html>
