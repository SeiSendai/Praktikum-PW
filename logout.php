<?php
session_start();

// Simpan username untuk pesan goodbye (opsional)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Hapus cookie session jika ada
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Review Laptop Lokal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .logout-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px 40px;
            width: 100%;
            max-width: 450px;
            text-align: center;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logout-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            animation: scaleIn 0.5s ease 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .logout-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .logout-container h1 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 15px;
            animation: fadeIn 0.6s ease 0.3s both;
        }

        .logout-container p {
            color: #666;
            font-size: 1rem;
            margin-bottom: 30px;
            line-height: 1.6;
            animation: fadeIn 0.6s ease 0.4s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .username-display {
            background: #f8f9fa;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-weight: 600;
            color: #667eea;
            animation: fadeIn 0.6s ease 0.5s both;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            animation: fadeIn 0.6s ease 0.6s both;
        }

        .btn {
            padding: 14px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        .loading-dots {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 20px 0;
        }

        .dot {
            width: 10px;
            height: 10px;
            background: #667eea;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        .dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1);
            }
        }

        .footer-text {
            margin-top: 30px;
            font-size: 0.85rem;
            color: #999;
            animation: fadeIn 0.6s ease 0.7s both;
        }

        @media (max-width: 480px) {
            .logout-container {
                padding: 40px 25px;
            }

            .logout-container h1 {
                font-size: 1.5rem;
            }

            .logout-icon {
                width: 70px;
                height: 70px;
            }

            .logout-icon i {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>Berhasil Logout</h1>
        
        <p>Terima kasih telah menggunakan sistem kami, <strong><?php echo htmlspecialchars($username); ?></strong>!</p>
        
        <div class="username-display">
            <i class="fas fa-user-circle"></i>
            Session telah dihapus dengan aman
        </div>

        <div class="loading-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <p style="font-size: 0.9rem; color: #999; margin-bottom: 25px;">
            Mengarahkan ke halaman login dalam <span id="countdown">3</span> detik...
        </p>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Login Kembali
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>
        </div>

        <div class="footer-text">
            <i class="fas fa-shield-alt"></i> Session Anda telah diakhiri dengan aman
        </div>
    </div>

    <script>
        // Auto redirect ke login setelah 3 detik
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = 'login.php';
            }
        }, 1000);

        // Optional: User bisa cancel auto redirect dengan hover/click
        const container = document.querySelector('.logout-container');
        container.addEventListener('mouseenter', () => {
            clearInterval(timer);
            countdownElement.parentElement.innerHTML = '<i class="fas fa-pause-circle"></i> Auto redirect dihentikan';
        });

        console.log('✅ Logout berhasil - Session destroyed');
    </script>
</body>
</html>