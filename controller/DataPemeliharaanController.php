<?php
include __DIR__ . '/../koneksi.php';

function getDataPemeliharaan()
{
  global $koneksi;
  $query = "SELECT * FROM data_pemeliharaan ORDER BY id_data_pemeliharaan DESC";
  $result = mysqli_query($koneksi, $query);
  $data = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
  }
  return $data;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
  $id = $_GET['id'];
  $delete = mysqli_query($koneksi, "DELETE FROM data_pemeliharaan WHERE id_data_pemeliharaan = '$id'");
  if ($delete) {
    header("Location: ../index.php?page=data-pemeliharaan&status=success");
  } else {
    echo "Query error: " . mysqli_error($koneksi);
    exit;
  }
  exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = mysqli_query($koneksi, "DELETE FROM data_pemeliharaan WHERE id_data_pemeliharaan = '$id'");
    if ($delete) {
        header("Location: ../index.php?page=data-pemeliharaan&status=success");
    } else {
        header("Location: ../index.php?page=data-pemeliharaan&status=error");
    }
    exit;
}

if (isset($_POST['tambah'])) {
    $nama_penyulang = $_POST['nama_penyulang'];
    $kategori = $_POST['kategori'];
    $pengukuran = $_POST['pengukuran'];
    $arrester = $_POST['arrester'];
    $grounding = $_POST['grounding'];
    $ultrasonic = $_POST['ultrasonic'];
    $cover_isolasi = $_POST['cover_isolasi'];
    $pangkas = $_POST['pangkas'];
    $tebang = $_POST['tebang'];
    $row_lain_lain = $_POST['row_lain_lain'];
    $jamperan = $_POST['jamperan'];
    $kawat_terburai = $_POST['kawat_terburai'];
    $gangguan = $_POST['gangguan'];
    $set_label = $_POST['set_label'];

    $query = "INSERT INTO data_pemeliharaan (
    nama_penyulang, kategori, pengukuran, arrester, grounding, ultrasonic,
    cover_isolasi, pangkas, tebang, row_lain_lain, jamperan, kawat_terburai,
    gangguan, set_label
  ) VALUES (
    '$nama_penyulang', '$kategori', '$pengukuran', '$arrester', '$grounding', '$ultrasonic',
    '$cover_isolasi', '$pangkas', '$tebang', '$row_lain_lain', '$jamperan', '$kawat_terburai',
    '$gangguan', '$set_label'
  )";

    $insert = mysqli_query($koneksi, $query);
    if ($insert) {
        header("Location: ../index.php?page=data-pemeliharaan&status=success");
    } else {
        die("Query error: " . mysqli_error($koneksi));
    }
    exit;
}

if (isset($_POST['update'])) {
    $id = $_POST['id_data_pemeliharaan'];
    $nama_penyulang = $_POST['nama_penyulang'];
    $kategori = $_POST['kategori'];
    $pengukuran = $_POST['pengukuran'];
    $arrester = $_POST['arrester'];
    $grounding = $_POST['grounding'];
    $ultrasonic = $_POST['ultrasonic'];
    $cover_isolasi = $_POST['cover_isolasi'];
    $pangkas = $_POST['pangkas'];
    $tebang = $_POST['tebang'];
    $row_lain_lain = $_POST['row_lain_lain'];
    $jamperan = $_POST['jamperan'];
    $kawat_terburai = $_POST['kawat_terburai'];
    $gangguan = $_POST['gangguan'];
    $set_label = $_POST['set_label'];
  
    $query = "UPDATE data_pemeliharaan SET
      nama_penyulang = '$nama_penyulang',
      kategori = '$kategori',
      pengukuran = '$pengukuran',
      arrester = '$arrester',
      grounding = '$grounding',
      ultrasonic = '$ultrasonic',
      cover_isolasi = '$cover_isolasi',
      pangkas = '$pangkas',
      tebang = '$tebang',
      row_lain_lain = '$row_lain_lain',
      jamperan = '$jamperan',
      kawat_terburai = '$kawat_terburai',
      gangguan = '$gangguan',
      set_label = '$set_label'
      WHERE id_data_pemeliharaan = '$id'";
  
    $update = mysqli_query($koneksi, $query);
    if ($update) {
      header("Location: ../index.php?page=data-pemeliharaan&status=success");
    } else {
      header("Location: ../index.php?page=data-pemeliharaan&status=error");
    }
    exit;
  }
  
