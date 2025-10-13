<?php
session_start();

// Redirect ke login jika belum login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

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

$message = '';
$message_type = '';

// Get laptop ID
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$laptop_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE laptops SET 
                              brand_id = ?, name = ?, model = ?, price = ?, processor = ?, 
                              ram = ?, storage = ?, display = ?, gpu = ?, weight = ?, 
                              battery = ?, category = ?, image = ?, description = ?, 
                              stock = ?, rating = ?, release_date = ?
                              WHERE id = ?");
        
        $stmt->execute([
            $_POST['brand_id'],
            $_POST['name'],
            $_POST['model'],
            $_POST['price'],
            $_POST['processor'],
            $_POST['ram'],
            $_POST['storage'],
            $_POST['display'],
            $_POST['gpu'],
            $_POST['weight'],
            $_POST['battery'],
            $_POST['category'],
            $_POST['image'],
            $_POST['description'],
            $_POST['stock'],
            $_POST['rating'],
            $_POST['release_date'],
            $laptop_id
        ]);
        
        header('Location: dashboard.php');
        exit();
    } catch(PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Fetch laptop data
try {
    $stmt = $pdo->prepare("SELECT * FROM laptops WHERE id = ?");
    $stmt->execute([$laptop_id]);
    $laptop = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$laptop) {
        header('Location: dashboard.php');
        exit();
    }
} catch(PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Fetch brands
$brands = $pdo->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laptop - Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 900px;
            margin: 120px auto 40px;
            padding: 20px;
        }

        .form-card {
            background: var(--card-bg);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 16px var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .form-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }

        .form-header h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-header p {
            margin: 10px 0 0 0;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .laptop-preview {
            background: var(--hover-bg);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .laptop-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }

        .laptop-preview-info h3 {
            margin: 0;
            color: var(--text-color);
            font-size: 1.2rem;
        }

        .laptop-preview-info p {
            margin: 5px 0 0 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-grid.single {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-color);
            font-size: 0.95rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group small {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .btn-submit {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-cancel {
            background: var(--card-bg);
            color: var(--text-color);
            padding: 14px 32px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: var(--hover-bg);
            border-color: #ef4444;
            color: #ef4444;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }

            .form-card {
                padding: 25px 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-submit,
            .btn-cancel {
                width: 100%;
                justify-content: center;
            }

            .laptop-preview {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <h1>
                <i class="fas fa-laptop"></i>
                Review Laptop Lokal
            </h1>
            <div class="nav-controls">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="index.php">Beranda</a></li>
                </ul>
                <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </nav>
    </header>

    <div class="form-container">
        <?php if (!empty($message)): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
        <?php endif; ?>

        <div class="form-card">
            <div class="form-header">
                <h2>
                    <i class="fas fa-edit"></i>
                    Edit Laptop
                </h2>
                <p>Update informasi laptop yang sudah ada</p>
            </div>

            <div class="laptop-preview">
                <img src="<?php echo htmlspecialchars($laptop['image']); ?>" alt="<?php echo htmlspecialchars($laptop['name']); ?>" onerror="this.src='IMG/placeholder.jpg'">
                <div class="laptop-preview-info">
                    <h3><?php echo htmlspecialchars($laptop['name']); ?></h3>
                    <p>ID: #<?php echo $laptop['id']; ?> | Stok: <?php echo $laptop['stock']; ?> unit | Rating: <?php echo $laptop['rating']; ?></p>
                </div>
            </div>

            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-building"></i>
                            Brand <span class="required">*</span>
                        </label>
                        <select name="brand_id" required>
                            <option value="">Pilih Brand</option>
                            <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>" <?php echo $laptop['brand_id'] == $brand['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($brand['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-laptop"></i>
                            Nama Laptop <span class="required">*</span>
                        </label>
                        <input type="text" name="name" required value="<?php echo htmlspecialchars($laptop['name']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-tag"></i>
                            Model
                        </label>
                        <input type="text" name="model" value="<?php echo htmlspecialchars($laptop['model']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-money-bill-wave"></i>
                            Harga <span class="required">*</span>
                        </label>
                        <input type="number" name="price" required value="<?php echo $laptop['price']; ?>" step="0.01">
                        <small>Dalam Rupiah, tanpa titik atau koma</small>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-microchip"></i>
                            Processor <span class="required">*</span>
                        </label>
                        <input type="text" name="processor" required value="<?php echo htmlspecialchars($laptop['processor']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-memory"></i>
                            RAM <span class="required">*</span>
                        </label>
                        <input type="text" name="ram" required value="<?php echo htmlspecialchars($laptop['ram']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-hdd"></i>
                            Storage <span class="required">*</span>
                        </label>
                        <input type="text" name="storage" required value="<?php echo htmlspecialchars($laptop['storage']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-desktop"></i>
                            Display
                        </label>
                        <input type="text" name="display" value="<?php echo htmlspecialchars($laptop['display']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-film"></i>
                            GPU
                        </label>
                        <input type="text" name="gpu" value="<?php echo htmlspecialchars($laptop['gpu']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-weight"></i>
                            Berat
                        </label>
                        <input type="text" name="weight" value="<?php echo htmlspecialchars($laptop['weight']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-battery-full"></i>
                            Baterai
                        </label>
                        <input type="text" name="battery" value="<?php echo htmlspecialchars($laptop['battery']); ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-list"></i>
                            Kategori
                        </label>
                        <select name="category">
                            <option value="">Pilih Kategori</option>
                            <?php 
                            $categories = ['Gaming', 'Office', 'Ultrabook', 'Productivity', 'Budget', 'Business', 'Lifestyle', 'Premium', '2-in-1', 'Multimedia'];
                            foreach ($categories as $cat): 
                            ?>
                            <option value="<?php echo $cat; ?>" <?php echo $laptop['category'] == $cat ? 'selected' : ''; ?>>
                                <?php echo $cat; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-star"></i>
                            Rating <span class="required">*</span>
                        </label>
                        <input type="number" name="rating" required value="<?php echo $laptop['rating']; ?>" step="0.1" min="0" max="5">
                        <small>Rating dari 0 sampai 5</small>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-boxes"></i>
                            Stok <span class="required">*</span>
                        </label>
                        <input type="number" name="stock" required value="<?php echo $laptop['stock']; ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-calendar"></i>
                            Tanggal Rilis
                        </label>
                        <input type="date" name="release_date" value="<?php echo $laptop['release_date']; ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-image"></i>
                            URL Gambar
                        </label>
                        <input type="text" name="image" value="<?php echo htmlspecialchars($laptop['image']); ?>">
                        <small>Path gambar relatif atau URL lengkap</small>
                    </div>
                </div>

                <div class="form-grid single">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-align-left"></i>
                            Deskripsi <span class="required">*</span>
                        </label>
                        <textarea name="description" required><?php echo htmlspecialchars($laptop['description']); ?></textarea>
                        <small>Deskripsi detail tentang laptop dan keunggulannya</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Update Laptop
                    </button>
                    <a href="dashboard.php" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>