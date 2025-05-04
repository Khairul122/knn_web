<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

include_once 'model/SutmModel.php';

class SutmController
{
   private $model;

   public function __construct()
   {
       $this->model = new SutmModel();
   }

   public function index()
   {
       $filter_bulan = isset($_GET['bulan']) ? trim($_GET['bulan']) : '';
       $filter_tahun = isset($_GET['tahun']) ? trim($_GET['tahun']) : '';
       $filter = '';

       if ($filter_bulan !== '' && $filter_tahun !== '') {
           $filter = "$filter_bulan-$filter_tahun";
           $dataSutm = $this->model->getSutmByMonthYear($filter_bulan, $filter_tahun);
       } else {
           $dataSutm = $this->model->getAllSutm();
       }

       $bulanTahunList = $this->model->getAvailableMonths();
       include 'view/sutm/index.php';
   }

   public function importSutmSave()
   {
       include_once 'koneksi.php';
       global $koneksi;

       if (!isset($_POST['data']) || !is_array($_POST['data'])) {
           echo "<script>alert('Data tidak ditemukan.'); window.location.href = 'index.php?page=data-sutm';</script>";
           return;
       }

       $bulan = $_POST['bulan'] ?? '';
       $tahun = $_POST['tahun'] ?? '';
       $tanggal = ($bulan && $tahun) ? "$bulan-$tahun" : date('F-Y');

       $dataArray = $_POST['data'];
       $insertedCount = 0;
       $errorCount = 0;

       foreach ($dataArray as $row) {
           $nama_penyulang = mysqli_real_escape_string($koneksi, $row['nama_penyulang']);

           $fields = [
               't1_inspeksi','t1_realisasi','t2_inspeksi','t2_realisasi','pangkas_kms','pangkas_batang','tebang','row_lain','pin_isolator','suspension_isolator','traves_dan_armtie','tiang','accesoris_sutm','arrester_sutm','fco_sutm','grounding_sutm','perbaikan_andong_kendor','kawat_terburai','jamperan_sutm','skur','ganti_kabel_isolasi','pemasangan_cover_isolasi','pemasangan_penghalang_panjang','alat_ultrasonik'
           ];

           $values = [];
           foreach ($fields as $field) {
               $val = isset($row[$field]) ? str_replace(',', '.', $row[$field]) : 0;
               $val = filter_var($val, FILTER_VALIDATE_FLOAT);
               $values[] = $val === false ? 0 : $val;
           }

           $query = "INSERT INTO sutm (nama_penyulang, " . implode(", ", $fields) . ") 
                     VALUES ('$nama_penyulang', " . implode(", ", $values) . ")";

           if (mysqli_query($koneksi, $query)) {
               $sutm_id = mysqli_insert_id($koneksi);
               $insertedCount++;
               $tanggalDb = mysqli_real_escape_string($koneksi, $tanggal);
               $insertPemeliharaanQuery = "INSERT INTO data_pemeliharaan (tanggal, nama_objek, id_sub_kategori) 
                                           VALUES ('$tanggalDb', 'sutm', $sutm_id)";
               if (!mysqli_query($koneksi, $insertPemeliharaanQuery)) {
                   $errorCount++;
               }
           } else {
               $errorCount++;
           }
       }

       if ($errorCount > 0) {
           echo "<script>alert('Selesai: $insertedCount data berhasil, $errorCount gagal.'); window.location.href = 'index.php?page=data-sutm';</script>";
       } else {
           echo "<script>alert('Import berhasil: $insertedCount data disimpan.'); window.location.href = 'index.php?page=data-sutm';</script>";
       }
   }

   public function importExcel($file)
   {
       require_once 'vendor/autoload.php';
       include_once 'koneksi.php';

       global $koneksi;

       if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
           echo "<script>alert('Tidak ada file diunggah.'); window.location.href = 'index.php?page=data-sutm';</script>";
           return;
       }

       try {
           if (!$koneksi) {
               throw new Exception("Koneksi database tidak tersedia");
           }

           $fileName = $file['name'];
           $bulanTahun = extractMonthYearAsString($fileName);

           $reader = IOFactory::createReaderForFile($file['tmp_name']);
           $reader->setReadDataOnly(true);
           $reader->setLoadSheetsOnly("LSK");
           $spreadsheet = $reader->load($file['tmp_name']);
           $sheet = $spreadsheet->getActiveSheet();

           if (!$sheet) {
               echo "<script>alert('Sheet \"LSK\" tidak ditemukan.'); window.location.href = 'index.php?page=data-sutm';</script>";
               return;
           }

           $startRow = 9;
           $highestRow = $sheet->getHighestDataRow();

           $fieldMap = [
               't1_inspeksi','t1_realisasi','t2_inspeksi','t2_realisasi','pangkas_kms','pangkas_batang','tebang','row_lain','pin_isolator','suspension_isolator','traves_dan_armtie','tiang','accesoris_sutm','arrester_sutm','fco_sutm','grounding_sutm','perbaikan_andong_kendor','kawat_terburai','jamperan_sutm','skur','ganti_kabel_isolasi','pemasangan_cover_isolasi','pemasangan_penghalang_panjang','alat_ultrasonik'
           ];

           $insertedCount = 0;
           $errorCount = 0;

           for ($row = $startRow; $row <= $highestRow; $row++) {
               $nama_penyulang = $sheet->getCell("B$row")->getValue();
               if (empty($nama_penyulang)) continue;
               $nama_penyulang = trim(mysqli_real_escape_string($koneksi, $nama_penyulang));

               $values = [];

               for ($i = 0; $i < count($fieldMap); $i++) {
                   $colIndex = Coordinate::stringFromColumnIndex($i + 3);
                   $val = $sheet->getCell("$colIndex$row")->getValue();
                   $val = trim((string)$val);
                   $val = filter_var(str_replace(',', '.', $val), FILTER_VALIDATE_FLOAT);
                   $values[] = $val === false ? 0 : $val;
               }

               $query = "INSERT INTO sutm (nama_penyulang, " . implode(", ", $fieldMap) . ") 
                         VALUES ('$nama_penyulang', " . implode(", ", $values) . ")";

               if (mysqli_query($koneksi, $query)) {
                   $sutm_id = mysqli_insert_id($koneksi);
                   $insertedCount++;
                   $tanggalDb = formatTanggalKeSQL($bulanTahun);
                   $tanggalDb_escaped = mysqli_real_escape_string($koneksi, $tanggalDb);
                   $insertPemeliharaanQuery = "INSERT INTO data_pemeliharaan (tanggal, nama_objek, id_sub_kategori) 
                                               VALUES ('$tanggalDb_escaped', 'sutm', $sutm_id)";
                   if (!mysqli_query($koneksi, $insertPemeliharaanQuery)) {
                       $errorCount++;
                   }
               } else {
                   $errorCount++;
               }
           }

           if ($errorCount > 0) {
               echo "<script>alert('Import selesai dengan $insertedCount data berhasil dan $errorCount data gagal.'); window.location.href = 'index.php?page=data-sutm';</script>";
           } else {
               echo "<script>alert('Import berhasil! $insertedCount data telah diimpor.'); window.location.href = 'index.php?page=data-sutm';</script>";
           }
       } catch (\Exception $e) {
           echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.location.href = 'index.php?page=data-sutm';</script>";
       }
   }

   public function tambahData()
   {
       include 'view/sutm/tambah-data.php';
   }

   public function edit()
   {
       $id = $_GET['id'] ?? null;
       if (!$id) {
           echo "<script>alert('ID tidak valid'); window.location.href='index.php?page=data-sutm';</script>";
           return;
       }

       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           $data = $_POST;
           if ($this->model->updateSutm($id, $data)) {
               echo "<script>alert('Data berhasil diperbarui.'); window.location.href='index.php?page=data-sutm';</script>";
           } else {
               echo "<script>alert('Gagal memperbarui data.'); window.location.href='index.php?page=data-sutm';</script>";
           }
       } else {
           $data = $this->model->getSutmById($id);
           include 'view/sutm/edit-data.php';
       }
   }

   public function delete()
   {
       $id = $_GET['id'] ?? null;
       if (!$id) {
           echo "<script>alert('ID tidak valid'); window.location.href='index.php?page=data-sutm';</script>";
           return;
       }

       if ($this->model->deleteSutm($id)) {
           echo "<script>alert('Data berhasil dihapus.'); window.location.href='index.php?page=data-sutm';</script>";
       } else {
           echo "<script>alert('Gagal menghapus data.'); window.location.href='index.php?page=data-sutm';</script>";
       }
   }
   public function simpanManual()
   {
       include_once 'koneksi.php';
       global $koneksi;

       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           $nama_penyulang = mysqli_real_escape_string($koneksi, $_POST['nama_penyulang'] ?? '');
           $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal'] ?? '');

           $fields = [
               't1_inspeksi','t1_realisasi','t2_inspeksi','t2_realisasi','pangkas_kms','pangkas_batang','tebang','row_lain','pin_isolator','suspension_isolator','traves_dan_armtie','tiang','accesoris_sutm','arrester_sutm','fco_sutm','grounding_sutm','perbaikan_andong_kendor','kawat_terburai','jamperan_sutm','skur','ganti_kabel_isolasi','pemasangan_cover_isolasi','pemasangan_penghalang_panjang','alat_ultrasonik'
           ];

           $values = [];
           foreach ($fields as $field) {
               $val = isset($_POST[$field]) ? str_replace(',', '.', $_POST[$field]) : 0;
               $val = filter_var($val, FILTER_VALIDATE_FLOAT);
               $values[] = $val === false ? 0 : $val;
           }

           $query = "INSERT INTO sutm (nama_penyulang, " . implode(", ", $fields) . ") 
                     VALUES ('$nama_penyulang', " . implode(", ", $values) . ")";

           if (mysqli_query($koneksi, $query)) {
               $sutm_id = mysqli_insert_id($koneksi);
               $insertPemeliharaanQuery = "INSERT INTO data_pemeliharaan (tanggal, nama_objek, id_sub_kategori) 
                                           VALUES ('$tanggal', 'sutm', $sutm_id)";
               mysqli_query($koneksi, $insertPemeliharaanQuery);
               echo "<script>alert('Data berhasil ditambahkan.'); window.location.href = 'index.php?page=data-sutm';</script>";
           } else {
               echo "<script>alert('Gagal menyimpan data.'); window.location.href = 'index.php?page=tambah-sutm';</script>";
           }
       } else {
           echo "<script>alert('Metode tidak valid.'); window.location.href = 'index.php?page=data-sutm';</script>";
       }
   }
}

function extractMonthYearAsString($fileName)
{
   $result = "Januari " . date('Y');
   $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
   $year = date('Y');
   if (preg_match('/(\d{4})/', $fileName, $yearMatches)) {
       $year = $yearMatches[1];
   }
   $monthPattern = '/' . implode('|', $monthNames) . '/i';
   if (preg_match($monthPattern, $fileName, $monthMatches)) {
       $monthName = ucfirst(strtolower($monthMatches[0]));
       $result = $monthName . " " . $year;
   }
   return $result;
}

function formatTanggalKeSQL($tanggalString)
{
   $monthNames = [
       'Januari' => '01','Februari' => '02','Maret' => '03','April' => '04','Mei' => '05','Juni' => '06','Juli' => '07','Agustus' => '08','September' => '09','Oktober' => '10','November' => '11','Desember' => '12'
   ];
   if (preg_match('/(\w+)\s+(\d{4})/', $tanggalString, $matches)) {
       $bulanNama = ucfirst(strtolower($matches[1]));
       $bulanAngka = $monthNames[$bulanNama] ?? '01';
       $tahun = $matches[2];
       return "$tahun-$bulanAngka-01";
   }
   return date('Y-m-d');
}