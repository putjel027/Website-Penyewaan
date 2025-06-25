<?php
include("includes/config.php");

// Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, redirect ke halaman yang sesuai
if (isset($_SESSION["is_login"]) && $_SESSION["is_login"] === true) {
    // Cek apakah ada redirect_to parameter
    if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
        $redirect_url = fixRedirectPath($_GET['redirect_to']);
        // Security check: pastikan URL aman
        if (isValidRedirectUrl($redirect_url)) {
            header("Location: " . $redirect_url);
            exit;
        }
    }
    
    // Redirect berdasarkan role jika tidak ada redirect_to
    if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        header("Location: ./Admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$login_message = "";
$alert_type = "";
$reset_message = "";
$reset_alert_type = "";

// Proses login
if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Menggunakan prepared statement
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // Verifikasi password (plain text)
        if ($data["password"] === $password) {
            // Simpan session login
            $_SESSION["user_id"] = $data["id"];
            $_SESSION["username"] = $data["username"];
            $_SESSION["role"] = $data["role"];
            $_SESSION["is_login"] = true;
            
            $login_message = "Login berhasil! Selamat datang, " . htmlspecialchars($username) . ".";
            $alert_type = "success";
            
            // Tentukan redirect URL untuk SweetAlert
            $redirect_url = "index.php";
            if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
                $redirect_url = fixRedirectPath($_GET['redirect_to']);
                if (!isValidRedirectUrl($redirect_url)) {
                    $redirect_url = "index.php";
                }
            } elseif ($data["role"] === "admin") {
                $redirect_url = "./Admin/dashboard.php";
            }
        } else {
            $login_message = "Password salah. Silakan coba lagi.";
            $alert_type = "error";
        }
    } else {
        $login_message = "Username tidak ditemukan. Silakan periksa kembali.";
        $alert_type = "error";
    }
    $stmt->close();
}

// Proses reset password
if (isset($_POST["reset_password"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $nohp = $_POST["nohp"];
    $new_password = $_POST["new_password"];
    
    // Validasi input
    if (empty($username) || empty($email) || empty($nohp) || empty($new_password)) {
        $reset_message = "Semua field harus diisi.";
        $reset_alert_type = "error";
    } else {
        // Cek apakah username, email, dan nohp cocok
        $sql = "SELECT * FROM users WHERE username = ? AND email = ? AND nohp = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $nohp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            // Update password (plain text)
            $sql_update = "UPDATE users SET password = ? WHERE username = ? AND email = ? AND nohp = ?";
            $stmt_update = $db->prepare($sql_update);
            $stmt_update->bind_param("ssss", $new_password, $username, $email, $nohp);
            
            if ($stmt_update->execute()) {
                $reset_message = "Password berhasil direset. Silakan login dengan password baru.";
                $reset_alert_type = "success";
            } else {
                $reset_message = "Gagal mereset password: " . $stmt_update->error;
                $reset_alert_type = "error";
            }
            $stmt_update->close();
        } else {
            $reset_message = "Username, email, atau nomor HP tidak cocok.";
            $reset_alert_type = "error";
        }
        $stmt->close();
    }
}

// Fungsi untuk memvalidasi URL redirect
function isValidRedirectUrl($url) {
    // Jika URL absolut, pastikan domain sama
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $parsed_url = parse_url($url);
        return isset($parsed_url['host']) && $parsed_url['host'] === $_SERVER['HTTP_HOST'];
    }
    
    // URL relatif, aman
    return true;
}

// Fungsi untuk memperbaiki path redirect
function fixRedirectPath($url) {
    // Jika sudah absolute URL, return as is
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return $url;
    }
    
    // Jika URL dimulai dengan "users/", buat absolute URL
    if (strpos($url, 'users/') === 0) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Dapatkan path sampai folder Decoration
        $script_path = $_SERVER['SCRIPT_NAME'];
        $decoration_pos = strpos($script_path, '/Decoration/');
        
        if ($decoration_pos !== false) {
            $base_path = substr($script_path, 0, $decoration_pos + strlen('/Decoration'));
        } else {
            // Fallback
            $base_path = '/Decoration';
        }
        
        return $protocol . '://' . $host . $base_path . '/' . $url;
    }
    
    return $url;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OM Project Decoration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#000000">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .login-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }
        .login-box img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 0 0.2rem rgba(78, 84, 200, 0.25);
        }
        .btn-custom {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            border: none;
            border-radius: 10px;
            padding: 12px;
            width: 100%;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #3b3fa6, #6f73d6);
            transform: translateY(-2px);
        }
        .btn-outline-custom {
            border-color: #4e54c8;
            color: #4e54c8;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-outline-custom:hover {
            background-color: #4e54c8;
            color: white;
        }
        .text-link {
            color: #4e54c8;
            text-decoration: none;
            font-weight: 500;
        }
        .text-link:hover {
            text-decoration: underline;
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .modal-content {
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box fade-in">
            <img src="./assets/decor.png" alt="OM Project Logo" />
            <h2 class="fw-bold text-dark mb-4">Login OM Project</h2>
            
            <!-- Tampilkan informasi redirect jika ada -->
            <?php if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])): ?>
                <div class="alert alert-info text-center mb-3" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    Silakan login untuk melanjutkan ke halaman pemesanan
                </div>
            <?php endif; ?>
            
            <form action="<?php echo $_SERVER['PHP_SELF'] . (isset($_GET['redirect_to']) ? '?redirect_to=' . urlencode($_GET['redirect_to']) : ''); ?>" method="POST">
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Username" name="username" required />
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password" required />
                </div>
                <button type="submit" name="login" class="btn btn-custom">Login</button>
            </form>
            <p class="mt-3 text-dark">
                Belum punya akun? <a href="register.php<?php echo isset($_GET['redirect_to']) ? '?redirect_to=' . urlencode($_GET['redirect_to']) : ''; ?>" class="text-link">Register di sini</a>
            </p>
            <p class="mt-2 text-dark">
                <a href="#" class="text-link" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">Lupa Password?</a>
            </p>
            
            <!-- Tombol kembali jika ada redirect -->
            <?php if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])): ?>
                <div class="text-center mt-2">
                    <a href="index.php" class="btn btn-outline-custom btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="resetPasswordModalLabel"><i class="bi bi-key me-2"></i>Reset Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF'] . (isset($_GET['redirect_to']) ? '?redirect_to=' . urlencode($_GET['redirect_to']) : ''); ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="resetUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="resetUsername" name="username" placeholder="Masukkan username Anda" required>
                        </div>
                        <div class="mb-3">
                            <label for="resetEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="resetEmail" name="email" placeholder="Masukkan email Anda" required>
                        </div>
                        <div class="mb-3">
                            <label for="resetNohp" class="form-label">Nomor HP</label>
                            <input type="text" class="form-control" id="resetNohp" name="nohp" placeholder="Masukkan nomor HP Anda" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Masukkan password baru" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="reset_password" class="btn btn-custom">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        // SweetAlert untuk pesan login
        <?php if (!empty($login_message)): ?>
            Swal.fire({
                icon: '<?php echo $alert_type; ?>',
                title: '<?php echo $alert_type === "success" ? "Berhasil!" : "Gagal!"; ?>',
                text: '<?php echo addslashes($login_message); ?>',
                confirmButtonColor: '#4e54c8',
                confirmButtonText: '<?php echo $alert_type === "success" ? "Lanjutkan" : "Coba Lagi"; ?>'
            }).then((result) => {
                if (result.isConfirmed && '<?php echo $alert_type; ?>' === 'success') {
                    window.location.href = '<?php echo $redirect_url; ?>';
                }
            });
        <?php endif; ?>

        // SweetAlert untuk pesan reset password
        <?php if (!empty($reset_message)): ?>
            Swal.fire({
                icon: '<?php echo $reset_alert_type; ?>',
                title: '<?php echo $reset_alert_type === "success" ? "Berhasil!" : "Gagal!"; ?>',
                text: '<?php echo addslashes($reset_message); ?>',
                confirmButtonColor: '#4e54c8',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed && '<?php echo $reset_alert_type; ?>' === 'success') {
                    var resetModal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                    if (resetModal) resetModal.hide();
                }
            });
        <?php endif; ?>

        // Animasi fade-in saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach(element => {
                element.classList.add('visible');
            });
        });

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('service-worker.js')
                    .then(reg => console.log("✅ Service Worker registered", reg))
                    .catch(err => console.error("❌ Service Worker registration failed", err));
            });
        }
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('✅ beforeinstallprompt event fired');
        });
        window.addEventListener('appinstalled', () => {
            console.log('✅ App installed');
        });
    </script>
</body>
</html>