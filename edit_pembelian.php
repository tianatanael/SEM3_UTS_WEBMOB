<?php
session_start();
require 'config.php';
require 'login_session.php';

// Cek apakah metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $idbeli = isset($_POST['idbeli']) ? $_POST['idbeli'] : null;
    $idbarang = isset($_POST['idbarang']) ? $_POST['idbarang'] : null;
    $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : null;
    $jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : null;
    $hargasatuan = isset($_POST['hargasatuan']) ? $_POST['hargasatuan'] : null;


    // Validasi jika semua data ada
    if ($idbeli && $idbarang && $tanggal && $jumlah && $hargasatuan) {
        
        $stmt = $conn->prepare("SELECT idbarang, jumlah FROM pembelian Where idbeli = ?");
        $stmt->bind_param("s", $idbeli);
        $stmt->execute();
        $stmt->bind_result($idbarang_awal, $jumlah_awal);
        $stmt->fetch();
        $stmt->close();

        if (!$idbarang_awal) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan idbeli.'];
            header('Location: pembelian.php');
            exit();
        }
        
        $stmt = $conn->prepare("UPDATE pembelian SET idbarang = ?, tanggal = ?, jumlah = ?, hargasatuan = ? WHERE idbeli = ?");
        $stmt->bind_param("ssiis", $idbarang, $tanggal, $jumlah, $hargasatuan, $idbeli);
        if ($stmt->execute()) {

        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan dalam mengubah data pembelian.'];
            header('Location: pembelian.php');
            exit();
        }

        $stmt = $conn->prepare("SELECT stok FROM barang WHERE idbarang = ?");
        $stmt->bind_param("s", $idbarang_awal);
        $stmt->execute();
        $stmt->bind_result($stok_awal);
        $stmt->fetch();
        $stmt->close();

        $balikan = $stok_awal - $jumlah_awal;
        if ($balikan < 0) {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Kesalahan dalam mengubah stok barang $idbarang_awal $stok_awal $balikan."];
            header('Location: pembelian.php');
            exit();
        }

        $stmt = $conn->prepare("UPDATE barang SET stok = ? WHERE idbarang = ?");
        $stmt->bind_param("is", $balikan, $idbarang_awal);
        if ($stmt->execute()) {
            
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan dalam mengubah stok barang.'];
            header('Location: pembelian.php');
            exit(); 
        }
        $stmt->close();

        // Cek apakah idbarang ada di tabel pegawai
        $stmt = $conn->prepare("SELECT stok, namabarang FROM barang WHERE idbarang = ?");
        $stmt->bind_param('s', $idbarang);
        $stmt->execute();
        $stmt->bind_result($stok, $nama);
        $stmt->fetch();
        $stmt->close();

        if (!$nama) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan.'];
            header('Location: pembelian.php');
            exit();
        }

        $stokbaru = $stok + $jumlah;
        $stmt = $conn->prepare("UPDATE barang SET stok = ? WHERE idbarang = ?");
        $stmt->bind_param("is", $stokbaru, $idbarang);
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Data berhasil diubah.'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan saat mengubah stok barang.'];
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Data yang dikirim tidak lengkap.'];
    }

    // Redirect kembali ke halaman pembelian setelah proses selesai
    header("Location: pembelian.php");
    exit();
} else {
    // Jika request bukan POST, redirect ke halaman pembelian
    header("Location: pembelian.php");
    exit();
}
?>
