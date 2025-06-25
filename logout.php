<?php
// Pastikan session dimulai
session_start();

// Unset semua variabel session satu per satu untuk memastikan semuanya terhapus
unset($_SESSION["user_id"]);
unset($_SESSION["username"]);
unset($_SESSION["role"]);
unset($_SESSION["is_login"]);
unset($_SESSION["last_activity"]);

// Hapus semua variabel session lainnya yang mungkin ada
$_SESSION = array();

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman index dengan parameter logout=success
header("Location: index.php");
exit();
?>