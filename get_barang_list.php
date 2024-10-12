<?php
require 'config.php';

// Ambil data pegawai dari database
$pegawai = $conn->query("SELECT idbarang, namabarang FROM barang");

$options = '';
while ($row = $pegawai->fetch_assoc()) {
    $options .= "<option value='" . htmlspecialchars($row['idbarang']) . "'>" . htmlspecialchars($row['namabarang']) . "</option>";
}

echo $options;
?>
