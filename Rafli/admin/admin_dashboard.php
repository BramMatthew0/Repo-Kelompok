<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    <!-- Link ke FontAwesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        /* CSS untuk tampilan */
        html {
            overflow-y: scroll;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-y: auto;
        }
        nav {
            background-color: #333;
            padding: 15px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
        }
        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            padding: 0;
            margin: 0;
        }
        nav ul li {
            margin: 0 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            padding: 10px 20px;
        }
        nav a:hover {
            color: #007bff;
            background-color: #444;
            border-radius: 5px;
        }

        .cart-icon {
            position: absolute;
            right: 20px; /* Menempatkan ikon di kanan */
            font-size: 24px;
            color: white;
        }

        .cart-icon:hover {
            color: #3498db;
        }

        /* New Release Section */
        .new-release {
            padding: 20px;
            background-color: white;
            margin: 60px;
        }

        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Untuk meratakan produk di tengah */
        }

        .product-item {
            display: flex;  /* Menampilkan gambar dan teks dalam satu baris */
            align-items: center;  /* Menjaga item sejajar secara vertikal */
            margin: 10px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 900px;  /* Menetapkan lebar produk */
        }

        .product-item img {
            width: 500px;  /* Menetapkan lebar gambar */
            height: 500px; /* Menetapkan tinggi gambar */
            object-fit: cover; /* Agar gambar tetap terpotong jika ukurannya tidak sesuai */
            border-radius: 8px;
            margin-right: 20px; /* Memberikan jarak antara gambar dan teks */
        }

        .product-item h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .product-item p {
            font-size: 14px;
            color: #555;
        }

        .product-item .price {
            font-weight: bold;
            color: #e74c3c;
        }

        .product-item .button {
            display: inline-block;
            padding: 10px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .product-item .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Home</a></li>
            <li><a href="review.php">Review</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="product.php">Product</a></li>
            <li><a href="order.php">Order</a></li>
            <li><a href="financial.php">Financial Report</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

<!-- New Release Section -->
<section class="new-release">
        <h2>New Release</h2>
        <div class="product-list">
            <div class="product-item">
                <!-- Gambar di sebelah kiri menggunakan path relatif -->
                <img src="../img/kursi davon.jpg" alt="Kursi Davon">
                <!-- Teks di sebelah kanan gambar -->
                <div class="product-details">
                    <h3>Kursi Davon</h3>
                    <p class="price">Rp 2,500,000</p>
                    <p>Sofa Davon 1 Dudukan Idemu hadir untuk membuat berbagai ruang yang ada di rumah anda menjadi lebih indah. Sofa Davon 1 dudukan Idemu memiliki desain yang minimalis sehingga membuat ruangan di rumah anda menjadi elegan dan modern, Sofa Davon 1 dudukan Idemu memiliki warna soft dan kualitas baik yang menciptakan kenyamanan saat anda berkumpul dengan keluarga, kerabat dan para sahabat.</p>
                    <!-- <a href="product_detail.php?id=1" class="button">Learn More</a> -->
                </div>
            </div>
        </div>
    </section>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            if (scrollbarWidth > 0) {
                document.body.style.paddingRight = `${scrollbarWidth}px`;
            }
        });
</script>
</body>
</html>
