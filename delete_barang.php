<?php
session_start();
require 'config.php';
require 'login_session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idbarang = $_POST['idbarang'];

    // Prepare a statement to get the filename of the photo
    $stmt = $conn->prepare("SELECT foto FROM barang WHERE idbarang = ?");
    $stmt->bind_param("s", $idbarang);
    $stmt->execute();
    $stmt->bind_result($foto);
    $stmt->fetch();
    $stmt->close();

    // Path to the photo
    $fotoPath = 'foto_barang/' . $foto;

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM barang WHERE idbarang = ?");
    $stmt->bind_param("s", $idbarang);

    if ($stmt->execute()) {
        // Check if the file exists and delete it
        if (file_exists($fotoPath)) {
            unlink($fotoPath); // Deletes the photo file
        }
        echo "Success: Data pegawai berhasil dihapus dan foto juga telah dihapus.";
        header('Location: stok_barang.php');
    } else {
        echo "Error: Terjadi kesalahan saat menghapus data.";
    }
    $stmt->close();
} else {
    echo "Error: Invalid request.";
}
?>
