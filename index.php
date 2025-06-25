<?php
// Pastikan session dimulai di awal file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Include config file jika diperlukan
include_once("includes/config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OM Project Decoration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* Global Styles */
body,
html {
  margin: 0;
  padding: 0;
  font-family: "Poppins", sans-serif;
  height: 100%; /* Full height */
  background-color: #f9f9f9;
  color: #333; /* Default text color */
}

/* Main Layout */
main {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh; /* Full screen height */
  background: url("assets/Decoration.jpg") no-repeat center center;
  background-size: cover; /* Ensure the image covers the whole area */
  position: relative;
  color: white;
}

/* Overlay Effect */
main::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6); /* Black overlay with opacity */
  z-index: 0; /* Ensure overlay is behind content */
}

.content-wrapper {
  position: relative;
  z-index: 1; /* Place content above overlay */
  text-align: center;
  padding: 20px;
}

/* Text on Top of Background */
main h2 {
  font-size: 2.5rem;
  font-weight: 600;
  color: #fff;
  margin-bottom: 20px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  main h2 {
    font-size: 1.8rem;
    padding: 0 10px;
  }
}

    </style>

</head>
<body>

<?php include('users/header.php'); ?>

<main>
    <div class="content-wrapper text-center mt-5">
        <h2>Halo, Selamat Datang di Sistem Reservasi <br/> Om Project Decoration</h2>
    </div>
</main>

<?php include('users/galeri.php'); ?>
<?php include('users/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>