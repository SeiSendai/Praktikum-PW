<?php
session_start();

// Redirect ke login jika belum login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$login_time = $_SESSION['login_time'] ?? 'Unknown';

// Koneksi database
$host = 'localhost';
$dbname = 'review_laptop_lokal';
$db_username = 'root';
$db_password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle CRUD Operations
$message = '';
$message_type = '';

// DELETE Operation
if (isset($_GET['delete']) && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM laptops WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = 'Laptop berhasil dihapus!';
        $message_type = 'success';
    } catch(PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Handle filter dari query string
$price_filter = $_GET['price'] ?? 'all';
$rating_filter = $_GET['rating'] ?? 'all';
$search_query = $_GET['search'] ?? '';
$brand_filter = $_GET['brand'] ?? 'all';

// Fetch data dari database
try {
    // Fetch brands
    $brands_stmt = $pdo->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name");
    $brands = $brands_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build query untuk laptop dengan filter
    $query = "SELECT l.*, b.name as brand_name 
              FROM laptops l 
              LEFT JOIN brands b ON l.brand_id = b.id 
              WHERE l.is_active = 1";
    
    $params = [];
    
    if ($brand_filter !== 'all') {
        $query .= " AND b.name = ?";
        $params[] = $brand_filter;
    }
    
    if ($price_filter !== 'all') {
        if ($price_filter === '0-4000000') {
            $query .= " AND l.price < 4000000";
        } elseif ($price_filter === '4000000-7000000') {
            $query .= " AND l.price BETWEEN 4000000 AND 7000000";
        } elseif ($price_filter === '7000000-10000000') {
            $query .= " AND l.price BETWEEN 7000000 AND 10000000";
        } elseif ($price_filter === '10000000') {
            $query .= " AND l.price > 10000000";
        }
    }
    
    if ($rating_filter !== 'all') {
        $query .= " AND l.rating >= ?";
        $params[] = floatval($rating_filter);
    }
    
    if (!empty($search_query)) {
        $query .= " AND (l.name LIKE ? OR l.description LIKE ? OR l.processor LIKE ?)";
        $search_param = "%$search_query%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    $query .= " ORDER BY l.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $laptops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung statistik
    $stats_query = "SELECT 
                    COUNT(*) as total_laptops,
                    ROUND(AVG(rating), 2) as avg_rating,
                    SUM(stock) as total_stock,
                    ROUND(AVG(price), 2) as avg_price
                    FROM laptops WHERE is_active = 1";
    $stats = $pdo->query($stats_query)->fetch(PDO::FETCH_ASSOC);

    // Statistik per brand
    $brand_stats_query = "SELECT 
                          b.name as brand_name,
                          COUNT(l.id) as count,
                          ROUND(AVG(l.rating), 2) as avg_rating,
                          SUM(l.stock) as total_stock
                          FROM brands b
                          LEFT JOIN laptops l ON b.id = l.brand_id AND l.is_active = 1
                          WHERE b.is_active = 1
                          GROUP BY b.id, b.name";
    $brand_stats_result = $pdo->query($brand_stats_query)->fetchAll(PDO::FETCH_ASSOC);
    
    $brand_stats = [];
    foreach ($brand_stats_result as $stat) {
        $brand_stats[$stat['brand_name']] = $stat;
    }

} catch(PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

$total_laptops = $stats['total_laptops'] ?? 0;
$filtered_count = count($laptops);
$avg_rating = $stats['avg_rating'] ?? 0;
$total_stock = $stats['total_stock'] ?? 0;
$avg_price = $stats['avg_price'] ?? 0;

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

function getStockStatus($stock) {
    if ($stock <= 5) return ['text' => 'Low Stock', 'color' => '#ef4444'];
    if ($stock <= 15) return ['text' => 'Medium', 'color' => '#f59e0b'];
    return ['text' => 'In Stock', 'color' => '#10b981'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Review Laptop Lokal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-header {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 16px var(--shadow-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            border: 1px solid var(--border-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .user-details h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .user-details p {
            margin: 5px 0 0 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .dashboard-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn-logout {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            border: none;
            cursor: pointer;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
        }

        .btn-home {
            background: var(--card-bg);
            color: var(--text-color);
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            border: 2px solid var(--border-color);
        }

        .btn-home:hover {
            background: var(--hover-bg);
            transform: translateY(-2px);
            border-color: #3b82f6;
        }

        /* Message Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        .alert.success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            color: #10b981;
        }

        .alert.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 16px var(--shadow-light);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px var(--shadow-medium);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--secondary-gradient);
        }

        .stat-card.accent::before {
            background: var(--accent-gradient);
        }

        .stat-card.warning::before {
            background: var(--warning-gradient);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: var(--secondary-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .stat-icon.accent {
            background: var(--accent-gradient);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .stat-icon.warning {
            background: var(--warning-gradient);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-color);
            margin: 10px 0 5px 0;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Brand Stats */
        .brand-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .brand-stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 16px var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .brand-stat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }

        .brand-logo {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
        }

        .brand-logo.axioo {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .brand-logo.advan {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .brand-logo.zyrex {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .brand-stat-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .brand-stat-item {
            text-align: center;
            padding: 10px;
            background: var(--hover-bg);
            border-radius: 8px;
        }

        .brand-stat-item .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .brand-stat-item .label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Filter Section */
        .filter-section {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 16px var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-header h3 {
            margin: 0;
            color: var(--text-color);
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .filter-group input,
        .filter-group select {
            padding: 12px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-color);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .btn-filter {
            padding: 12px 24px;
            background: var(--secondary-gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-reset {
            padding: 12px 24px;
            background: var(--card-bg);
            color: var(--text-color);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background: var(--hover-bg);
            border-color: #ef4444;
            color: #ef4444;
        }

        /* Active Filters */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .filter-badge {
            background: #3b82f6;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* CRUD Buttons */
        .btn-add {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            border: none;
            cursor: pointer;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
        }

        .btn-edit:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        /* Laptop Table */
        .laptop-table-container {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 16px var(--shadow-light);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h3 {
            margin: 0;
            color: var(--text-color);
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .result-count {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .laptop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .laptop-table thead {
            background: var(--hover-bg);
        }

        .laptop-table th {
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--text-color);
            font-size: 0.9rem;
            border-bottom: 2px solid var(--border-color);
        }

        .laptop-table td {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .laptop-table tbody tr {
            transition: all 0.2s ease;
        }

        .laptop-table tbody tr:hover {
            background: var(--hover-bg);
        }

        .laptop-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .laptop-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }

        .laptop-details h4 {
            margin: 0 0 4px 0;
            color: var(--text-color);
            font-size: 1rem;
            font-weight: 600;
        }

        .laptop-details p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .brand-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .brand-badge.axioo {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .brand-badge.advan {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .brand-badge.zyrex {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .rating-display {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rating-stars-small {
            display: flex;
            gap: 2px;
        }

        .rating-stars-small i {
            font-size: 0.85rem;
            color: #fbbf24;
        }

        .rating-value {
            font-weight: 600;
            color: var(--text-color);
        }

        .price-display {
            font-weight: 700;
            color: #10b981;
            font-size: 1rem;
        }

        .stock-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .specs-list {
            font-size: 0.85rem;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        @media (max-width: 1024px) {
            .laptop-table {
                display: block;
                overflow-x: auto;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }

            .user-info {
                flex-direction: column;
            }

            .dashboard-actions {
                width: 100%;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .brand-stats-grid {
                grid-template-columns: 1fr;
            }

            .filter-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .filter-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn-filter,
            .btn-reset {
                width: 100%;
                justify-content: center;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div id="reading-progress" class="reading-progress"></div>
    
    <header>
        <nav>
            <h1>
                <i class="fas fa-laptop"></i>
                Review Laptop Lokal
            </h1>
            <div class="nav-controls">
                <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="index.php">Beranda</a></li>
                </ul>
                <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </nav>
    </header>

    <main style="margin-top: 100px;">
        <div class="dashboard-container">
            <!-- Message Alert -->
            <?php if (!empty($message)): ?>
            <div class="alert <?php echo $message_type; ?>">
                <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
            <?php endif; ?>

            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($username, 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h2>Selamat Datang, <?php echo htmlspecialchars($username); ?>! 👋</h2>
                        <p><i class="fas fa-clock"></i> Login: <?php echo htmlspecialchars($login_time); ?></p>
                    </div>
                </div>
                <div class="dashboard-actions">
                    <a href="index.php" class="btn-home">
                        <i class="fas fa-home"></i>
                        Beranda
                    </a>
                    <a href="logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>

            <!-- Main Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo $total_laptops; ?></div>
                    <div class="stat-label">Total Laptop</div>
                </div>

                <div class="stat-card accent">
                    <div class="stat-header">
                        <div class="stat-icon accent">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo number_format($avg_rating, 1); ?></div>
                    <div class="stat-label">Rating Rata-rata</div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-header">
                        <div class="stat-icon warning">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo $total_stock; ?></div>
                    <div class="stat-label">Total Stok</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo formatRupiah($avg_price); ?></div>
                    <div class="stat-label">Harga Rata-rata</div>
                </div>
            </div>

            <!-- Brand Statistics -->
            <div class="brand-stats-grid">
                <?php foreach ($brand_stats as $brand => $stats): ?>
                <div class="brand-stat-card">
                    <div class="brand-stat-header">
                        <div class="brand-logo <?php echo strtolower($brand); ?>">
                            <?php echo substr($brand, 0, 1); ?>
                        </div>
                        <div>
                            <h4 style="margin: 0; color: var(--text-color);"><?php echo $brand; ?></h4>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Brand Statistics</p>
                        </div>
                    </div>
                    <div class="brand-stat-info">
                        <div class="brand-stat-item">
                            <div class="value"><?php echo $stats['count']; ?></div>
                            <div class="label">Models</div>
                        </div>
                        <div class="brand-stat-item">
                            <div class="value"><?php echo number_format($stats['avg_rating'], 1); ?></div>
                            <div class="label">Avg Rating</div>
                        </div>
                        <div class="brand-stat-item">
                            <div class="value"><?php echo $stats['total_stock']; ?></div>
                            <div class="label">Stock</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-header">
                    <h3>
                        <i class="fas fa-filter"></i>
                        Filter & Pencarian
                    </h3>
                    <span class="result-count">
                        Menampilkan <strong><?php echo $filtered_count; ?></strong> dari <strong><?php echo $total_laptops; ?></strong> laptop
                    </span>
                </div>

                <form method="GET" action="dashboard.php" class="filter-form">
                    <div class="filter-group">
                        <label><i class="fas fa-search"></i> Cari Laptop</label>
                        <input type="text" name="search" placeholder="Nama, processor..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-building"></i> Brand</label>
                        <select name="brand">
                            <option value="all" <?php echo $brand_filter === 'all' ? 'selected' : ''; ?>>Semua Brand</option>
                            <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo htmlspecialchars($brand['name']); ?>" <?php echo $brand_filter === $brand['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($brand['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-tag"></i> Harga</label>
                        <select name="price">
                            <option value="all" <?php echo $price_filter === 'all' ? 'selected' : ''; ?>>Semua Harga</option>
                            <option value="0-4000000" <?php echo $price_filter === '0-4000000' ? 'selected' : ''; ?>>< 4 Juta</option>
                            <option value="4000000-7000000" <?php echo $price_filter === '4000000-7000000' ? 'selected' : ''; ?>>4 - 7 Juta</option>
                            <option value="7000000-10000000" <?php echo $price_filter === '7000000-10000000' ? 'selected' : ''; ?>>7 - 10 Juta</option>
                            <option value="10000000" <?php echo $price_filter === '10000000' ? 'selected' : ''; ?>>> 10 Juta</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-star"></i> Rating</label>
                        <select name="rating">
                            <option value="all" <?php echo $rating_filter === 'all' ? 'selected' : ''; ?>>Semua Rating</option>
                            <option value="4.0" <?php echo $rating_filter === '4.0' ? 'selected' : ''; ?>>4+ Bintang</option>
                            <option value="3.5" <?php echo $rating_filter === '3.5' ? 'selected' : ''; ?>>3.5+ Bintang</option>
                            <option value="3.0" <?php echo $rating_filter === '3.0' ? 'selected' : ''; ?>>3+ Bintang</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i>
                            Terapkan Filter
                        </button>
                        <a href="dashboard.php" class="btn-reset">
                            <i class="fas fa-redo"></i>
                            Reset
                        </a>
                    </div>
                </form>

                <?php if ($price_filter !== 'all' || $rating_filter !== 'all' || !empty($search_query) || $brand_filter !== 'all'): ?>
                <div class="active-filters">
                    <span style="color: var(--text-muted); font-weight: 600;">Filter Aktif:</span>
                    <?php if ($brand_filter !== 'all'): ?>
                        <span class="filter-badge">
                            <i class="fas fa-building"></i>
                            Brand: <?php echo $brand_filter; ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($price_filter !== 'all'): ?>
                        <span class="filter-badge">
                            <i class="fas fa-tag"></i>
                            Harga: <?php 
                                if ($price_filter === '0-4000000') echo '< 4 Juta';
                                elseif ($price_filter === '4000000-7000000') echo '4-7 Juta';
                                elseif ($price_filter === '7000000-10000000') echo '7-10 Juta';
                                elseif ($price_filter === '10000000') echo '> 10 Juta';
                            ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($rating_filter !== 'all'): ?>
                        <span class="filter-badge">
                            <i class="fas fa-star"></i>
                            Rating: <?php echo $rating_filter; ?>+ Bintang
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($search_query)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i>
                            "<?php echo htmlspecialchars($search_query); ?>"
                        </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Laptop Table -->
            <div class="laptop-table-container">
                <div class="table-header">
                    <h3>
                        <i class="fas fa-table"></i>
                        Data Laptop
                    </h3>
                    <a href="laptop_add.php" class="btn-add">
                        <i class="fas fa-plus"></i>
                        Tambah Laptop
                    </a>
                </div>

                <?php if ($filtered_count > 0): ?>
                <table class="laptop-table">
                    <thead>
                        <tr>
                            <th>Laptop</th>
                            <th>Brand</th>
                            <th>Spesifikasi</th>
                            <th>Rating</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laptops as $laptop): ?>
                        <?php $stock_status = getStockStatus($laptop['stock']); ?>
                        <tr>
                            <td>
                                <div class="laptop-info">
                                    <img src="<?php echo htmlspecialchars($laptop['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($laptop['name']); ?>" 
                                         class="laptop-img"
                                         onerror="this.src='IMG/placeholder.jpg'">
                                    <div class="laptop-details">
                                        <h4><?php echo htmlspecialchars($laptop['name']); ?></h4>
                                        <p><?php echo htmlspecialchars(substr($laptop['description'], 0, 50)) . '...'; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="brand-badge <?php echo strtolower($laptop['brand_name']); ?>">
                                    <?php echo htmlspecialchars($laptop['brand_name']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="specs-list">
                                    <div><i class="fas fa-microchip"></i> <?php echo htmlspecialchars($laptop['processor']); ?></div>
                                    <div><i class="fas fa-memory"></i> <?php echo htmlspecialchars($laptop['ram']); ?></div>
                                    <div><i class="fas fa-hdd"></i> <?php echo htmlspecialchars($laptop['storage']); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="rating-display">
                                    <div class="rating-stars-small">
                                        <?php 
                                        $full_stars = floor($laptop['rating']);
                                        $half_star = ($laptop['rating'] - $full_stars) >= 0.5;
                                        
                                        for ($i = 0; $i < $full_stars; $i++) {
                                            echo '<i class="fas fa-star"></i>';
                                        }
                                        if ($half_star) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        }
                                        for ($i = 0; $i < (5 - ceil($laptop['rating'])); $i++) {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <span class="rating-value"><?php echo number_format($laptop['rating'], 1); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="price-display"><?php echo formatRupiah($laptop['price']); ?></div>
                            </td>
                            <td>
                                <span class="stock-badge" style="background-color: <?php echo $stock_status['color']; ?>20; color: <?php echo $stock_status['color']; ?>;">
                                    <?php echo $laptop['stock']; ?> unit - <?php echo $stock_status['text']; ?>
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($laptop['category']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="laptop_edit.php?id=<?php echo $laptop['id']; ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    <a href="dashboard.php?delete=1&id=<?php echo $laptop['id']; ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus laptop ini?');">
                                        <i class="fas fa-trash"></i>
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>Tidak Ada Data Ditemukan</h3>
                    <p>Tidak ada laptop yang sesuai dengan kriteria filter Anda.</p>
                    <a href="dashboard.php" class="btn-filter" style="display: inline-flex;">
                        <i class="fas fa-redo"></i>
                        Reset Filter
                    </a>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <button id="scroll-top" class="scroll-top" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <footer>
        <div class="footer-content">
            <p><small>
                <i class="fas fa-copyright"></i>
                2025 Review Laptop Lokal - Dashboard Pengelola Handal
            </small></p>
            <div class="footer-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Auto hide alert after 5 seconds
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    </script>
</body>
</html>