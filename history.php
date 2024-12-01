<?php
session_start();
require_once('../config/db_connection.php');

// ID pelanggan (ambil dari session login)
$pelanggan_id = 1; // Contoh ID pelanggan

// Ambil riwayat pembelian dari database
$query = "
    SELECT rp.*, f.nama AS furnitur_nama, f.warna
    FROM RiwayatPembelian rp
    JOIN Furnitur f ON rp.furnitur_id = f.id
    WHERE rp.pelanggan_id = ?
    ORDER BY rp.tanggal_pembelian DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pelanggan_id);
$stmt->execute();
$history_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- History Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">Riwayat Pembelian</h2>
            <?php if (empty($history_results)): ?>
                <p class="text-gray-500">Belum ada riwayat pembelian.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Tanggal</th>
                                <th class="py-2 px-4 border-b">Nama Furnitur</th>
                                <th class="py-2 px-4 border-b">Warna</th>
                                <th class="py-2 px-4 border-b">Jumlah</th>
                                <th class="py-2 px-4 border-b">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history_results as $history): ?>
                                <tr>
                                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($history['tanggal_pembelian']); ?></td>
                                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($history['furnitur_nama']); ?></td>
                                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($history['warna']); ?></td>
                                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($history['jumlah']); ?></td>
                                    <td class="py-2 px-4 border-b">Rp <?php echo number_format($history['total_harga'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
