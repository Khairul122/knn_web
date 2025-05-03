<?php
include_once 'model/GarduModel.php';
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GarduController
{
    private $model;

    public function __construct()
    {
        $this->model = new GarduModel();
    }

    public function index()
    {
        $filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
        $filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

        if (!empty($filter_bulan) && !empty($filter_tahun)) {
            $dataGardu = $this->model->getGarduByMonthYear($filter_bulan, $filter_tahun);
        } else {
            $dataGardu = $this->model->getAllGardu();
        }

        $bulanTahunList = $this->model->getAvailableMonths();
        include 'view/gardu/index.php';
    }

    public function importExcel($file)
    {
        require_once 'vendor/autoload.php';
        include_once 'koneksi.php';
        use PhpOffice\PhpSpreadsheet\IOFactory;
    
        global $koneksi;
    
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            echo "<script>alert('Tidak ada file diunggah.'); window.location.href = 'index.php?page=data-gardu';</script>";
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
                echo "<script>alert('Sheet \"LSK\" tidak ditemukan.'); window.location.href = 'index.php?page=data-gardu';</script>";
                return;
            }
    
            $startRow = 9;
            $highestRow = $sheet->getHighestDataRow();
    
            $fieldMap = [
                't1_inspeksi', 't1_realisasi', 't2_inspeksi', 't2_realisasi',
                'pengukuran', 'pergantian_arrester', 'pergantian_fco', 'relokasi_gardu',
                'pembangunan_gardu_siapan', 'penyimbang_beban_gardu', 'pemecahan_beban_gardu',
                'perubahan_tap_charger_trafo', 'pergantian_box', 'pergantian_opstic',
                'perbaikan_grounding', 'accesoris_gardu', 'pergantian_kabel_isolasi',
                'pemasangan_cover_isolasi', 'pemasangan_penghalang_panjat', 'alat_ultrasonik'
            ];
    
            $insertedCount = 0;
            $errorCount = 0;
    
            for ($row = $startRow; $row <= $highestRow; $row++) {
                $nama_penyulang = $sheet->getCell("B$row")->getValue();
                if (empty($nama_penyulang)) continue;
                $nama_penyulang = trim(mysqli_real_escape_string($koneksi, $nama_penyulang));
    
                $values = [];
    
                for ($i = 0; $i < count($fieldMap); $i++) {
                    $colIndex = chr(67 + $i); // 67 = 'C', 68 = 'D', ... sampai 'V'
                    $val = $sheet->getCell("$colIndex$row")->getValue();
                    $val = trim((string)$val);
                    if ($val === '-' || $val === '' || is_null($val)) {
                        $val = 0;
                    } else {
                        $val = (float)str_replace(',', '.', $val);
                    }
                    $values[] = $val;
                }
    
                $query = "INSERT INTO gardu (nama_penyulang, " . implode(", ", $fieldMap) . ") 
                          VALUES ('$nama_penyulang', " . implode(", ", $values) . ")";
    
                if (mysqli_query($koneksi, $query)) {
                    $gardu_id = mysqli_insert_id($koneksi);
                    $insertedCount++;
                    $tanggalDb = formatTanggalKeSQL($bulanTahun);
                    $tanggalDb_escaped = mysqli_real_escape_string($koneksi, $tanggalDb);
                    $insertPemeliharaanQuery = "INSERT INTO data_pemeliharaan (tanggal, nama_objek, id_sub_kategori) 
                                                VALUES ('$tanggalDb_escaped', 'gardu', $gardu_id)";
                    if (!mysqli_query($koneksi, $insertPemeliharaanQuery)) {
                        $errorCount++;
                    }
                } else {
                    $errorCount++;
                }
            }
    
            if ($errorCount > 0) {
                echo "<script>alert('Import selesai dengan $insertedCount data berhasil dan $errorCount data gagal.'); window.location.href = 'index.php?page=data-gardu';</script>";
            } else {
                echo "<script>alert('Import berhasil! $insertedCount data telah diimpor.'); window.location.href = 'index.php?page=data-gardu';</script>";
            }
    
        } catch (\Exception $e) {
            echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.location.href = 'index.php?page=data-gardu';</script>";
        }
    }
    

    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($id) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = [
                    'nama_penyulang' => $_POST['nama_penyulang'],
                    't1_inspeksi' => (float)$_POST['t1_inspeksi'],
                    't1_realisasi' => (float)$_POST['t1_realisasi'],
                    't2_inspeksi' => (float)$_POST['t2_inspeksi'],
                    't2_realisasi' => (float)$_POST['t2_realisasi'],
                    'pengukuran' => (float)$_POST['pengukuran'],
                    'pergantian_arrester' => (float)$_POST['pergantian_arrester'],
                    'pergantian_fco' => (float)$_POST['pergantian_fco'],
                    'relokasi_gardu' => (float)$_POST['relokasi_gardu'],
                    'pembangunan_gardu_siapan' => (float)$_POST['pembangunan_gardu_siapan'],
                    'penyimbang_beban_gardu' => (float)$_POST['penyimbang_beban_gardu'],
                    'pemecahan_beban_gardu' => (float)$_POST['pemecahan_beban_gardu'],
                    'perubahan_tap_charger_trafo' => (float)$_POST['perubahan_tap_charger_trafo'],
                    'pergantian_box' => (float)$_POST['pergantian_box'],
                    'pergantian_opstic' => (float)$_POST['pergantian_opstic'],
                    'perbaikan_grounding' => (float)$_POST['perbaikan_grounding'],
                    'accesoris_gardu' => (float)$_POST['accesoris_gardu'],
                    'pergantian_kabel_isolasi' => (float)$_POST['pergantian_kabel_isolasi'],
                    'pemasangan_cover_isolasi' => (float)$_POST['pemasangan_cover_isolasi'],
                    'pemasangan_penghalang_panjat' => (float)$_POST['pemasangan_penghalang_panjat'],
                    'alat_ultrasonik' => (float)$_POST['alat_ultrasonik'],
                    'tanggal' => $_POST['tanggal']
                ];

                if ($this->model->updateGardu($id, $data)) {
                    echo "<script>alert('Data berhasil diperbarui.'); window.location.href = 'index.php?page=data-gardu';</script>";
                } else {
                    echo "<script>alert('Gagal memperbarui data.'); window.location.href = 'index.php?page=data-gardu';</script>";
                }
            } else {
                $gardu = $this->model->getGarduById($id);
                if ($gardu) {
                    include 'view/gardu/edit.php';
                } else {
                    echo "<script>alert('Data tidak ditemukan.'); window.location.href = 'index.php?page=data-gardu';</script>";
                }
            }
        } else {
            echo "<script>alert('ID tidak valid.'); window.location.href = 'index.php?page=data-gardu';</script>";
        }
    }

    public function delete()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($id) {
            if ($this->model->deleteGardu($id)) {
                echo "<script>alert('Data berhasil dihapus.'); window.location.href = 'index.php?page=data-gardu';</script>";
            } else {
                echo "<script>alert('Gagal menghapus data.'); window.location.href = 'index.php?page=data-gardu';</script>";
            }
        } else {
            echo "<script>alert('ID tidak valid.'); window.location.href = 'index.php?page=data-gardu';</script>";
        }
    }
}

function extractMonthYearAsString($fileName)
{
    $result = "Januari " . date('Y');
    $monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
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
        'Januari' => '01','Februari' => '02','Maret' => '03','April' => '04','Mei' => '05','Juni' => '06',
        'Juli' => '07','Agustus' => '08','September' => '09','Oktober' => '10','November' => '11','Desember' => '12'
    ];
    if (preg_match('/(\w+)\s+(\d{4})/', $tanggalString, $matches)) {
        $bulanNama = ucfirst(strtolower($matches[1]));
        $bulanAngka = $monthNames[$bulanNama] ?? '01';
        $tahun = $matches[2];
        return "$tahun-$bulanAngka-01";
    }
    return date('Y-m-d');
}
