<?php
session_start();
require 'config.php';
require 'login_session.php';

// Cek apakah metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $idjual = isset($_POST['idjual']) ? $_POST['idjual'] : null;
    $idbarang = isset($_POST['idbarang']) ? $_POST['idbarang'] : null;
    $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : null;
    $jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : null;
    $hargasatuan = isset($_POST['hargasatuan']) ? $_POST['hargasatuan'] : null;


    // Validasi jika semua data ada
    if ($idjual && $idbarang && $tanggal && $jumlah && $hargasatuan) {
        
        $stmt = $conn->prepare("SELECT idbarang, jumlah FROM penjualan Where idjual = ?");
        $stmt->bind_param("s", $idjual);
        $stmt->execute();
        $stmt->bind_result($idbarang_awal, $jumlah_awal);
        $stmt->fetch();
        $stmt->close();

        if (!$idbarang_awal) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan pada mencari barang.'];
            header('Location: penjualan.php');
            exit();
        }

        // Mengubah stok lama
        $stmt = $conn->prepare("SELECT stok FROM barang WHERE idbarang = ?");
        $stmt->bind_param("s", $idbarang_awal);
        $stmt->execute();
        $stmt->bind_result($stok_awal);
        $stmt->fetch();
        $stmt->close();

        $balikan = $stok_awal + $jumlah_awal;

        $stmt = $conn->prepare("UPDATE barang SET stok = ? WHERE idbarang = ?");
        $stmt->bind_param("is", $balikan, $idbarang_awal);
        if ($stmt->execute()) {
            
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan dalam mengubah stok barang lama.'];
            header('Location: penjualan.php');
            exit(); 
        }
        $stmt->close();

        // Cek apakah idbarang baru ada di tabel barang
        $stmt = $conn->prepare("SELECT stok, namabarang FROM barang WHERE idbarang = ?");
        $stmt->bind_param('s', $idbarang);
        $stmt->execute();
        $stmt->bind_result($stok, $nama);
        $stmt->fetch();
        $stmt->close();

        if (!$nama) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan dalam mencari barang.'];
            header('Location: penjualan.php');
            exit();
        }

        if ($jumlah > $stok) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Stok barang yang dipilih tidak cukup.'];
            header('Location: penjualan.php');
            exit();
        }
        
        $stmt = $conn->prepare("UPDATE penjualan SET idbarang = ?, tanggal = ?, jumlah = ?, hargasatuan = ? WHERE idjual = ?");
        $stmt->bind_param("ssiis", $idbarang, $tanggal, $jumlah, $hargasatuan, $idjual);
        if ($stmt->execute()) {

        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan dalam mengubah data penjualan.'];
            header('Location: penjualan.php');
            exit();
        }

        $stokbaru = $stok - $jumlah;
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

    // Redirect kembali ke halaman penjualan setelah proses selesai
    header("Location: penjualan.php");
    exit();
} else {
    // Jika request bukan POST, redirect ke halaman penjualan
    header("Location: penjualan.php");
    exit();
}
?>
