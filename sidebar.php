<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi HRD</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="/JAYA_GUDANG/CSS/sidebar.css">
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <div class="user-info">
        <a href="profile.php">
        <img src="foto/<?php echo htmlspecialchars($foto); ?>" alt="User Photo" class="user-photo">
        <p class="user-name"><?php echo htmlspecialchars($username); ?></p>
        </a>
    </div>
    <ul>
        <li><a href="index.php"><span><i class="fas fa-home"></i> Home</span></a></li>
        <li><a href="profile.php"><span><i class="fas fa-user"></i> Profile</span></a></li>
        <li><a href="identitasusaha.php"><span><i class="fas fa-building"></i> Identitas Usaha</span></a></li>
        <li>
            <a class="menu-toggle"><span><i class="fas fa-users"></i> Master</span><i class="fas fa-chevron-right arrow"></i></a>
            <ul class="sub-menu">
                <li><a href="stok_barang.php"><span>Stok Barang</span></a></li>
                <li><a href="pembelian_barang.php"><span>Pembelian Barang</span></a></li>
                <li><a href="penjualan_barang.php"><span>Penjualan Barang</span></a></li>
            </ul>
        </li>
        <li><a href="logout.php"><span><i class="fas fa-sign-out-alt"></i> Logout</span></a></li>
    </ul>
    <div class="toggle-sidebar">
        <i class="fas fa-bars"></i>
    </div>
</div>

</body>
</html>