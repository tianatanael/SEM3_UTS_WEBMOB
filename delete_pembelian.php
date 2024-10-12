<?php
session_start();
require 'config.php';
require 'login_session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idbeli = $_POST['idbeli'];

    $stmt = $conn->prepare("SELECT idbarang, jumlah FROM pembelian WHERE idbeli = ?");
    $stmt->bind_param("s", $idbeli);
    $stmt->execute();
    $stmt->bind_result($idbarang, $jumlah);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT stok FROM barang WHERE idbarang = ?");
    $stmt->bind_param("s", $idbarang);
    $stmt->execute();
    $stmt->bind_result($stok);
    $stmt->fetch();
    $stmt->close();

    if ($stok < $jumlah) {
        echo "Error: Stok lebih kecil dari jumlah.";
        header("pembelian.php");
        exit();
    }

    $stokbaru = $stok - $jumlah;
    $stmt = $conn->prepare("UPDATE barang SET stok = ? WHERE idbarang = ?");
    $stmt->bind_param("is", $stokbaru, $idbarang);    
    if (!$stmt->execute()) {
        echo "Error: Terjadi kesalahan saat mengubah stok.";
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM pembelian WHERE idbeli = ?");
    $stmt->bind_param("s", $idbeli);

    if ($stmt->execute()) {
        echo 'Success: Berhasil menghapus data.';
    } else {
        echo "Error: Terjadi kesalahan saat menghapus data.";
    }
    $stmt->close();
} else {
    echo "Error: Invalid request.";
}
?>
