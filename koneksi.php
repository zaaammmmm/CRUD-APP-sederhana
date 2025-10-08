<?php
$server = "localhost";
$user = "root"; // Sesuaikan dengan username phpMyAdmin Anda
$password = ""; // Sesuaikan dengan password phpMyAdmin Anda
$database = "db_crud_mahasiswa";

// Buat koneksi
$koneksi = mysqli_connect($server, $user, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>