<?php
session_start();
require 'config.php';
require 'login_session.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Retrieve POST data
    $idusaha = $_POST['idusaha'];
    $namausaha = $_POST['namausaha'];
    $alamat = $_POST['alamat'];
    $notelepon = $_POST['notelepon'];
    $fax = $_POST['fax'];
    $email = $_POST['email'];
    $npwp = $_POST['npwp'];
    $bank = $_POST['bank'];
    $noaccount = $_POST['noaccount'];
    $atasnama = $_POST['atasnama'];
    $pimpinan = $_POST['pimpinan'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO identitasusaha (idusaha ,namausaha , alamat, notelepon, fax, email, npwp, bank, noaccount, atasnama, pimpinan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("sssssssssss", $idusaha, $namausaha, $alamat, $notelepon, $fax, $email, $npwp, $bank, $noaccount, $atasnama, $pimpinan);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Data usaha berhasil ditambahkan.'];
        header('Location: identitasusaha.php');
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan saat menambahkan data.'];
    }

    // Close the statement
    $stmt->close();
    $conn->close();
    } else {
    header('Location: identitasusaha.php');
}
?>