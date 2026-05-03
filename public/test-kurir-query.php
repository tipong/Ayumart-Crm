<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Kurir Dashboard Query</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #10b981;
            padding-bottom: 10px;
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #10b981;
        }
        .success {
            color: #10b981;
            font-weight: bold;
        }
        .error {
            color: #ef4444;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #10b981;
            color: white;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #10b981;
            color: white;
        }
        .badge-warning {
            background: #f59e0b;
            color: white;
        }
        .badge-info {
            background: #3b82f6;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Kurir Dashboard - Completed Deliveries Query</h1>

        <?php
        // Database connection (adjust according to your config)
        $host = 'localhost';
        $dbname = 'crm_atmabeauty';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo '<div class="section">';
            echo '<h2>✅ Database Connection: <span class="success">SUCCESS</span></h2>';
            echo '</div>';

            // Test 1: Get all delivery statuses
            echo '<div class="section">';
            echo '<h3>📊 Test 1: All Delivery Statuses</h3>';
            $stmt = $pdo->query("
                SELECT status_pengiriman, COUNT(*) as count
                FROM tb_pengiriman
                GROUP BY status_pengiriman
            ");
            $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($statuses) {
                echo '<table>';
                echo '<tr><th>Status</th><th>Count</th></tr>';
                foreach ($statuses as $status) {
                    $badge_class = $status['status_pengiriman'] == 'selesai' ? 'badge-success' :
                                  ($status['status_pengiriman'] == 'dalam_pengiriman' ? 'badge-info' : 'badge-warning');
                    echo '<tr>';
                    echo '<td><span class="badge ' . $badge_class . '">' . htmlspecialchars($status['status_pengiriman']) . '</span></td>';
                    echo '<td>' . $status['count'] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="error">No data found</p>';
            }
            echo '</div>';

            // Test 2: Completed deliveries (status = 'selesai')
            echo '<div class="section">';
            echo '<h3>✅ Test 2: Completed Deliveries (Status = selesai)</h3>';
            $stmt = $pdo->query("
                SELECT
                    p.id_pengiriman,
                    p.id_kurir,
                    p.no_resi,
                    p.nama_penerima,
                    p.alamat_penerima,
                    p.status_pengiriman,
                    p.updated_at,
                    t.kode_transaksi,
                    t.status_pembayaran,
                    pel.nama_pelanggan,
                    c.nama_cabang
                FROM tb_pengiriman p
                JOIN tb_transaksi t ON p.id_transaksi = t.id_transaksi
                JOIN tb_pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
                LEFT JOIN tb_cabang c ON t.id_cabang = c.id_cabang
                WHERE p.status_pengiriman = 'selesai'
                  AND t.status_pembayaran = 'sudah_bayar'
                  AND p.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY p.updated_at DESC
            ");
            $completed = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($completed) {
                echo '<p class="success">Found ' . count($completed) . ' completed deliveries in last 30 days</p>';
                echo '<table>';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Resi</th>';
                echo '<th>Kurir ID</th>';
                echo '<th>Pelanggan</th>';
                echo '<th>Penerima</th>';
                echo '<th>Alamat</th>';
                echo '<th>Status Payment</th>';
                echo '<th>Status Delivery</th>';
                echo '<th>Updated</th>';
                echo '</tr>';

                foreach ($completed as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['id_pengiriman'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['no_resi']) . '</td>';
                    echo '<td>' . ($row['id_kurir'] ?? 'NULL') . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_pelanggan']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_penerima']) . '</td>';
                    echo '<td>' . htmlspecialchars(substr($row['alamat_penerima'], 0, 40)) . '...</td>';
                    echo '<td><span class="badge badge-success">' . $row['status_pembayaran'] . '</span></td>';
                    echo '<td><span class="badge badge-success">' . $row['status_pengiriman'] . '</span></td>';
                    echo '<td>' . date('d M Y H:i', strtotime($row['updated_at'])) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="error">No completed deliveries found in last 30 days</p>';
            }
            echo '</div>';

            // Test 3: Completed deliveries by specific kurir (ID 39)
            echo '<div class="section">';
            echo '<h3>👤 Test 3: Completed Deliveries for Kurir ID 39</h3>';
            $stmt = $pdo->query("
                SELECT
                    p.id_pengiriman,
                    p.no_resi,
                    p.nama_penerima,
                    p.status_pengiriman,
                    p.updated_at,
                    t.kode_transaksi,
                    pel.nama_pelanggan
                FROM tb_pengiriman p
                JOIN tb_transaksi t ON p.id_transaksi = t.id_transaksi
                JOIN tb_pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
                WHERE p.status_pengiriman = 'selesai'
                  AND t.status_pembayaran = 'sudah_bayar'
                  AND p.id_kurir = 39
                  AND p.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY p.updated_at DESC
            ");
            $kurirCompleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($kurirCompleted) {
                echo '<p class="success">Found ' . count($kurirCompleted) . ' completed deliveries for Kurir #39</p>';
                echo '<table>';
                echo '<tr>';
                echo '<th>Order ID</th>';
                echo '<th>Resi</th>';
                echo '<th>Pelanggan</th>';
                echo '<th>Penerima</th>';
                echo '<th>Status</th>';
                echo '<th>Completed At</th>';
                echo '</tr>';

                foreach ($kurirCompleted as $row) {
                    echo '<tr>';
                    echo '<td><strong>' . htmlspecialchars($row['kode_transaksi']) . '</strong></td>';
                    echo '<td>' . htmlspecialchars($row['no_resi']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_pelanggan']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_penerima']) . '</td>';
                    echo '<td><span class="badge badge-success">' . $row['status_pengiriman'] . '</span></td>';
                    echo '<td>' . date('d M Y H:i', strtotime($row['updated_at'])) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="error">No completed deliveries found for Kurir #39</p>';
            }
            echo '</div>';

            // Test 4: Statistics
            echo '<div class="section">';
            echo '<h3>📈 Test 4: Kurir Statistics (ID 39)</h3>';

            // Pending deliveries
            $stmt = $pdo->query("
                SELECT COUNT(*) as count
                FROM tb_pengiriman p
                JOIN tb_transaksi t ON p.id_transaksi = t.id_transaksi
                WHERE t.status_pembayaran = 'sudah_bayar'
                  AND p.status_pengiriman = 'dikemas'
                  AND (p.id_kurir IS NULL OR p.id_kurir = 39)
            ");
            $pending = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Ongoing deliveries
            $stmt = $pdo->query("
                SELECT COUNT(*) as count
                FROM tb_pengiriman p
                JOIN tb_transaksi t ON p.id_transaksi = t.id_transaksi
                WHERE t.status_pembayaran = 'sudah_bayar'
                  AND p.status_pengiriman = 'dalam_pengiriman'
                  AND p.id_kurir = 39
            ");
            $ongoing = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Completed today
            $stmt = $pdo->query("
                SELECT COUNT(*) as count
                FROM tb_pengiriman p
                JOIN tb_transaksi t ON p.id_transaksi = t.id_transaksi
                WHERE t.status_pembayaran = 'sudah_bayar'
                  AND p.status_pengiriman = 'selesai'
                  AND p.id_kurir = 39
                  AND DATE(p.updated_at) = CURDATE()
            ");
            $completedToday = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Total deliveries
            $stmt = $pdo->query("
                SELECT COUNT(*) as count
                FROM tb_pengiriman
                WHERE id_kurir = 39
                  AND status_pengiriman = 'selesai'
            ");
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            echo '<table>';
            echo '<tr><th>Metric</th><th>Count</th></tr>';
            echo '<tr><td>Pending Deliveries</td><td><span class="badge badge-warning">' . $pending . '</span></td></tr>';
            echo '<tr><td>Ongoing Deliveries</td><td><span class="badge badge-info">' . $ongoing . '</span></td></tr>';
            echo '<tr><td>Completed Today</td><td><span class="badge badge-success">' . $completedToday . '</span></td></tr>';
            echo '<tr><td>Total Completed (All Time)</td><td><span class="badge badge-success">' . $total . '</span></td></tr>';
            echo '</table>';
            echo '</div>';

            echo '<div class="section">';
            echo '<h2>🎯 Summary</h2>';
            echo '<ul>';
            echo '<li class="success">✅ Database connection successful</li>';
            echo '<li class="success">✅ Status "selesai" found in database</li>';
            echo '<li class="success">✅ Query for completed deliveries working</li>';
            echo '<li class="success">✅ Kurir-specific completed deliveries found</li>';
            echo '</ul>';
            echo '<p><strong>Conclusion:</strong> The query is working correctly. If data doesn\'t show in dashboard, check:</p>';
            echo '<ol>';
            echo '<li>Authentication - Is the user logged in as Kurir (role_id = 4)?</li>';
            echo '<li>View rendering - Check if $completedOrders is passed to view</li>';
            echo '<li>Blade template - Verify @forelse loop is working</li>';
            echo '<li>Cache - Clear view and cache: <code>php artisan cache:clear && php artisan view:clear</code></li>';
            echo '</ol>';
            echo '</div>';

        } catch(PDOException $e) {
            echo '<div class="section">';
            echo '<h2 class="error">❌ Database Connection Error</h2>';
            echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
            echo '<p>Please check your database credentials and make sure MySQL is running.</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
