<?php
session_start();
require 'config.php';
require 'login_session.php';

$staffid = $_SESSION['staffid'];

// Ambil data user dari database
$stmt = $conn->prepare("SELECT username, foto FROM login WHERE staffid = ?");
$stmt->bind_param("i", $staffid);
$stmt->execute();   
$stmt->bind_result($username, $foto);
$stmt->fetch();
$stmt->close();

// Ambil data nama usaha dan alamat dari database
$stmt = $conn->prepare("SELECT namausaha, alamat FROM identitasusaha LIMIT 1");
$stmt->execute();
$stmt->bind_result($namaUsaha, $alamatUsaha);
$stmt->fetch();
$stmt->close();

// Ambil banyak pegawai dari database
$result = $conn->query("SELECT COUNT(*) as count FROM barang");
$row = $result->fetch_assoc();
$jmlhbrg = $row['count'];

// Ambil tgl awal dan akhir
$month = intval(date('m'));
$year = intval(date('Y'));
if ($month < 12) {
    $endmonth = $month+1;
    $endyear = $year;
} else {
    $endmonth = 1;
    $endyear = $year=+1;
}
$startdate = "$year-$month-01";
$enddate = "$endyear-$endmonth-01";

// Ambil jumlah penghargaan bulan ini berdasarkan tanggal surat
$sql = "SELECT COUNT(*) as count FROM pembelian WHERE tanggal >= '$startdate' AND tanggal < '$enddate'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$jmlhpembelianbrg = $row['count'];

// Ambil jumlah penghargaan bulan ini berdasarkan tanggal surat
$sql = "SELECT COUNT(*) as count FROM penjualan WHERE tanggal >= '$startdate' AND tanggal < '$enddate'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$jmlhpenjualanbrg = $row['count'];

?>

<?php require 'head.php'; ?>
<div class="wrapper">
    <header>
        <h4 style="text-align:center;"><?php echo htmlspecialchars($namaUsaha ?? ''); ?></h4>
        <p style="text-align:center;"><?php echo htmlspecialchars($alamatUsaha ?? ''); ?></p>
    </header>


    <?php include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="/JAYA_GUDANG/CSS/index.css">
</head>
<body>

<div class="content" id="content">
            <div class="container-fluid mt-3">
                <div class="cards-container">
                    <!-- Card 1: Total Pegawai -->
                    <div class="card card-tipe">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Stok Barang</h5>
                                    <h4><p><?php echo "$jmlhbrg";?></p></h4>
                            </div>
                            <div class="card-icon-wrapper">
                                <i class="fas fa-shopping-basket"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Penghargaan -->
                <div class="card card-stok">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5>Pembelian Barang</h5>
                                <h4><p><?php echo "$jmlhpembelianbrg";?></p></h4>
                            </div>
                            <div class="card-icon-wrapper">
                                <i class="fas fa-cart-plus"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Peringatan -->
                <div class="card card-merek">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5>Penjualan Barang</h5>
                                <h4><p><?php echo "$jmlhpenjualanbrg";?></p></h4>
                            </div>
                            <div class="card-icon-wrapper">
                                <i class="fab fa-cc-amazon-pay"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full-width card for Aplikasi Stok Barang -->
            <div class="full-width-card">
                <div class="card w-100">
                    <div class="card-header"><strong>Aplikasi Stok Barang</strong></div>
                    <img src="/JAYA_GUDANG/gambar/gudang_gambar.png" class="img-fluid" style="display:block; margin:auto;">
                </div>
            </div>
        </div>
    </div>

    <?php require 'footer.php'; ?>
</div>
    
</body>
</html>