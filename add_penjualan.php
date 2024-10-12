<?php
// Start session dan sambungkan ke database
session_start();
require 'config.php';
require 'login_session.php';

// Cek apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $idjual = $_POST['idjual'];
    $idbarang = $_POST['idbarang'];
    $tanggal = $_POST['tanggal'];    
    $jumlah = $_POST['jumlah'];
    
    // Validasi data input
    if (empty($idbarang) || empty($tanggal) || empty($jumlah) || empty($idjual)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Semua field harus diisi.'];
        header('Location: penjualan.php');
        exit();
    }

    // Cek apakah idbarang ada di tabel barang
    $stmt = $conn->prepare("SELECT namabarang, stok, harga FROM barang WHERE idbarang = ?");
    $stmt->bind_param('s', $idbarang);
    $stmt->execute();
    $stmt->bind_result($namabarang, $stok, $harga);
    $stmt->fetch();
    $stmt->close();

    if (!$namabarang) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'ID Barang tidak ditemukan.'];
        header('Location: penjualan.php');
        exit();
    }

    if ($jumlah > $stok) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Stok barang yang dipilih tidak cukup.'];
        header('Location: penjualan.php');
        exit();
    }

    // Insert data ke database
    $stmt = $conn->prepare("INSERT INTO penjualan (idjual, idbarang, tanggal, jumlah, hargasatuan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssii', $idjual, $idbarang, $tanggal, $jumlah, $harga);

    // Eksekusi query dan cek apakah berhasil
    if (!$stmt->execute()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan ketika menambah data penjualan.'];
        header('Location: penjualan.php');
        exit();
    }
    $stmt->close();
    
    $stokbaru = $stok - $jumlah;
    $stmt = $conn->prepare("UPDATE barang SET stok = ? WHERE idbarang = ?");
    $stmt->bind_param("is", $stokbaru, $idbarang);
    // Eksekusi query dan cek apakah berhasil
    if ($stmt->execute()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Penjualan berhasil ditambahkan.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan ketika mengubah stok.'];
    }

    $stmt->close();
    $conn->close();

    // Redirect kembali ke halaman penjualan
    header('Location: penjualan.php');
    exit();
} else {
    // Jika bukan melalui POST, kembalikan ke halaman utama
    header('Location: penjualan.php');
    exit();
}
