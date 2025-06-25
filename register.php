<?php
include("includes/config.php");
session_start();

$register_message = "";
$alert_type = "";

// Proses register
if (isset($_POST["register"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $alamat = $_POST["alamat"];
    $nohp = $_POST["nohp"];
    $role = "user"; // Default role user biasa

    // Cek apakah username atau email sudah ada
    $sql_check = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt_check = $db->prepare($sql_check);
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check && $result_check->num_rows > 0) {
        $register_message = "Username atau email sudah digunakan, pilih yang lain.";
        $alert_type = "error";
    } else {
        // Insert user baru
        $sql = "INSERT INTO users (username, password, email, alamat, nohp, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssss", $username, $password, $email, $alamat, $nohp, $role);
        if ($stmt->execute()) {
            $register_message = "Registrasi berhasil! Silakan login.";
            $alert_type = "success";
        } else {
            $register_message = "Registrasi gagal, coba lagi.";
            $alert_type = "error";
        }
    }
    $stmt->close();
    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OM Project Decoration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .register-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }
        .register-box img {
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
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box fade-in">
            <img src="./assets/decor.png" alt="OM Project Logo" />
            <h2 class="fw-bold text-dark mb-4">Register OM Project</h2>
            <form action="" method="POST">
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Username" name="username" required />
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" required />
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password" required />
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Alamat" name="alamat" required />
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="No HP" name="nohp" required />
                </div>
                <button type="submit" name="register" class="btn btn-custom">Register</button>
            </form>
            <p class="mt-3 text-dark">Sudah punya akun? <a href="index.php" class="text-link">Login di sini</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        // SweetAlert untuk pesan registrasi
        <?php if (!empty($register_message)): ?>
            Swal.fire({
                icon: '<?php echo $alert_type; ?>',
                title: '<?php echo $alert_type === "success" ? "Berhasil!" : "Gagal!"; ?>',
                text: '<?php echo $register_message; ?>',
                confirmButtonColor: '#4e54c8',
                confirmButtonText: '<?php echo $alert_type === "success" ? "Login Sekarang" : "Coba Lagi"; ?>'
            }).then((result) => {
                if (result.isConfirmed && '<?php echo $alert_type; ?>' === 'success') {
                    window.location.href = 'index.php';
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
    </script>
</body>
</html>