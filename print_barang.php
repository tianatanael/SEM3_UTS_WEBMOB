<?php
session_start();
require 'config.php';
require 'login_session.php';
require 'fpdf/fpdf.php';

//Ambil data nama usaha dan alamat dari database
$stmt = $conn->prepare("SELECT namausaha, alamat, notelepon FROM identitasusaha LIMIT 1");
$stmt->execute();
$stmt->bind_result($namaUsaha, $alamatUsaha, $noTelepon);
$stmt->fetch();
$stmt->close();

// Ambil data dari GET
$idbarang = isset($_GET['id']) ? $_GET['id'] : '';

// Ambil data dari tabel barang berdasarkan id
$stmt1 = $conn->prepare("SELECT * FROM barang WHERE idbarang = ?");
$stmt1->bind_param('s', $idbarang); // Bind the parameter
$stmt1->execute();

$result = $stmt1->get_result();
$row1 = $result->fetch_assoc(); // Fetch the result as associative array
$stmt1->close();

//Buat PDF
$pdf = new FPDF();
$pdf -> AddPage('P', 'A4');

// Tambahkan logo di sisi kiri dan nama perusahaan serta alamat di sisi kanan
$logoFile = 'logo/logo.jpg'; // Path ke file logo
$logoWidth = 30; // Lebar logo
$logoHeight = 30; // Tinggi logo

// Logo
$pdf->Image($logoFile, 10, 10, $logoWidth, $logoHeight);

// Nama Perusahaan dan Alamat
$pdf->SetXY(10, 10); // Set posisi X dan Y setelah logo
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, $namaUsaha, 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, $alamatUsaha, 0, 1, 'C');
$pdf->Cell(0, 10, 'Telepon: '.$noTelepon, 0, 1, 'C');

// Garis pembatas di bawah alamat
$pdf->Ln(1);
$pdf->SetDrawColor(0, 0, 0); // Warna hitam
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Garis dari kiri ke kanan
$pdf->Ln(0.8);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Garis dari kiri ke kanan
$pdf->Ln(5);

// Tambahkan jenis surat dengan garis bawah
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'Data Barang', 0, 1, 'C');
$pdf->SetDrawColor(0, 0, 0); // Warna hitam
$pdf->Line(80, $pdf->GetY(), 130, $pdf->GetY()); // Garis bawah judul Data Pegawai
$pdf->Cell(0, 10, 'Id: '. $row1['idbarang'], 0, 1, 'C');
$pdf->Ln(3);

// NIK dan Nama Pegawai
$pdf->SetFont('Arial', 'B', 11);
// Set lebar kolom untuk label dan nilai
$labelWidth = 25; // Lebar untuk label seperti 'ID' dan 'Nama'
$valueWidth = 30; // Lebar untuk nilai seperti ID Pegawai dan Nama Pegawai

// idbarang
$pdf->Cell($labelWidth, 10, 'Id barang', 0, 0, 'L'); // Kolom label idbarang
$pdf->Cell($labelWidth, 10, ': '. $row1['idbarang'], 0, 1, 'L'); 
$pdf->Ln(2);

// Nama
$pdf->Cell($labelWidth, 10, 'Nama', 0, 0, 'L'); // Kolom label Nama
$pdf->Cell($labelWidth, 10, ': '. $row1['namabarang'], 0, 1, 'L'); 
$pdf->Ln(2);

// Foto
$pdf->Cell($labelWidth, 10, 'Foto', 0, 0, 'L'); // Kolom label foto
$pdf->Cell($labelWidth, 10, ':', 0, 0, 'L'); 
$pdf->Cell($valueWidth, 10, "", 0, 1, 'L'); // Kolom nilai Nama
$pdf->Ln(2);

$photoPath = 'foto_barang/' . $row1['foto']; // Make sure the path is correct
// Check if the file exists and display the image
if (file_exists($photoPath) && !empty($row1['foto'])) {
    // Add the photo to the PDF (with X, Y, width, height)
    $pdf->Image($photoPath, $pdf->GetX()+29, $pdf->GetY()-10, 30, 30); // Adjust the width and height as needed
    $pdf->Ln(22); // Move the cursor below the image after it's placed
} else {
    // If no image exists, display a placeholder text or blank cell
    $pdf->Cell(30, 30, 'No photo available', 1, 1, 'L'); // Placeholder text
}
$pdf->Ln(2); // Add space after the image or placeholder

// namabarang
$pdf->Cell($labelWidth, 10, 'Nama barang', 0, 0, 'L'); // Kolom label namabarang
$pdf->Cell($labelWidth, 10, ': '. $row1['namabarang'], 0, 1, 'L'); 
$pdf->Ln(2);

// merk
$pdf->Cell($labelWidth, 10, 'Merk', 0, 0, 'L'); // Kolom label merk
$pdf->Cell($labelWidth, 10, ': '. $row1['merk'], 0, 1, 'L'); 
$pdf->Ln(2);

// deskripsi
$pdf->Cell($labelWidth, 10, 'Deskripsi', 0, 0, 'L'); // Kolom label deskripsi
$pdf->Cell($labelWidth, 10, ': '. $row1['deskripsi'], 0, 1, 'L'); 
$pdf->Ln(2);

// stok
$pdf->Cell($labelWidth, 10, 'Stok', 0, 0, 'L'); // Kolom label stok
$pdf->Cell($labelWidth, 10, ': '. $row1['stok'], 0, 1, 'L'); 
$pdf->Ln(2);

// harga
$pdf->Cell($labelWidth, 10, 'Harga:', 0, 0, 'L'); // Kolom label
$pdf->Cell($valueWidth, 10, ': Rp ' . number_format($row1['harga'], 0, ',', '.'), 0, 1, 'L'); // Format harga ke Rupiah
$pdf->Ln(2);

//Output PDF
$pdf -> Output('I', "Daftar_usaha.pdf");
?>