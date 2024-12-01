<?php
// Koneksi database menggunakan mysqli
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tokokapi";  // pastikan database yang digunakan adalah tokokapi

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

<?php
session_start();
require_once('../config/db_connection.php');

// Get search parameters
$search_furniture = isset($_GET['search_furniture']) ? $_GET['search_furniture'] : '';
$search_color = isset($_GET['color']) ? $_GET['color'] : '';

// Build the query
$query = "SELECT * FROM Furnitur WHERE 1=1";

// Tambahkan filter jika ada input pencarian
if (!empty($search_furniture)) {
    $query .= " AND nama LIKE ?";
}
if (!empty($search_color)) {
    $query .= " AND warna = ?";
}

// Prepare statement
$stmt = $conn->prepare($query);

// Bind parameters sesuai filter
if (!empty($search_furniture) && !empty($search_color)) {
    $search_param = "%$search_furniture%";
    $stmt->bind_param("ss", $search_param, $search_color);
} elseif (!empty($search_furniture)) {
    $search_param = "%$search_furniture%";
    $stmt->bind_param("s", $search_param);
} elseif (!empty($search_color)) {
    $stmt->bind_param("s", $search_color);
}

$stmt->execute();
$furniture_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Furnitur</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Search Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Cari Furnitur</h2>
            <form method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Nama Furnitur</label>
                    <input 
                        type="text" 
                        name="search_furniture" 
                        value="<?php echo htmlspecialchars($search_furniture); ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                        placeholder="Cari nama furnitur...">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Warna</label>
                    <select 
                        name="color" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Warna</option>
                        <option value="Merah" <?php echo $search_color === 'Merah' ? 'selected' : ''; ?>>Merah</option>
                        <option value="Biru" <?php echo $search_color === 'Biru' ? 'selected' : ''; ?>>Biru</option>
                        <option value="Hijau" <?php echo $search_color === 'Hijau' ? 'selected' : ''; ?>>Hijau</option>
                        <option value="Hitam" <?php echo $search_color === 'Hitam' ? 'selected' : ''; ?>>Hitam</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button 
                        type="submit" 
                        class="w-full md:w-auto px-6 py-2 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cari
                    </button>
                </div>
            </form>
        </div>
       

            <!-- Furniture Results -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Hasil Pencarian</h3>
                </div>
                <?php if (empty($furniture_results)): ?>
                    <div class="p-6 text-center text-gray-500">
                        Tidak ada furnitur yang ditemukan.
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-6">
                        <?php foreach ($furniture_results as $furniture): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-4">
                                    <h4 class="text-lg font-medium text-gray-900">
                                        <?php echo htmlspecialchars($furniture['nama']); ?>
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Warna: <?php echo htmlspecialchars($furniture['warna']); ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Harga: Rp <?php echo number_format($furniture['harga'], 0, ',', '.'); ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Material: <?php echo htmlspecialchars($furniture['material']); ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Stok: <?php echo htmlspecialchars($furniture['stok']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
    </div>
</body>
</html>