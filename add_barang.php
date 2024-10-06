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

    // File upload handling
    $uploadDir = 'foto_barang/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    // Get the uploaded file details
    $foto_barang = $_FILES['foto_barang'];
    $fileName = $foto_barang['name'];
    $fileTmp = $foto_barang['tmp_name'];
    $fileSize = $foto_barang['size'];
    $fileType = mime_content_type($fileTmp);

    // Check if the file is an image and within size limits (2MB in this example)
    if (in_array($fileType, $allowedTypes) && $fileSize <= 2 * 1024 * 1024) {
        // Create the destination path for the file (save with idpeg as the filename)
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); // Get the file extension (jpg, png)
        $newFileName = $idbarang . '.' . $fileExtension;
        $destination = $uploadDir . $newFileName;

        // Move the uploaded file to the destination folder
        if (move_uploaded_file($fileTmp, $destination)) {
            // Prepare the SQL statement (add profile picture field if necessary)
            $stmt = $conn->prepare("INSERT INTO barang (idbarang, namabarang, merk, deskripsi, stok, harga, foto)
            VALUES (?, ?, ?, ?, ?, ?, ?)");

            // Bind parameters (note: added 'foto' field)
            $stmt->bind_param("ssssiis", $idbarang, $namabarang, $merk, $deskripsi, $stok, $harga, $newFileName);

            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Data barang dan foto berhasil ditambahkan.'];
                header('Location: stok_barang.php');
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan saat menambahkan data barang.'];
            }

            // Close the statement
            $stmt->close();
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal mengupload foto barang.'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'File yang diupload harus berupa gambar JPG/PNG dan maksimal 2MB.'];
    }

    // Close the database connection
    $conn->close();
} else {
    header('Location: stok_barang.php');
}
?>