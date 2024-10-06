<?php  
if (!isset($_SESSION['staffid'])) {
    header("Location: login.php");
    exit();
}
?>