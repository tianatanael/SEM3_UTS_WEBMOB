<?php
session_start();
require 'config.php';
require 'login_session.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $idusaha = $_POST['idusaha'];

    $stmt = $conn->prepare("DELETE FROM identitasusaha WHERE idusaha = ?");
    $stmt->bind_param("s", $idusaha);

    if($stmt->execute()){
        echo"Success: Data usaha berhasil dihapus.";
    }else{
        echo "Error: Terjadi kesalahan saat menghapus data.";
    }
    $stmt->close();
}else{
    echo"Error: Invalid request.";
}
?>