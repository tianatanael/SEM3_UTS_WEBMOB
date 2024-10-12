<?php
session_start();
require 'config.php';
require 'login_session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idjual = $_POST['idjual'];

    $stmt = $conn->prepare("SELECT idbarang, jumlah FROM penjualan WHERE idjual = ?");
    $stmt->bind_param("s", $idjual);
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

    $stokbaru = $stok + $jumlah;
    $stmt = $conn->prepare("UPDATE barang SET stok = ? WHERE idbarang = ?");
    $stmt->bind_param("is", $stokbaru, $idbarang);    
    if (!$stmt->execute()) {
        echo "Error: Terjadi kesalahan saat mengubah stok.";
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM penjualan WHERE idjual = ?");
    $stmt->bind_param("s", $idjual);

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
