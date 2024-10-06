<?php
session_start();
require 'config.php';
require 'login_session.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Retrieve POST data
    $idbarang = $_POST['idbarang'];
    $namabarang = $_POST['namabarang'];
    $merk = $_POST['merk'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    // Initialize variable to hold the new photo name
    $newPhotoName = null;

    // File upload handling
    if (isset($_FILES['foto_barang']) && $_FILES['foto_barang']['error'] == UPLOAD_ERR_OK) {
        // Define the upload directory and allowed file types
        $uploadDir = 'foto_barang/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileName = $_FILES['foto_barang']['name'];
        $fileTmp = $_FILES['foto_barang']['tmp_name'];
        $fileSize = $_FILES['foto_barang']['size'];
        $fileType = mime_content_type($fileTmp);

        // Check if the file is valid
        if (in_array($fileType, $allowedTypes) && $fileSize <= 2 * 1024 * 1024) {
            // Create a unique filename using idpeg
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newPhotoName = $idbarang . '.' . $fileExtension;
            $destination = $uploadDir . $newPhotoName;

            // Move the uploaded file to the destination folder
            if (!move_uploaded_file($fileTmp, $destination)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal mengupload foto barang.'];
                header('Location: stok_barang.php');
                exit();
            }
        }else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'File yang diupload harus berupa gambar JPG/PNG/JPEG/JFIF dan maksimal 2MB.'];
            header('Location: stok_barang.php');
            exit();
        }
    }

    // Prepare the SQL update statement
    if ($newPhotoName) {
        // If there's a new photo, update it in the database
        $stmt = $conn->prepare("UPDATE barang
        SET namabarang=?, merk=?, deskripsi=?, stok=?, harga=?, foto=? WHERE idbarang=?");
        $stmt->bind_param('sssiiss', $namabarang, $merk, $deskripsi, $stok, $harga, $newPhotoName, $idbarang);
    } else {
        $stmt = $conn->prepare("UPDATE barang
        SET namabarang=?, merk=?, deskripsi=?, stok=?, harga=? WHERE idbarang=?");
        $stmt->bind_param('sssiis', $namabarang, $merk, $deskripsi, $stok, $harga, $idbarang);
    }

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Data barang berhasil diperbarui.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan saat memperbarui data.'];
    }

    $stmt->close();
    header('Location: stok_barang.php');
    exit();
    }else{
    header("Location: stok_barang.php");
    exit();
}
?>