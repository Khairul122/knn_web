<?php
include_once 'koneksi.php';

class GarduModel
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAllGardu()
    {
        $query = "SELECT g.*, dp.tanggal 
                  FROM gardu g
                  LEFT JOIN data_pemeliharaan dp ON g.id_gardu = dp.id_sub_kategori
                  WHERE dp.nama_objek = 'gardu'";
        $result = $this->db->query($query);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function getGarduByMonthYear($bulan, $tahun)
    {
        $filter = "$bulan-$tahun";
    
        $query = "SELECT g.*, dp.tanggal 
                  FROM gardu g
                  LEFT JOIN data_pemeliharaan dp ON g.id_gardu = dp.id_sub_kategori
                  WHERE dp.nama_objek = 'gardu' 
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
    
    public function getGarduById($id)
    {
        $query = "SELECT g.*, dp.tanggal 
                  FROM gardu g
                  LEFT JOIN data_pemeliharaan dp ON g.id_gardu = dp.id_sub_kategori
                  WHERE g.id_gardu = ? AND dp.nama_objek = 'gardu'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function updateGardu($id, $data)
    {
        // Gunakan query manual untuk menghindari masalah dengan bind_param
        $query = "UPDATE gardu SET 
                  nama_penyulang = '" . $this->db->real_escape_string($data['nama_penyulang'] ?? '') . "',
                  t1_inspeksi = " . floatval($data['t1_inspeksi'] ?? 0) . ",
                  t1_realisasi = " . floatval($data['t1_realisasi'] ?? 0) . ",
                  t2_inspeksi = " . floatval($data['t2_inspeksi'] ?? 0) . ",
                  t2_realisasi = " . floatval($data['t2_realisasi'] ?? 0) . ",
                  pengukuran = " . floatval($data['pengukuran'] ?? 0) . ",
                  pergantian_arrester = " . intval($data['pergantian_arrester'] ?? 0) . ",
                  pergantian_fco = " . intval($data['pergantian_fco'] ?? 0) . ",
                  relokasi_gardu = " . intval($data['relokasi_gardu'] ?? 0) . ",
                  pembangunan_gardu_siapan = " . intval($data['pembangunan_gardu_siapan'] ?? 0) . ",
                  penyimbang_beban_gardu = " . intval($data['penyimbang_beban_gardu'] ?? 0) . ",
                  pemecahan_beban_gardu = " . intval($data['pemecahan_beban_gardu'] ?? 0) . ",
                  perubahan_tap_charger_trafo = " . intval($data['perubahan_tap_charger_trafo'] ?? 0) . ",
                  pergantian_box = " . intval($data['pergantian_box'] ?? 0) . ",
                  pergantian_opstic = " . intval($data['pergantian_opstic'] ?? 0) . ",
                  perbaikan_grounding = " . intval($data['perbaikan_grounding'] ?? 0) . ",
                  accesoris_gardu = " . intval($data['accesoris_gardu'] ?? 0) . ",
                  pergantian_kabel_isolasi = " . intval($data['pergantian_kabel_isolasi'] ?? 0) . ",
                  pemasangan_cover_isolasi = " . intval($data['pemasangan_cover_isolasi'] ?? 0) . ",
                  pemasangan_penghalang_panjat = " . intval($data['pemasangan_penghalang_panjat'] ?? 0) . ",
                  alat_ultrasonik = " . intval($data['alat_ultrasonik'] ?? 0) . "
                  WHERE id_gardu = " . intval($id);
        
        $result = $this->db->query($query);
        
        // Update tanggal jika bulan dan tahun ada
        if ($result && isset($data['bulan']) && isset($data['tahun'])) {
            $tanggal = $data['bulan'] . '-' . $data['tahun']; // Format sesuai "Maret-2023"
            $tanggal_escaped = $this->db->real_escape_string($tanggal);
            
            $updateDateQuery = "UPDATE data_pemeliharaan SET tanggal = '$tanggal_escaped' 
                              WHERE id_sub_kategori = " . intval($id) . " AND nama_objek = 'gardu'";
            $this->db->query($updateDateQuery);
        }
        
        return $result;
    }

    public function deleteGardu($id)
    {
        $query = "DELETE FROM data_pemeliharaan WHERE id_sub_kategori = ? AND nama_objek = 'gardu'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $query = "DELETE FROM gardu WHERE id_gardu = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getAvailableMonths()
    {
        $query = "SELECT DISTINCT tanggal FROM data_pemeliharaan WHERE nama_objek = 'gardu' ORDER BY tanggal DESC";
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