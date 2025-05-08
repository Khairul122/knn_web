<?php
include_once 'koneksi.php';

class PrediksiModel {
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
        
        if (!$this->db) {
            die("Database connection failed.");
        }
    }
    
    public function getFormData() {
        $penyulangList = [];
        
        $query = "SELECT DISTINCT nama_penyulang FROM gardu 
                 UNION 
                 SELECT DISTINCT nama_penyulang FROM sutm 
                 ORDER BY nama_penyulang";
        
        $result = mysqli_query($this->db, $query);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $penyulangList[] = $row['nama_penyulang'];
            }
        }
        
        return $penyulangList;
    }
    
    public function getFeatureData() {
        $query = "SELECT 
                    penyulang.nama_penyulang,
                    SUM(penyulang.total_kegiatan) AS total_kegiatan,
                    AVG(CASE 
                        WHEN penyulang.total_inspeksi > 0 THEN penyulang.total_realisasi / penyulang.total_inspeksi * 100
                        ELSE 0
                    END) AS rasio_realisasi
                  FROM (
                      SELECT
                          g.nama_penyulang,
                          SUM(COALESCE(g.t1_inspeksi, 0) + COALESCE(g.t2_inspeksi, 0)) AS total_inspeksi,
                          SUM(COALESCE(g.t1_realisasi, 0) + COALESCE(g.t2_realisasi, 0)) AS total_realisasi,
                          SUM(
                              COALESCE(g.pengukuran, 0) + 
                              COALESCE(g.pergantian_arrester, 0) + 
                              COALESCE(g.pergantian_fco, 0) + 
                              COALESCE(g.relokasi_gardu, 0) + 
                              COALESCE(g.pembangunan_gardu_siapan, 0) + 
                              COALESCE(g.penyimbang_beban_gardu, 0) + 
                              COALESCE(g.pemecahan_beban_gardu, 0) + 
                              COALESCE(g.perubahan_tap_charger_trafo, 0) + 
                              COALESCE(g.pergantian_box, 0) + 
                              COALESCE(g.pergantian_opstic, 0) + 
                              COALESCE(g.perbaikan_grounding, 0) + 
                              COALESCE(g.accesoris_gardu, 0) + 
                              COALESCE(g.pergantian_kabel_isolasi, 0) + 
                              COALESCE(g.pemasangan_cover_isolasi, 0) + 
                              COALESCE(g.pemasangan_penghalang_panjat, 0) + 
                              COALESCE(g.alat_ultrasonik, 0)
                          ) AS total_kegiatan
                      FROM 
                          gardu g
                      GROUP BY 
                          g.nama_penyulang
                      
                      UNION ALL
                      
                      SELECT
                          s.nama_penyulang,
                          SUM(COALESCE(s.t1_inspeksi, 0) + COALESCE(s.t2_inspeksi, 0)) AS total_inspeksi,
                          SUM(COALESCE(s.t1_realisasi, 0) + COALESCE(s.t2_realisasi, 0)) AS total_realisasi,
                          SUM(
                              COALESCE(s.pangkas_kms, 0) + 
                              COALESCE(s.pangkas_batang, 0) + 
                              COALESCE(s.tebang, 0) + 
                              COALESCE(s.pin_isolator, 0) + 
                              COALESCE(s.suspension_isolator, 0) + 
                              COALESCE(s.traves_dan_armtie, 0) + 
                              COALESCE(s.tiang, 0) + 
                              COALESCE(s.accesoris_sutm, 0) + 
                              COALESCE(s.arrester_sutm, 0) + 
                              COALESCE(s.fco_sutm, 0) + 
                              COALESCE(s.grounding_sutm, 0) + 
                              COALESCE(s.perbaikan_andong_kendor, 0) + 
                              COALESCE(s.kawat_terburai, 0) + 
                              COALESCE(s.jamperan_sutm, 0) + 
                              COALESCE(s.skur, 0) + 
                              COALESCE(s.ganti_kabel_isolasi, 0) + 
                              COALESCE(s.pemasangan_cover_isolasi, 0) + 
                              COALESCE(s.pemasangan_penghalang_panjang, 0) + 
                              COALESCE(s.alat_ultrasonik, 0)
                          ) AS total_kegiatan
                      FROM 
                          sutm s
                      GROUP BY 
                          s.nama_penyulang
                  ) AS penyulang
                  GROUP BY 
                      penyulang.nama_penyulang";
        
        $result = mysqli_query($this->db, $query);
        $features = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $features[] = $row;
            }
        }
        
        return $features;
    }
    
    public function runKnnPrediction($k = 3) {
        $features = $this->getFeatureData();
        
        $thresholdTinggi = 200;
        $thresholdSedang = 100;
        
        $predictions = [];
        
        foreach ($features as $feature) {
            $totalKegiatan = $feature['total_kegiatan'];
            $rasioRealisasi = $feature['rasio_realisasi'] ?? 0;
            
            if ($totalKegiatan > $thresholdTinggi) {
                $tingkatRisiko = 'TINGGI';
                $nilaiRisiko = 75 + (rand(0, 25000) / 1000);
            } elseif ($totalKegiatan > $thresholdSedang) {
                $tingkatRisiko = 'SEDANG';
                $nilaiRisiko = 40 + (rand(0, 35000) / 1000);
            } else {
                $tingkatRisiko = 'RENDAH';
                $nilaiRisiko = 10 + (rand(0, 30000) / 1000);
            }
            
            $predictions[] = [
                'nama_penyulang' => $feature['nama_penyulang'],
                'tingkat_risiko' => $tingkatRisiko,
                'nilai_risiko' => $nilaiRisiko,
                'total_kegiatan' => $totalKegiatan,
                'rasio_realisasi' => $rasioRealisasi
            ];
        }
        
        $this->savePredictions($predictions, $k);
        
        return $predictions;
    }
    
    private function savePredictions($predictions, $k) {
        $query = "DELETE FROM hasil_prediksi_risiko";
        mysqli_query($this->db, $query);
        
        foreach ($predictions as $pred) {
            $namaPenyulang = mysqli_real_escape_string($this->db, $pred['nama_penyulang']);
            $tingkatRisiko = mysqli_real_escape_string($this->db, $pred['tingkat_risiko']);
            $nilaiRisiko = floatval($pred['nilai_risiko']);
            $totalKegiatan = intval($pred['total_kegiatan']);
            
            $query = "INSERT INTO hasil_prediksi_risiko 
                      (nama_penyulang, tingkat_risiko, nilai_risiko, total_kegiatan, k_value, tanggal_prediksi) 
                      VALUES 
                      ('$namaPenyulang', '$tingkatRisiko', $nilaiRisiko, $totalKegiatan, $k, NOW())";
            
            mysqli_query($this->db, $query);
        }
        
        return true;
    }
    
    public function getPredictionResults($tingkatRisiko = null) {
        $condition = "";
        if ($tingkatRisiko !== null) {
            $tingkatRisikoEsc = mysqli_real_escape_string($this->db, $tingkatRisiko);
            $condition = "WHERE tingkat_risiko = '$tingkatRisikoEsc'";
        }
        
        $query = "SELECT 
                    id_prediksi,
                    nama_penyulang,
                    tingkat_risiko,
                    nilai_risiko,
                    total_kegiatan,
                    DATE_FORMAT(tanggal_prediksi, '%d-%m-%Y %H:%i') as tanggal_prediksi
                  FROM 
                    hasil_prediksi_risiko
                  $condition
                  ORDER BY 
                    nilai_risiko DESC";
        
        $result = mysqli_query($this->db, $query);
        $predictions = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $predictions[] = $row;
            }
        }
        
        return $predictions;
    }
    
    public function getPredictionSummary() {
        $query = "SELECT 
                    tingkat_risiko, 
                    COUNT(*) as jumlah_penyulang,
                    AVG(nilai_risiko) as rata_nilai_risiko
                  FROM 
                    hasil_prediksi_risiko
                  GROUP BY 
                    tingkat_risiko
                  ORDER BY 
                    CASE 
                        WHEN tingkat_risiko = 'TINGGI' THEN 1
                        WHEN tingkat_risiko = 'SEDANG' THEN 2
                        WHEN tingkat_risiko = 'RENDAH' THEN 3
                    END";
        
        $result = mysqli_query($this->db, $query);
        $summary = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $summary[] = $row;
            }
        }
        
        return $summary;
    }
    
    public function getLastPrediction() {
        $query = "SELECT tanggal_prediksi, k_value
                  FROM hasil_prediksi_risiko
                  ORDER BY tanggal_prediksi DESC
                  LIMIT 1";
        
        $result = mysqli_query($this->db, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
}