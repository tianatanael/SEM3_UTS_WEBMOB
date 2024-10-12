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
$idbeli = isset($_GET['id']) ? $_GET['id'] : '';

// Ambil data dari tabel cuti dan pegawai
$stmt2 = $conn->prepare("SELECT p.idbeli, p.idbarang, p.tanggal, p.jumlah, p.hargasatuan, b.namabarang, b.merk FROM pembelian p JOIN barang b ON p.idbarang = b.idbarang WHERE p.idbeli = ? LIMIT 1");
$stmt2->bind_param('s', $idbeli);
$stmt2->execute();
$stmt2->bind_result($idbeli, $idbarang, $tanggal, $jumlah, $hargasatuan, $namabarang, $merk);
$stmt2->fetch();
$stmt2->close();

$total = $hargasatuan * $jumlah;

// Buat PDF
$pdf = new FPDF();
$pdf->AddPage();

// Tambahkan logo di sisi kiri dan nama perusahaan serta alamat di sisi kanan
$logoFile = 'logo/logo.png'; // Path ke file logo
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
$pdf->Cell(0, 10, 'Data Pembelian', 0, 1, 'C');
$pdf->SetDrawColor(0, 0, 0); // Warna hitam
$pdf->Line(80, $pdf->GetY(), 130, $pdf->GetY()); // Garis bawah judul Surat Cuti
$pdf->Ln(2);

// Nomor surat dengan id_cuti
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 10, 'Nomor : ' . $idbeli, 0, 1, 'C');
$pdf->Ln(1);

// Isi surat
// $pdf->SetFont('Arial', 'B', 11);
// $pdf->Cell(0, 10, 'Surat cuti ini diajukan oleh :', 0, 1, 'L');
// $pdf->Ln(1);

// NIK dan Nama Pegawai
$pdf->SetFont('Arial', 'B', 11);
// Set lebar kolom untuk label dan nilai
$labelWidth = 40; // Lebar untuk label seperti 'NIK' dan 'Nama'
$tabWidth = 10;
$valueWidth = 80; // Lebar untuk nilai seperti ID Pegawai dan Nama Pegawai


// Dari & Sampai
$pdf->Cell($labelWidth, 10, 'Tanggal Pembelian', 0, 0, 'L'); // Kolom label Tgl/Waktu
$pdf->Cell($tabWidth, 10, ':', 0, 0, 'L'); 
$pdf->Cell($valueWidth, 10, $tanggal, 0, 1, 'L'); // Kolom nilai Tgl/Waktu

// NIK
$pdf->Cell($labelWidth, 10, 'ID Barang', 0, 0, 'L'); // Kolom label NIK
$pdf->Cell($tabWidth, 10, ':', 0, 0, 'L'); 
$pdf->Cell($valueWidth, 10, $idbarang, 0, 1, 'L'); // Kolom nilai NIK

// Nama
$pdf->Cell($labelWidth, 10, 'Nama Barang', 0, 0, 'L'); // Kolom label Nama
$pdf->Cell($tabWidth, 10, ':', 0, 0, 'L'); 
$pdf->Cell($valueWidth, 10, $namabarang, 0, 1, 'L'); // Kolom nilai Nama

// Nama
$pdf->Cell($labelWidth, 10, 'Merk Barang', 0, 0, 'L'); // Kolom label Nama
$pdf->Cell($tabWidth, 10, ':', 0, 0, 'L'); 
$pdf->Cell($valueWidth, 10, $merk, 0, 1, 'L'); // Kolom nilai Nama

// Nama
$pdf->Cell($labelWidth, 10, 'Harga Satuan', 0, 0, 'L'); // Kolom label Nama
$pdf->Cell($tabWidth, 10, ':', 0, 0, 'L'); 
$pdf->Cell($valueWidth, 10, "Rp. $hargasatuan", 0, 1, 'L'); // Kolom nilai Nama

// Nama
$pdf->Cell($labelWidth, 10, 'Jumlah', 0, 0, 'L'); // Kolom label Nama
$pdf->Cell($tabWidth, 10, ':', 0, 0, 'L'); 
$pdf->Cell($valueWidth, 10, $jumlah, 0, 1, 'L'); // Kolom nilai Nama

// Nama
$pdf->Cell($labelWidth, 10, 'Total Harga', 0, 0, 'L'); // Kolom label Nama
$pdf->Cell($tabWidth, 10, ':', 0, 0, 'L'); 
$pdf->Cell($valueWidth, 10, "Rp. $total", 0, 1, 'L'); // Kolom nilai Nama


// Hormat kami, pembuat surat, dan posisinya di-center dan rata kanan
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(310, 10, 'Diterbitkan oleh,', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(310, 10, "", 0, 1, 'C');
$pdf->SetDrawColor(0, 0, 0); // Warna hitam
$pdf->Line(130, $pdf->GetY(), 200, $pdf->GetY()); // Garis bawah 
$pdf->Ln(1);

// HRD dan nama perusahaan
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(310, 10, 'HRD '. $namaUsaha, 0, 1, 'C');

// Output PDF
$pdf->Output('I', 'data_pembelian.pdf');
?>
