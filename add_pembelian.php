<?php
// Start session dan sambungkan ke database
session_start();
require 'config.php';
require 'login_session.php';

// Cek apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $idbeli = $_POST['idbeli'];
    $idbarang = $_POST['idbarang'];
    $tanggal = $_POST['tanggal'];    
    $jumlah = $_POST['jumlah'];
    $hargasatuan = $_POST['hargasatuan'];
    
    // Validasi data input
    if (empty($idbarang) || empty($tanggal) || empty($jumlah) || empty($hargasatuan) || empty($idbeli)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Semua field harus diisi.'];
        header('Location: pembelian.php');
        exit();
    }

    // Cek apakah idbarang ada di tabel barang
    $stmt = $conn->prepare("SELECT namabarang, stok FROM barang WHERE idbarang = ?");
    $stmt->bind_param('s', $idbarang);
    $stmt->execute();
    $stmt->bind_result($namabarang, $stok);
    $stmt->fetch();
    $stmt->close();

    if (!$namabarang) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'ID Barang tidak ditemukan.'];
        header('Location: pembelian.php');
        exit();
    }

    // Insert data ke database
    $stmt = $conn->prepare("INSERT INTO pembelian (idbeli, idbarang, tanggal, jumlah, hargasatuan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssii', $idbeli, $idbarang, $tanggal, $jumlah, $hargasatuan);

    // Eksekusi query dan cek apakah berhasil
    if (!$stmt->execute()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan ketika menambah data pembelian.'];
        header('Location: pembelian.php');
        exit();
    }
    $stmt->close();

    
    $stokbaru = $stok + $jumlah;
    $stmt = $conn->prepare("UPDATE barang SET stok = ? WHERE idbarang = ?");
    $stmt->bind_param("is", $stokbaru, $idbarang);
    // Eksekusi query dan cek apakah berhasil
    if ($stmt->execute()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Pembelian berhasil ditambahkan.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan ketika mengubah stok.'];
    }

    $stmt->close();
    $conn->close();

    // Redirect kembali ke halaman pembelian
    header('Location: pembelian.php');
    exit();
} else {
    // Jika bukan melalui POST, kembalikan ke halaman utama
    header('Location: pembelian.php');
    exit();
}
