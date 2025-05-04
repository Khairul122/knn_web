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
        $query = "UPDATE gardu SET 
                  nama_penyulang = ?,
                  t1_inspeksi = ?,
                  t1_realisasi = ?,
                  t2_inspeksi = ?,
                  t2_realisasi = ?,
                  pengukuran = ?,
                  pergantian_arrester = ?,
                  pergantian_fco = ?,
                  relokasi_gardu = ?,
                  pembangunan_gardu_siapan = ?,
                  penyimbang_beban_gardu = ?,
                  pemecahan_beban_gardu = ?,
                  perubahan_tap_charger_trafo = ?,
                  pergantian_box = ?,
                  pergantian_opstic = ?,
                  perbaikan_grounding = ?,
                  accesoris_gardu = ?,
                  pergantian_kabel_isolasi = ?,
                  pemasangan_cover_isolasi = ?,
                  pemasangan_penghalang_panjat = ?,
                  alat_ultrasonik = ?
                  WHERE id_gardu = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "sdddddiiiiiiiiiiiiiii", 
            $data['nama_penyulang'],
            $data['t1_inspeksi'],
            $data['t1_realisasi'],
            $data['t2_inspeksi'],
            $data['t2_realisasi'],
            $data['pengukuran'],
            $data['pergantian_arrester'],
            $data['pergantian_fco'],
            $data['relokasi_gardu'],
            $data['pembangunan_gardu_siapan'],
            $data['penyimbang_beban_gardu'],
            $data['pemecahan_beban_gardu'],
            $data['perubahan_tap_charger_trafo'],
            $data['pergantian_box'],
            $data['pergantian_opstic'],
            $data['perbaikan_grounding'],
            $data['accesoris_gardu'],
            $data['pergantian_kabel_isolasi'],
            $data['pemasangan_cover_isolasi'],
            $data['pemasangan_penghalang_panjat'],
            $data['alat_ultrasonik'],
            $id
        );
        
        $result = $stmt->execute();
        
        if ($result && isset($data['tanggal'])) {
            $tanggalObj = $this->formatTanggalKeSQL($data['tanggal']);
            
            $updateDateQuery = "UPDATE data_pemeliharaan SET tanggal = ? 
                                WHERE id_sub_kategori = ? AND nama_objek = 'gardu'";
            $dateStmt = $this->db->prepare($updateDateQuery);
            $dateStmt->bind_param("si", $tanggalObj, $id);
            $dateStmt->execute();
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
        $query = "SELECT DISTINCT YEAR(tanggal) as tahun, MONTH(tanggal) as bulan 
                  FROM data_pemeliharaan 
                  WHERE nama_objek = 'gardu' 
                  ORDER BY tahun DESC, bulan DESC";
        $result = $this->db->query($query);
        
        $months = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bulanNama = $this->getNamaBulan($row['bulan']);
                $months[] = [
                    'bulan_angka' => $row['bulan'],
                    'bulan_nama' => $bulanNama,
                    'tahun' => $row['tahun'],
                    'label' => $bulanNama . ' ' . $row['tahun']
                ];
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
    
    private function formatTanggalKeSQL($tanggalString)
    {
        if (preg_match('/(\w+)\s+(\d{4})/', $tanggalString, $matches)) {
            $bulan = $this->getBulanAngka($matches[1]);
            $tahun = $matches[2];
            return "$tahun-$bulan-01"; 
        }

        return date('Y-m-d');
    }
}