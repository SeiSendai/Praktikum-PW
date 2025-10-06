<?php
session_start();
$is_logged_in = isset($_SESSION['username']);
$username = $is_logged_in ? $_SESSION['username'] : null;

// Ambil ID laptop dari query string
$laptop_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Data laptop lengkap dengan review detail
$laptops = [
    1 => [
        'id' => 1,
        'name' => 'Axioo Pongo 725',
        'brand' => 'Axioo',
        'rating' => 4.2,
        'price' => 12999000,
        'description' => 'Gaming laptop flagship dari Axioo dengan performa tinggi dan cooling system yang mumpuni untuk gaming intensif.',
        'image' => 'IMG/pongo 725.png',
        'processor' => 'Intel Core i7-10750H',
        'ram' => '16GB DDR4',
        'storage' => '512GB SSD',
        'display' => '15.6" FHD 144Hz',
        'gpu' => 'NVIDIA RTX 2050 4GB',
        'weight' => '2.3 kg',
        'battery' => '56Wh',
        'category' => 'Gaming',
        'release_date' => '2025-09-01',
        'pros' => [
            'Performa gaming sangat baik untuk AAA games',
            'Cooling system efektif dengan dual fan',
            'Layar 144Hz smooth untuk gaming kompetitif',
            'Build quality solid dan premium',
            'Port I/O lengkap termasuk USB-C'
        ],
        'cons' => [
            'Harga cukup tinggi untuk spek ini',
            'Baterai kurang awet saat gaming',
            'Agak berat untuk dibawa traveling',
            'Kipas cukup berisik saat full load'
        ],
        'review_text' => 'Axioo Pongo 725 adalah pilihan solid untuk gamers Indonesia yang mencari laptop gaming lokal dengan performa mumpuni. Dengan Intel Core i7-10750H dan GTX 1660 Ti, laptop ini mampu menjalankan game-game AAA dengan setting high-ultra di 1080p dengan framerate stabil. Cooling system dual fan bekerja efektif menjaga suhu tetap optimal, meskipun sedikit berisik saat gaming intensif. Layar 144Hz memberikan pengalaman gaming yang smooth, cocok untuk FPS dan MOBA. Build quality terasa premium dengan material yang kokoh. Port I/O sangat lengkap termasuk USB-C, HDMI 2.0, dan RJ45. Namun, baterai 56Wh kurang ideal untuk gaming unplugged, lebih cocok digunakan dengan charger. Overall, ini adalah laptop gaming terbaik dari brand lokal dengan value yang kompetitif.',
        'performance_score' => 8.5,
        'build_quality_score' => 8.0,
        'display_score' => 8.5,
        'battery_score' => 6.5,
        'value_score' => 7.5
    ],
    2 => [
        'id' => 2,
        'name' => 'Advan WorkPro',
        'brand' => 'Advan',
        'rating' => 4.0,
        'price' => 3999000,
        'description' => 'Laptop budget champion untuk kebutuhan office dan pembelajaran online dengan value terbaik di kelasnya.',
        'image' => 'IMG/Advan-Work-Pro-Lite-9.png',
        'processor' => 'Intel Celeron N4020',
        'ram' => '4GB DDR4',
        'storage' => '128GB SSD',
        'display' => '14" HD',
        'gpu' => 'Intel UHD Graphics',
        'weight' => '1.5 kg',
        'battery' => '38Wh',
        'category' => 'Office',
        'release_date' => '2023-08-15',
        'pros' => [
            'Harga sangat terjangkau di bawah 4 juta',
            'Ringan dan mudah dibawa kemana-mana',
            'SSD membuat booting dan loading cepat',
            'Cukup untuk office work dan browsing',
            'Build quality baik untuk harga segini'
        ],
        'cons' => [
            'RAM 4GB terbatas untuk multitasking berat',
            'Processor Celeron tidak cocok untuk task berat',
            'Display hanya HD, bukan Full HD',
            'Speaker kurang keras dan kurang bass',
            'Tidak bisa gaming sama sekali'
        ],
        'review_text' => 'Advan WorkPro adalah definisi sempurna dari "value for money" untuk laptop budget. Dengan harga di bawah 4 juta, laptop ini menawarkan pengalaman komputasi yang decent untuk kebutuhan basic. SSD 128GB membuat booting Windows hanya 10 detik dan aplikasi office membuka dengan cepat. Processor Celeron N4020 memang entry-level, tapi cukup untuk Microsoft Office, browsing dengan 5-10 tabs, dan streaming video 1080p. RAM 4GB adalah bottleneck utama - multitasking agak terbatas, disarankan tidak membuka terlalu banyak aplikasi sekaligus. Display 14" HD cukup terang untuk indoor, meskipun resolusi bukan Full HD. Build quality mengejutkan bagus untuk harga ini - tidak ada flex di keyboard dan trackpad responsif. Baterai bisa bertahan 4-5 jam untuk office work. Sangat recommended untuk pelajar, mahasiswa, atau pekerja kantoran dengan budget terbatas.',
        'performance_score' => 6.0,
        'build_quality_score' => 7.0,
        'display_score' => 6.5,
        'battery_score' => 7.0,
        'value_score' => 9.0
    ],
    3 => [
        'id' => 3,
        'name' => 'Zyrex Sky 232',
        'brand' => 'Zyrex',
        'rating' => 3.5,
        'price' => 7499000,
        'description' => 'Ultrabook ringan dengan daya tahan baterai solid untuk mobilitas tinggi profesional dan pelajar.',
        'image' => 'IMG/Zyrex.png',
        'processor' => 'Intel Core i5-1135G7',
        'ram' => '8GB DDR4',
        'storage' => '256GB SSD',
        'display' => '13.3" FHD IPS',
        'gpu' => 'Intel Iris Xe Graphics',
        'weight' => '1.2 kg',
        'battery' => '50Wh',
        'category' => 'Ultrabook',
        'release_date' => '2021-01-08',
        'pros' => [
            'Sangat ringan hanya 1.2kg, perfect untuk mobilitas',
            'Baterai awet bisa 7-8 jam usage normal',
            'Display IPS Full HD dengan color accuracy bagus',
            'Intel Iris Xe cukup untuk light gaming dan editing',
            'Desain minimalis dan professional'
        ],
        'cons' => [
            'Harga agak mahal untuk spek yang ditawarkan',
            'RAM 8GB tidak bisa di-upgrade (soldered)',
            'Port terbatas, hanya 2x USB-C',
            'Storage 256GB terasa kurang untuk profesional',
            'Keyboard travel pendek, kurang nyaman mengetik lama'
        ],
        'review_text' => 'Zyrex Sky 232 adalah ultrabook yang menargetkan profesional mobile dan pelajar yang mengutamakan portabilitas. Dengan berat hanya 1.2kg dan ketebalan 15mm, laptop ini sangat mudah dibawa dalam tas ransel. Display 13.3" IPS Full HD memiliki color accuracy yang baik untuk content consumption dan light editing. Intel Core i5-1135G7 dengan Iris Xe Graphics memberikan performa yang cukup untuk productivity work, bahkan bisa handle light gaming seperti League of Legends atau Valorant di low-medium settings. Baterai 50Wh sangat awet, bisa bertahan 7-8 jam untuk office work atau 5-6 jam untuk browsing dan streaming. Build quality solid dengan material aluminium yang terasa premium. Namun ada beberapa kekurangan: RAM 8GB soldered tidak bisa upgrade, port terbatas hanya USB-C (butuh dongle), dan keyboard dengan travel pendek kurang nyaman untuk typing marathon. Untuk harga 7.4 juta, value-nya kurang kompetitif dibanding kompetitor. Recommended untuk yang prioritas portabilitas dan baterai life.',
        'performance_score' => 7.5,
        'build_quality_score' => 8.5,
        'display_score' => 8.0,
        'battery_score' => 8.5,
        'value_score' => 6.5
    ]
];

// Redirect jika laptop tidak ditemukan
if (!isset($laptops[$laptop_id])) {
    header('Location: index.php');
    exit();
}

$laptop = $laptops[$laptop_id];

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d F Y', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Review lengkap <?php echo htmlspecialchars($laptop['name']); ?> - Spesifikasi, kelebihan, kekurangan, dan performa">
    <title>Review <?php echo htmlspecialchars($laptop['name']); ?> - Review Laptop Lokal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .review-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .breadcrumb {
            background: var(--card-bg);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px var(--shadow-light);
        }

        .breadcrumb a {
            color: #3b82f6;
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb a:hover {
            color: #1d4ed8;
        }

        .breadcrumb span {
            color: var(--text-muted);
        }

        .review-header {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 16px var(--shadow-light);
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .laptop-image-main {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 24px var(--shadow-medium);
        }

        .laptop-meta h1 {
            font-size: 2rem;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .brand-tag {
            display: inline-block;
            padding: 6px 16px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .rating-large {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: var(--hover-bg);
            border-radius: 8px;
        }

        .rating-stars-large {
            display: flex;
            gap: 5px;
        }

        .rating-stars-large i {
            font-size: 1.5rem;
            color: #fbbf24;
        }

        .rating-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .price-large {
            font-size: 2.5rem;
            font-weight: 700;
            color: #10b981;
            margin: 15px 0;
        }

        .quick-specs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 20px;
        }

        .spec-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: var(--hover-bg);
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .spec-item i {
            color: #3b82f6;
            font-size: 1.2rem;
        }

        .review-section {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 16px var(--shadow-light);
        }

        .review-section h2 {
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .review-text {
            line-height: 1.8;
            color: var(--text-muted);
            font-size: 1.05rem;
            text-align: justify;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .spec-card {
            padding: 20px;
            background: var(--hover-bg);
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }

        .spec-card h3 {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .spec-card p {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .pros-cons-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .pros-list, .cons-list {
            padding: 20px;
            border-radius: 8px;
        }

        .pros-list {
            background: rgba(16, 185, 129, 0.05);
            border: 2px solid rgba(16, 185, 129, 0.2);
        }

        .cons-list {
            background: rgba(239, 68, 68, 0.05);
            border: 2px solid rgba(239, 68, 68, 0.2);
        }

        .pros-list h3, .cons-list h3 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .pros-list h3 {
            color: #10b981;
        }

        .cons-list h3 {
            color: #ef4444;
        }

        .pros-list ul, .cons-list ul {
            list-style: none;
            padding: 0;
        }

        .pros-list li, .cons-list li {
            padding: 10px 0;
            padding-left: 25px;
            position: relative;
            line-height: 1.6;
            color: var(--text-muted);
        }

        .pros-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .cons-list li::before {
            content: "✗";
            position: absolute;
            left: 0;
            color: #ef4444;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .score-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .score-item {
            text-align: center;
            padding: 20px;
            background: var(--hover-bg);
            border-radius: 8px;
        }

        .score-circle {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .score-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 10px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--secondary-gradient);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        @media (max-width: 768px) {
            .review-header {
                grid-template-columns: 1fr;
            }

            .pros-cons-grid {
                grid-template-columns: 1fr;
            }

            .quick-specs {
                grid-template-columns: 1fr;
            }

            .laptop-meta h1 {
                font-size: 1.5rem;
            }

            .price-large {
                font-size: 2rem;
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
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="index.php#review" class="active">Review</a></li>
                    <?php if ($is_logged_in): ?>
                        <li><a href="dashboard.php"><i class="fas fa-user-circle"></i> Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <?php endif; ?>
                </ul>
                <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </nav>
    </header>

    <main style="margin-top: 100px;">
        <div class="review-container">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
                <span>/</span>
                <a href="index.php#review">Review</a>
                <span>/</span>
                <span><?php echo htmlspecialchars($laptop['name']); ?></span>
            </div>

            <!-- Review Header -->
            <div class="review-header">
                <div>
                    <img src="<?php echo htmlspecialchars($laptop['image']); ?>" 
                         alt="<?php echo htmlspecialchars($laptop['name']); ?>" 
                         class="laptop-image-main"
                         onerror="this.src='IMG/placeholder.jpg'">
                </div>
                <div class="laptop-meta">
                    <span class="brand-tag"><?php echo htmlspecialchars($laptop['brand']); ?></span>
                    <h1><?php echo htmlspecialchars($laptop['name']); ?></h1>
                    <p style="color: var(--text-muted); margin-bottom: 10px;">
                        <i class="fas fa-calendar"></i> Dirilis: <?php echo formatDate($laptop['release_date']); ?>
                    </p>
                    
                    <div class="rating-large">
                        <div class="rating-stars-large">
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
                        <div>
                            <div class="rating-number"><?php echo number_format($laptop['rating'], 1); ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">dari 5.0</div>
                        </div>
                    </div>

                    <div class="price-large"><?php echo formatRupiah($laptop['price']); ?></div>

                    <div class="quick-specs">
                        <div class="spec-item">
                            <i class="fas fa-microchip"></i>
                            <span><?php echo htmlspecialchars($laptop['processor']); ?></span>
                        </div>
                        <div class="spec-item">
                            <i class="fas fa-memory"></i>
                            <span><?php echo htmlspecialchars($laptop['ram']); ?></span>
                        </div>
                        <div class="spec-item">
                            <i class="fas fa-hdd"></i>
                            <span><?php echo htmlspecialchars($laptop['storage']); ?></span>
                        </div>
                        <div class="spec-item">
                            <i class="fas fa-desktop"></i>
                            <span><?php echo htmlspecialchars($laptop['display']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Text -->
            <div class="review-section">
                <h2><i class="fas fa-file-alt"></i> Review Lengkap</h2>
                <p class="review-text"><?php echo nl2br(htmlspecialchars($laptop['review_text'])); ?></p>
            </div>

            <!-- Spesifikasi Lengkap -->
            <div class="review-section">
                <h2><i class="fas fa-list"></i> Spesifikasi Lengkap</h2>
                <div class="specs-grid">
                    <div class="spec-card">
                        <h3><i class="fas fa-microchip"></i> Processor</h3>
                        <p><?php echo htmlspecialchars($laptop['processor']); ?></p>
                    </div>
                    <div class="spec-card">
                        <h3><i class="fas fa-memory"></i> RAM</h3>
                        <p><?php echo htmlspecialchars($laptop['ram']); ?></p>
                    </div>
                    <div class="spec-card">
                        <h3><i class="fas fa-hdd"></i> Storage</h3>
                        <p><?php echo htmlspecialchars($laptop['storage']); ?></p>
                    </div>
                    <div class="spec-card">
                        <h3><i class="fas fa-desktop"></i> Display</h3>
                        <p><?php echo htmlspecialchars($laptop['display']); ?></p>
                    </div>
                    <div class="spec-card">
                        <h3><i class="fas fa-tv"></i> Graphics</h3>
                        <p><?php echo htmlspecialchars($laptop['gpu']); ?></p>
                    </div>
                    <div class="spec-card">
                        <h3><i class="fas fa-weight"></i> Weight</h3>
                        <p><?php echo htmlspecialchars($laptop['weight']); ?></p>
                    </div>
                    <div class="spec-card">
                        <h3><i class="fas fa-battery-full"></i> Battery</h3>
                        <p><?php echo htmlspecialchars($laptop['battery']); ?></p>
                    </div>
                    <div class="spec-card">
                        <h3><i class="fas fa-tag"></i> Category</h3>
                        <p><?php echo htmlspecialchars($laptop['category']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Pros & Cons -->
            <div class="review-section">
                <h2><i class="fas fa-balance-scale"></i> Kelebihan & Kekurangan</h2>
                <div class="pros-cons-grid">
                    <div class="pros-list">
                        <h3><i class="fas fa-thumbs-up"></i> Kelebihan</h3>
                        <ul>
                            <?php foreach ($laptop['pros'] as $pro): ?>
                                <li><?php echo htmlspecialchars($pro); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="cons-list">
                        <h3><i class="fas fa-thumbs-down"></i> Kekurangan</h3>
                        <ul>
                            <?php foreach ($laptop['cons'] as $con): ?>
                                <li><?php echo htmlspecialchars($con); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Performance Scores -->
            <div class="review-section">
                <h2><i class="fas fa-chart-line"></i> Skor Performa</h2>
                <div class="score-grid">
                    <div class="score-item">
                        <div class="score-circle"><?php echo $laptop['performance_score']; ?></div>
                        <div class="score-label">Performance</div>
                    </div>
                    <div class="score-item">
                        <div class="score-circle" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><?php echo $laptop['build_quality_score']; ?></div>
                        <div class="score-label">Build Quality</div>
                    </div>
                    <div class="score-item">
                        <div class="score-circle" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"><?php echo $laptop['display_score']; ?></div>
                        <div class="score-label">Display</div>
                    </div>
                    <div class="score-item">
                        <div class="score-circle" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);"><?php echo $laptop['battery_score']; ?></div>
                        <div class="score-label">Battery Life</div>
                    </div>
                    <div class="score-item">
                        <div class="score-circle" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);"><?php echo $laptop['value_score']; ?></div>
                        <div class="score-label">Value for Money</div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div style="text-align: center; margin: 40px 0;">
                <a href="index.php#review" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar Review
                </a>
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
                2025 Review Laptop Lokal - Projek Pengnative Handal
            </small></p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>