<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

include_once 'model/GarduModel.php';

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

    public function importGarduSave()
    {
        include_once 'koneksi.php';
        global $koneksi;

        if (!isset($_POST['data']) || !is_array($_POST['data'])) {
            echo "<script>alert('Data tidak ditemukan.'); window.location.href = 'index.php?page=data-gardu';</script>";
            return;
        }

        $dataArray = $_POST['data'];
        $insertedCount = 0;
        $errorCount = 0;

        foreach ($dataArray as $row) {
            $nama_penyulang = mysqli_real_escape_string($koneksi, $row['nama_penyulang']);

            $fields = [
                't1_inspeksi', 't1_realisasi', 't2_inspeksi', 't2_realisasi',
                'pengukuran', 'pergantian_arrester', 'pergantian_fco', 'relokasi_gardu',
                'pembangunan_gardu_siapan', 'penyimbang_beban_gardu', 'pemecahan_beban_gardu',
                'perubahan_tap_charger_trafo', 'pergantian_box', 'pergantian_opstic',
                'perbaikan_grounding', 'accesoris_gardu', 'pergantian_kabel_isolasi',
                'pemasangan_cover_isolasi', 'pemasangan_penghalang_panjat', 'alat_ultrasonik'
            ];

            $values = [];
            foreach ($fields as $field) {
                $val = isset($row[$field]) ? str_replace(',', '.', $row[$field]) : 0;
                $val = filter_var($val, FILTER_VALIDATE_FLOAT);
                $values[] = $val === false ? 0 : $val;
            }

            $query = "INSERT INTO gardu (nama_penyulang, " . implode(", ", $fields) . ") 
                      VALUES ('$nama_penyulang', " . implode(", ", $values) . ")";

            if (mysqli_query($koneksi, $query)) {
                $gardu_id = mysqli_insert_id($koneksi);
                $insertedCount++;
                $insertPemeliharaanQuery = "INSERT INTO data_pemeliharaan (tanggal, nama_objek, id_sub_kategori) 
                                            VALUES (NOW(), 'gardu', $gardu_id)";
                if (!mysqli_query($koneksi, $insertPemeliharaanQuery)) {
                    $errorCount++;
                }
            } else {
                $errorCount++;
            }
        }

        if ($errorCount > 0) {
            echo "<script>alert('Selesai: $insertedCount data berhasil, $errorCount gagal.'); window.location.href = 'index.php?page=data-gardu';</script>";
        } else {
            echo "<script>alert('Import berhasil: $insertedCount data disimpan.'); window.location.href = 'index.php?page=data-gardu';</script>";
        }
    }

    public function importExcel($file)
    {
        require_once 'vendor/autoload.php';
        include_once 'koneksi.php';
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
                    $colIndex = Coordinate::stringFromColumnIndex($i + 3);
                    $val = $sheet->getCell("$colIndex$row")->getValue();
                    $val = trim((string)$val);
                    $val = filter_var(str_replace(',', '.', $val), FILTER_VALIDATE_FLOAT);
                    $values[] = $val === false ? 0 : $val;
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
                $data = $_POST;
                foreach ($data as $key => $val) {
                    if ($key !== 'nama_penyulang' && $key !== 'tanggal') {
                        $data[$key] = (float)$val;
                    }
                }

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
    if (preg_match('/(\\d{4})/', $fileName, $yearMatches)) {
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
    if (preg_match('/(\\w+)\\s+(\\d{4})/', $tanggalString, $matches)) {
        $bulanNama = ucfirst(strtolower($matches[1]));
        if (!isset($monthNames[$bulanNama])) return date('Y-m-d');
        $bulanAngka = $monthNames[$bulanNama];
        $tahun = $matches[2];
        return "$tahun-$bulanAngka-01";
    }
    return date('Y-m-d');
}
