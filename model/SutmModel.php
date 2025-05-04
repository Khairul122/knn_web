<?php
include_once 'koneksi.php';

class SutmModel
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAllSutm()
    {
        $query = "SELECT s.*, dp.tanggal 
                  FROM sutm s
                  LEFT JOIN data_pemeliharaan dp ON s.id_sutm = dp.id_sub_kategori
                  WHERE dp.nama_objek = 'sutm'";
        $result = $this->db->query($query);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function getSutmByMonthYear($bulan, $tahun)
    {
        $filter = "$bulan-$tahun";
    
        $query = "SELECT s.*, dp.tanggal 
                  FROM sutm s
                  LEFT JOIN data_pemeliharaan dp ON s.id_sutm = dp.id_sub_kategori
                  WHERE dp.nama_objek = 'sutm' 
                  AND dp.tanggal = ?";
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $filter);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
    
        return $data;
    }
    
    public function getSutmById($id)
    {
        $query = "SELECT s.*, dp.tanggal 
                  FROM sutm s
                  LEFT JOIN data_pemeliharaan dp ON s.id_sutm = dp.id_sub_kategori
                  WHERE s.id_sutm = ? AND dp.nama_objek = 'sutm'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function updateSutm($id, $data)
    {
        $query = "UPDATE sutm SET 
                  nama_penyulang = '" . $this->db->real_escape_string($data['nama_penyulang'] ?? '') . "',
                  t1_inspeksi = " . floatval($data['t1_inspeksi'] ?? 0) . ",
                  t1_realisasi = " . floatval($data['t1_realisasi'] ?? 0) . ",
                  t2_inspeksi = " . floatval($data['t2_inspeksi'] ?? 0) . ",
                  t2_realisasi = " . floatval($data['t2_realisasi'] ?? 0) . ",
                  pangkas_kms = " . floatval($data['pangkas_kms'] ?? 0) . ",
                  pangkas_batang = " . intval($data['pangkas_batang'] ?? 0) . ",
                  tebang = " . intval($data['tebang'] ?? 0) . ",
                  row_lain = '" . $this->db->real_escape_string($data['row_lain'] ?? '') . "',
                  pin_isolator = " . intval($data['pin_isolator'] ?? 0) . ",
                  suspension_isolator = " . intval($data['suspension_isolator'] ?? 0) . ",
                  traves_dan_armtie = " . intval($data['traves_dan_armtie'] ?? 0) . ",
                  tiang = " . intval($data['tiang'] ?? 0) . ",
                  accesoris_sutm = " . intval($data['accesoris_sutm'] ?? 0) . ",
                  arrester_sutm = " . intval($data['arrester_sutm'] ?? 0) . ",
                  fco_sutm = " . intval($data['fco_sutm'] ?? 0) . ",
                  grounding_sutm = " . intval($data['grounding_sutm'] ?? 0) . ",
                  perbaikan_andong_kendor = " . intval($data['perbaikan_andong_kendor'] ?? 0) . ",
                  kawat_terburai = " . intval($data['kawat_terburai'] ?? 0) . ",
                  jamperan_sutm = " . intval($data['jamperan_sutm'] ?? 0) . ",
                  skur = " . intval($data['skur'] ?? 0) . ",
                  ganti_kabel_isolasi = " . intval($data['ganti_kabel_isolasi'] ?? 0) . ",
                  pemasangan_cover_isolasi = " . intval($data['pemasangan_cover_isolasi'] ?? 0) . ",
                  pemasangan_penghalang_panjang = " . intval($data['pemasangan_penghalang_panjang'] ?? 0) . ",
                  alat_ultrasonik = " . intval($data['alat_ultrasonik'] ?? 0) . "
                  WHERE id_sutm = " . intval($id);
        
        $result = $this->db->query($query);
        
        if ($result && isset($data['bulan']) && isset($data['tahun'])) {
            $tanggal = $data['bulan'] . '-' . $data['tahun'];
            $tanggal_escaped = $this->db->real_escape_string($tanggal);
            
            $updateDateQuery = "UPDATE data_pemeliharaan SET tanggal = '$tanggal_escaped' 
                              WHERE id_sub_kategori = " . intval($id) . " AND nama_objek = 'sutm'";
            $this->db->query($updateDateQuery);
        }
        
        return $result;
    }

    public function deleteSutm($id)
    {
        $query = "DELETE FROM data_pemeliharaan WHERE id_sub_kategori = ? AND nama_objek = 'sutm'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $query = "DELETE FROM sutm WHERE id_sutm = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getAvailableMonths()
    {
        $query = "SELECT DISTINCT tanggal FROM data_pemeliharaan WHERE nama_objek = 'sutm' ORDER BY tanggal DESC";
        $result = $this->db->query($query);
        
        $months = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tanggalParts = explode('-', $row['tanggal']);
                if (count($tanggalParts) == 2) {
                    $bulan = $tanggalParts[0];
                    $tahun = $tanggalParts[1];
                    $months[] = [
                        'bulan_nama' => $bulan,
                        'tahun' => $tahun,
                        'label' => $bulan . ' ' . $tahun
                    ];
                }
            }
        }
        
        return $months;
    }
    
    private function getNamaBulan($bulanAngka)
    {
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        return $namaBulan[$bulanAngka] ?? 'Januari';
    }
    
    private function getBulanAngka($bulanNama)
    {
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        
        return $bulanMap[ucfirst(strtolower($bulanNama))] ?? 1;
    }
}