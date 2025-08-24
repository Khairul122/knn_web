<?php

require_once 'koneksi.php';

class ClusterModel 
{
    private $db;

    public function __construct() 
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getCombinedData()  #Mengambil dan Menggabungkan Data Gardu & SUTM
    {
        $query = "
            SELECT 
                dp.id_data_pemeliharaan,
                dp.tanggal,
                dp.nama_objek,
                CASE 
                    WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                    WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                END as nama_penyulang,
                
                COALESCE(g.t1_inspeksi, s.t1_inspeksi, 0) as t1_inspeksi,
                COALESCE(g.t1_realisasi, s.t1_realisasi, 0) as t1_realisasi,
                COALESCE(g.t2_inspeksi, s.t2_inspeksi, 0) as t2_inspeksi,
                COALESCE(g.t2_realisasi, s.t2_realisasi, 0) as t2_realisasi,
                
                COALESCE(g.pengukuran, 0) as pengukuran,
                COALESCE(g.pergantian_arrester, 0) as pergantian_arrester,
                COALESCE(g.pergantian_fco, 0) as pergantian_fco,
                COALESCE(g.relokasi_gardu, 0) as relokasi_gardu,
                COALESCE(g.pembangunan_gardu_siapan, 0) as pembangunan_gardu_siapan,
                COALESCE(g.penyimbang_beban_gardu, 0) as penyimbang_beban_gardu,
                COALESCE(g.pemecahan_beban_gardu, 0) as pemecahan_beban_gardu,
                COALESCE(g.perubahan_tap_charger_trafo, 0) as perubahan_tap_charger_trafo,
                COALESCE(g.pergantian_box, 0) as pergantian_box,
                COALESCE(g.pergantian_opstic, 0) as pergantian_opstic,
                COALESCE(g.perbaikan_grounding, 0) as perbaikan_grounding,
                COALESCE(g.accesoris_gardu, 0) as accesoris_gardu,
                COALESCE(g.pergantian_kabel_isolasi, 0) as pergantian_kabel_isolasi,
                COALESCE(g.pemasangan_cover_isolasi, 0) as pemasangan_cover_isolasi,
                COALESCE(g.pemasangan_penghalang_panjat, 0) as pemasangan_penghalang_panjat,
                COALESCE(g.alat_ultrasonik, 0) as alat_ultrasonik,
                
                COALESCE(s.pangkas_kms, 0) as pangkas_kms,
                COALESCE(s.pangkas_batang, 0) as pangkas_batang,
                COALESCE(s.tebang, 0) as tebang,
                COALESCE(s.pin_isolator, 0) as pin_isolator,
                COALESCE(s.suspension_isolator, 0) as suspension_isolator,
                COALESCE(s.traves_dan_armtie, 0) as traves_dan_armtie,
                COALESCE(s.tiang, 0) as tiang,
                COALESCE(s.accesoris_sutm, 0) as accesoris_sutm,
                COALESCE(s.arrester_sutm, 0) as arrester_sutm,
                COALESCE(s.fco_sutm, 0) as fco_sutm,
                COALESCE(s.grounding_sutm, 0) as grounding_sutm,
                COALESCE(s.perbaikan_andong_kendor, 0) as perbaikan_andong_kendor,
                COALESCE(s.kawat_terburai, 0) as kawat_terburai,
                COALESCE(s.jamperan_sutm, 0) as jamperan_sutm,
                COALESCE(s.skur, 0) as skur,
                COALESCE(s.ganti_kabel_isolasi, 0) as ganti_kabel_isolasi,
                COALESCE(s.pemasangan_cover_isolasi, 0) as pemasangan_cover_isolasi_sutm,
                COALESCE(s.pemasangan_penghalang_panjang, 0) as pemasangan_penghalang_panjang_sutm,
                COALESCE(s.alat_ultrasonik, 0) as alat_ultrasonik_sutm
                
            FROM data_pemeliharaan dp
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            WHERE (dp.nama_objek = 'gardu' AND g.id_gardu IS NOT NULL) 
               OR (dp.nama_objek = 'sutm' AND s.id_sutm IS NOT NULL)
            ORDER BY dp.tanggal, dp.id_data_pemeliharaan
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Database query failed: " . $this->db->error);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function calculateAdvancedRiskScore($dataPoint) #Hitung Skor Risiko Total
    {
        $inspectionScore = $this->calculateInspectionScore($dataPoint);
        $maintenanceScore = $this->calculateMaintenanceScore($dataPoint);
        $criticalScore = $this->calculateCriticalRepairsScore($dataPoint);
        $infrastructureScore = $this->calculateInfrastructureScore($dataPoint);
        
        $finalScore = (
            $inspectionScore * 0.35 +
            $maintenanceScore * 0.30 +
            $criticalScore * 0.25 +
            $infrastructureScore * 0.10
        );
        
        return min(100, max(0, $finalScore * 100));
    }
    
    private function calculateInspectionScore($data)  #menghitung skor infrastruktur dari satu titik data ($data)
    {
        $t1_inspeksi = floatval($data['t1_inspeksi'] ?? 0);
        $t1_realisasi = floatval($data['t1_realisasi'] ?? 0);
        $t2_inspeksi = floatval($data['t2_inspeksi'] ?? 0);
        $t2_realisasi = floatval($data['t2_realisasi'] ?? 0);
        
        $totalInspeksi = $t1_inspeksi + $t2_inspeksi;
        $totalRealisasi = $t1_realisasi + $t2_realisasi;
        
        $gap = abs($totalInspeksi - $totalRealisasi);
        
        $inspectionNormalized = min(1, $totalInspeksi / 150);
        $gapNormalized = min(1, $gap / 50);
        
        $boostFactor = 1;
        if ($totalInspeksi > 100) {
            $boostFactor = 1.5;
        } elseif ($totalInspeksi > 50) {
            $boostFactor = 1.2;
        }
        
        $score = (($inspectionNormalized * 0.7) + ($gapNormalized * 0.3)) * $boostFactor;
        return min(1, $score);
    }
    
    private function calculateMaintenanceScore($data) 
    {
        $maintenanceTotal = 0;
        
        $highImpactActivities = [
            'pergantian_arrester' => 8,
            'pergantian_fco' => 8,
            'arrester_sutm' => 8,
            'fco_sutm' => 8,
            'perbaikan_grounding' => 6,
            'grounding_sutm' => 6,
            'pergantian_box' => 5,
            'pergantian_opstic' => 5,
            'pin_isolator' => 5,
            'suspension_isolator' => 5,
            'tiang' => 6,
            'traves_dan_armtie' => 5
        ];
        
        foreach ($highImpactActivities as $activity => $weight) {
            $value = floatval($data[$activity] ?? 0);
            $maintenanceTotal += $value * $weight;
        }
        
        $mediumImpactActivities = [
            'pengukuran' => 3,
            'accesoris_gardu' => 3,
            'accesoris_sutm' => 3,
            'pergantian_kabel_isolasi' => 4,
            'ganti_kabel_isolasi' => 4,
            'pemasangan_cover_isolasi' => 3,
            'pemasangan_cover_isolasi_sutm' => 3,
            'kawat_terburai' => 5,
            'perbaikan_andong_kendor' => 5
        ];
        
        foreach ($mediumImpactActivities as $activity => $weight) {
            $value = floatval($data[$activity] ?? 0);
            $maintenanceTotal += $value * $weight;
        }
        
        $volumeActivities = [
            'pangkas_batang' => 0.05,
            'tebang' => 3,
            'pangkas_kms' => 2
        ];
        
        foreach ($volumeActivities as $activity => $weight) {
            $value = floatval($data[$activity] ?? 0);
            $maintenanceTotal += $value * $weight;
        }
        
        return min(1, $maintenanceTotal / 100);
    }
    
    private function calculateCriticalRepairsScore($data) 
    {
        $criticalTotal = 0;
        
        $criticalRepairs = [
            'relokasi_gardu' => 30,
            'pembangunan_gardu_siapan' => 25,
            'perubahan_tap_charger_trafo' => 15,
            'penyimbang_beban_gardu' => 12,
            'pemecahan_beban_gardu' => 12,
            'jamperan_sutm' => 10,
            'skur' => 8
        ];
        
        foreach ($criticalRepairs as $repair => $weight) {
            $value = floatval($data[$repair] ?? 0);
            if ($value > 0) {
                $criticalTotal += $value * $weight;
            }
        }
        
        return min(1, $criticalTotal / 50);
    }
    
    private function calculateInfrastructureScore($data) #menghitung skor infrastruktur dari satu titik data 
    {
        $infraTotal = 0;
        
        $infraActivities = [
            'pemasangan_penghalang_panjat' => 1,
            'pemasangan_penghalang_panjang_sutm' => 1,
            'alat_ultrasonik' => 2,
            'alat_ultrasonik_sutm' => 2
        ];
        
        foreach ($infraActivities as $activity => $weight) {
            $value = floatval($data[$activity] ?? 0);
            $infraTotal += $value * $weight;
        }
        
        return min(1, $infraTotal / 50);
    }

    public function determineRiskLevel($riskScore) #untuk menentukan klasifikasi risiko berdasarkan nilai skor
    {
        if ($riskScore >= 25) {
            return 'TINGGI';
        } elseif ($riskScore >= 12) {
            return 'SEDANG';
        } else {
            return 'RENDAH';
        }
    }

    public function normalizeData($data) #melakukan normalisasi data ke dalam rentang 0–1 agar fitur bisa dibandingkan adil 
    {
        $features = [
            'inspection_score',
            'maintenance_score', 
            'critical_score',
            'infrastructure_score'
        ];
        
        $normalized = [];
        $stats = [];
        
        foreach ($data as &$row) {
            $row['inspection_score'] = $this->calculateInspectionScore($row) * 100;
            $row['maintenance_score'] = $this->calculateMaintenanceScore($row) * 100;
            $row['critical_score'] = $this->calculateCriticalRepairsScore($row) * 100;
            $row['infrastructure_score'] = $this->calculateInfrastructureScore($row) * 100;
        }
        
        foreach ($features as $feature) {
            $values = array_column($data, $feature);
            $values = array_filter($values, function($v) { return is_numeric($v) && $v >= 0; });
            
            if (empty($values)) {
                $stats[$feature] = ['min' => 0, 'max' => 1];
                continue;
            }
            
            $min = min($values);
            $max = max($values);
            $stats[$feature] = ['min' => $min, 'max' => max($max, $min + 1)];
        }
        
        foreach ($data as $row) {
            $normalizedRow = $row;
            foreach ($features as $feature) {
                $min = $stats[$feature]['min'];
                $max = $stats[$feature]['max'];
                $value = max(0, floatval($row[$feature] ?? 0));
                $normalizedRow[$feature] = ($max > $min) ? ($value - $min) / ($max - $min) : 0;
            }
            $normalized[] = $normalizedRow;
        }
        
        return ['data' => $normalized, 'stats' => $stats];
    }

    public function calculateDistance($point1, $point2, $features) #menghitung jarak Euclidean antara dua titik data pada fitur yang sama
    {
        $sum = 0;
        foreach ($features as $feature) {
            $val1 = floatval($point1[$feature] ?? 0);
            $val2 = floatval($point2[$feature] ?? 0);
            $sum += pow($val1 - $val2, 2);
        }
        return sqrt($sum);
    }

    public function initializeCentroids($data, $k, $features) #Inisialisasi titik pusat (centroid)
    {
        $n = count($data);
        if ($n == 0) {
            throw new Exception("No data available for clustering");
        }
        
        $centroids = [];
        $usedIndices = [];
        
        for ($i = 0; $i < $k; $i++) {
            do {
                $randomIndex = rand(0, $n - 1);
            } while (in_array($randomIndex, $usedIndices) && count($usedIndices) < $n);
            
            $usedIndices[] = $randomIndex;
            $centroid = [];
            
            foreach ($features as $feature) {
                $centroid[$feature] = floatval($data[$randomIndex][$feature] ?? 0);
            }
            $centroids[] = $centroid;
        }
        
        return $centroids;
    }

    public function assignClusters($data, $centroids, $features)  #Mengelompokkan setiap data ke centroid terdekat berdasarkan jarak
    {
        $clusters = [];
        
        foreach ($data as $index => $point) {
            $minDistance = INF;
            $assignedCluster = 0;
            
            foreach ($centroids as $clusterIndex => $centroid) {
                $distance = $this->calculateDistance($point, $centroid, $features);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $assignedCluster = $clusterIndex;
                }
            }
            
            $clusters[$index] = $assignedCluster;
        }
        
        return $clusters;
    }

    public function updateCentroids($data, $clusters, $k, $features)  #Menghitung ulang posisi centroid berdasarkan rata-rata semua anggota klaster
    {
        $newCentroids = [];
        
        for ($i = 0; $i < $k; $i++) {
            $clusterPoints = [];
            foreach ($clusters as $dataIndex => $cluster) {
                if ($cluster == $i && isset($data[$dataIndex])) {
                    $clusterPoints[] = $data[$dataIndex];
                }
            }
            
            if (empty($clusterPoints)) {
                $randomCentroid = $this->initializeCentroids($data, 1, $features);
                $newCentroids[] = $randomCentroid[0];
                continue;
            }
            
            $centroid = [];
            foreach ($features as $feature) {
                $sum = 0;
                foreach ($clusterPoints as $point) {
                    $sum += floatval($point[$feature] ?? 0);
                }
                $centroid[$feature] = $sum / count($clusterPoints);
            }
            $newCentroids[] = $centroid;
        }
        
        return $newCentroids;
    }

    public function performKMeansClustering($maxIterations = 100) #menjalankan K-Means sampai hasil stabil atau iterasi maksimal tercapai
    {
        try {
            if (!$this->db || $this->db->connect_error) {
                return ['success' => false, 'message' => 'Database connection failed'];
            }
            
            $data = $this->getCombinedData();
            
            if (empty($data)) {
                return ['success' => false, 'message' => 'Tidak ada data untuk di-cluster'];
            }
            
            $normalizedResult = $this->normalizeData($data);
            $normalizedData = $normalizedResult['data'];
            $features = ['inspection_score', 'maintenance_score', 'critical_score', 'infrastructure_score'];
            $k = 3;
            
            $centroids = $this->initializeCentroids($normalizedData, $k, $features);
            $previousClusters = [];
            $iteration = 0;
            
            for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
                $clusters = $this->assignClusters($normalizedData, $centroids, $features);
                
                if ($clusters === $previousClusters) {
                    break;
                }
                
                $centroids = $this->updateCentroids($normalizedData, $clusters, $k, $features);
                $previousClusters = $clusters;
            }
            
            try {
                $this->clearPreviousResults();
            } catch (Exception $e) {
            }
            
            $this->db->begin_transaction();
            $successCount = 0;
            
            $insertQuery = "INSERT INTO hasil_cluster (id_data_pemeliharaan, cluster_label, risk_score, tingkat_risiko) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($insertQuery);
            
            if (!$stmt) {
                $this->db->rollback();
                return ['success' => false, 'message' => 'Prepare statement failed: ' . $this->db->error];
            }
            
            foreach ($data as $index => $dataPoint) {
                if (!isset($clusters[$index])) {
                    continue;
                }
                
                $riskScore = $this->calculateAdvancedRiskScore($dataPoint);
                $riskLevel = $this->determineRiskLevel($riskScore);
                $clusterLabel = $clusters[$index];
                $idData = intval($dataPoint['id_data_pemeliharaan']);
                
                if ($idData <= 0) {
                    continue;
                }
                
                $stmt->bind_param("iids", $idData, $clusterLabel, $riskScore, $riskLevel);
                
                if ($stmt->execute()) {
                    $successCount++;
                }
            }
            
            $stmt->close();
            
            if ($successCount > 0) {
                $this->db->commit();
                return [
                    'success' => true,
                    'message' => 'Clustering berhasil dilakukan dengan semua kolom',
                    'total_data' => count($data),
                    'iterations' => $iteration + 1,
                    'inserted_records' => $successCount,
                    'features_used' => count($features) + count($this->getAllFeatures())
                ];
            } else {
                $this->db->rollback();
                return ['success' => false, 'message' => 'Tidak ada data yang berhasil disimpan'];
            }
            
        } catch (Exception $e) {
            if ($this->db) {
                $this->db->rollback();
            }
            return ['success' => false, 'message' => 'Clustering gagal: ' . $e->getMessage()];
        }
    }

    private function getAllFeatures() #Mengembalikan daftar semua fitur dari gardu & SUTM
    {
        return [
            't1_inspeksi', 't1_realisasi', 't2_inspeksi', 't2_realisasi',
            'pengukuran', 'pergantian_arrester', 'pergantian_fco', 'relokasi_gardu',
            'pembangunan_gardu_siapan', 'penyimbang_beban_gardu', 'pemecahan_beban_gardu',
            'perubahan_tap_charger_trafo', 'pergantian_box', 'pergantian_opstic',
            'perbaikan_grounding', 'accesoris_gardu', 'pergantian_kabel_isolasi',
            'pemasangan_cover_isolasi', 'pemasangan_penghalang_panjat', 'alat_ultrasonik',
            'pangkas_kms', 'pangkas_batang', 'tebang', 'pin_isolator', 'suspension_isolator',
            'traves_dan_armtie', 'tiang', 'accesoris_sutm', 'arrester_sutm', 'fco_sutm',
            'grounding_sutm', 'perbaikan_andong_kendor', 'kawat_terburai', 'jamperan_sutm',
            'skur', 'ganti_kabel_isolasi', 'pemasangan_cover_isolasi_sutm', 
            'pemasangan_penghalang_panjang_sutm', 'alat_ultrasonik_sutm'
        ];
    }

    public function clearPreviousResults() #Menghapus isi tabel hasil_cluster
    {
        $result = $this->db->query("DELETE FROM hasil_cluster");
        if (!$result) {
            throw new Exception("Failed to clear previous results: " . $this->db->error);
        }
        return true;
    }

#Fungsi Statistik & Visualisasi
    public function getClusterSummary() #Ringkasan per klaster
    {
        $query = "
            SELECT 
                cluster_label,
                tingkat_risiko,
                COUNT(*) as jumlah,
                ROUND(AVG(risk_score), 2) as avg_risk_score,
                ROUND(MIN(risk_score), 2) as min_risk_score,
                ROUND(MAX(risk_score), 2) as max_risk_score
            FROM hasil_cluster 
            GROUP BY cluster_label, tingkat_risiko
            ORDER BY cluster_label, 
                CASE tingkat_risiko 
                    WHEN 'RENDAH' THEN 1 
                    WHEN 'SEDANG' THEN 2 
                    WHEN 'TINGGI' THEN 3 
                END
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get cluster summary: " . $this->db->error);
        }
        
        $summary = [];
        while ($row = $result->fetch_assoc()) {
            $summary[] = $row;
        }
        
        return $summary;
    }

    public function getClusterResults($limit = null) #Menampilkan data hasil cluster
    {
        $limitClause = $limit ? "LIMIT " . intval($limit) : "";
        
        $query = "
            SELECT 
                hc.*,
                dp.tanggal,
                dp.nama_objek,
                CASE 
                    WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                    WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                    ELSE 'Unknown'
                END as nama_penyulang
            FROM hasil_cluster hc
            JOIN data_pemeliharaan dp ON hc.id_data_pemeliharaan = dp.id_data_pemeliharaan
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            ORDER BY hc.risk_score DESC, hc.tingkat_risiko DESC, dp.tanggal DESC
            {$limitClause}
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get cluster results: " . $this->db->error);
        }
        
        $results = [];
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        
        return $results;
    }

    public function getClusterStatistics() #Statistik keseluruhan hasil cluster
    {
        $query = "
            SELECT 
                COUNT(*) as total_data,
                SUM(CASE WHEN tingkat_risiko = 'RENDAH' THEN 1 ELSE 0 END) as rendah,
                SUM(CASE WHEN tingkat_risiko = 'SEDANG' THEN 1 ELSE 0 END) as sedang,
                SUM(CASE WHEN tingkat_risiko = 'TINGGI' THEN 1 ELSE 0 END) as tinggi,
                ROUND(AVG(risk_score), 2) as avg_risk_score,
                ROUND(MIN(risk_score), 2) as min_risk_score,
                ROUND(MAX(risk_score), 2) as max_risk_score
            FROM hasil_cluster
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get cluster statistics: " . $this->db->error);
        }
        
        $stats = $result->fetch_assoc();
        return $stats ?: [
            'total_data' => 0, 'rendah' => 0, 'sedang' => 0, 'tinggi' => 0,
            'avg_risk_score' => 0, 'min_risk_score' => 0, 'max_risk_score' => 0
        ];
    }

    public function getPenyulangRiskAnalysis() #Risiko berdasarkan nama_penyulang
    {
        $query = "
            SELECT 
                CASE 
                    WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                    WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                    ELSE 'Unknown'
                END as nama_penyulang,
                COUNT(*) as total_aktivitas,
                ROUND(AVG(hc.risk_score), 2) as avg_risk_score,
                SUM(CASE WHEN hc.tingkat_risiko = 'TINGGI' THEN 1 ELSE 0 END) as tinggi_count,
                SUM(CASE WHEN hc.tingkat_risiko = 'SEDANG' THEN 1 ELSE 0 END) as sedang_count,
                SUM(CASE WHEN hc.tingkat_risiko = 'RENDAH' THEN 1 ELSE 0 END) as rendah_count,
                ROUND(MAX(hc.risk_score), 2) as max_risk_score,
                ROUND(MIN(hc.risk_score), 2) as min_risk_score
            FROM hasil_cluster hc
            JOIN data_pemeliharaan dp ON hc.id_data_pemeliharaan = dp.id_data_pemeliharaan
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            GROUP BY nama_penyulang
            HAVING nama_penyulang IS NOT NULL AND nama_penyulang != 'Unknown'
            ORDER BY avg_risk_score DESC, tinggi_count DESC
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get penyulang risk analysis: " . $this->db->error);
        }
        
        $analysis = [];
        while ($row = $result->fetch_assoc()) {
            $analysis[] = $row;
        }
        
        return $analysis;
    }

    public function getDetailClusterData($id) #Detail lengkap 1 data berdasarkan ID
    {
        $query = "
            SELECT 
                hc.*,
                dp.tanggal,
                dp.nama_objek,
                CASE 
                    WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                    WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                    ELSE 'Unknown'
                END as nama_penyulang,
                
                g.t1_inspeksi as gardu_t1_inspeksi, g.t1_realisasi as gardu_t1_realisasi,
                g.t2_inspeksi as gardu_t2_inspeksi, g.t2_realisasi as gardu_t2_realisasi,
                g.pengukuran, g.pergantian_arrester, g.pergantian_fco, g.relokasi_gardu,
                g.pembangunan_gardu_siapan, g.penyimbang_beban_gardu, g.pemecahan_beban_gardu,
                g.perubahan_tap_charger_trafo, g.pergantian_box, g.pergantian_opstic,
                g.perbaikan_grounding, g.accesoris_gardu, g.pergantian_kabel_isolasi,
                g.pemasangan_cover_isolasi, g.pemasangan_penghalang_panjat, g.alat_ultrasonik,
                
                s.t1_inspeksi as sutm_t1_inspeksi, s.t1_realisasi as sutm_t1_realisasi,
                s.t2_inspeksi as sutm_t2_inspeksi, s.t2_realisasi as sutm_t2_realisasi,
                s.pangkas_kms, s.pangkas_batang, s.tebang, s.row_lain, s.pin_isolator,
                s.suspension_isolator, s.traves_dan_armtie, s.tiang, s.accesoris_sutm,
                s.arrester_sutm, s.fco_sutm, s.grounding_sutm, s.perbaikan_andong_kendor,
                s.kawat_terburai, s.jamperan_sutm, s.skur, s.ganti_kabel_isolasi,
                s.pemasangan_cover_isolasi as sutm_pemasangan_cover_isolasi,
                s.pemasangan_penghalang_panjang, s.alat_ultrasonik as sutm_alat_ultrasonik
                
            FROM hasil_cluster hc
            JOIN data_pemeliharaan dp ON hc.id_data_pemeliharaan = dp.id_data_pemeliharaan
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            WHERE hc.id_data_pemeliharaan = ?
            LIMIT 1
        ";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data;
    }

#Fungsi Reset & Optimasi
    public function resetAllData() #Hapus semua data hasil & reset auto_increment
    {
        try {
            $this->db->begin_transaction();
            
            $queries = [
                "DELETE FROM hasil_cluster",
                "DELETE FROM split_data", 
                "DELETE FROM hasil_prediksi_risiko",
                "ALTER TABLE hasil_cluster AUTO_INCREMENT = 1",
                "ALTER TABLE split_data AUTO_INCREMENT = 1", 
                "ALTER TABLE hasil_prediksi_risiko AUTO_INCREMENT = 1"
            ];
            
            $successCount = 0;
            foreach ($queries as $query) {
                if ($this->db->query($query)) {
                    $successCount++;
                } else {
                    throw new Exception("Reset query failed: " . $this->db->error . " - Query: $query");
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Semua data hasil clustering berhasil direset',
                'queries_executed' => $successCount,
                'tables_reset' => ['hasil_cluster', 'split_data', 'hasil_prediksi_risiko']
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Reset gagal: ' . $e->getMessage()
            ];
        }
    }

    public function resetClusterOnly() #Hanya hapus hasil_cluster
    {
        try {
            $this->db->begin_transaction();
            
            $result1 = $this->db->query("DELETE FROM hasil_cluster");
            $result2 = $this->db->query("ALTER TABLE hasil_cluster AUTO_INCREMENT = 1");
            
            if ($result1 && $result2) {
                $this->db->commit();
                
                return [
                    'success' => true,
                    'message' => 'Data clustering berhasil direset',
                    'table_reset' => 'hasil_cluster'
                ];
            } else {
                throw new Exception("Reset cluster failed: " . $this->db->error);
            }
            
        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Reset cluster gagal: ' . $e->getMessage()
            ];
        }
    }

    public function getSystemInfo() # Info sistem, total data di setiap tabel, versi MySQL
    {
        try {
            $info = [
                'database_name' => $this->db->query("SELECT DATABASE() as db_name")->fetch_assoc()['db_name'],
                'mysql_version' => $this->db->query("SELECT VERSION() as version")->fetch_assoc()['version'],
                'total_tables' => 0,
                'table_status' => [],
                'clustering_features' => [
                    'total_gardu_features' => 16,
                    'total_sutm_features' => 19,
                    'inspection_features' => 4,
                    'total_features_used' => 39
                ]
            ];
            
            $tables = ['data_pemeliharaan', 'gardu', 'sutm', 'hasil_cluster', 'split_data', 'hasil_prediksi_risiko', 'users'];
            
            foreach ($tables as $table) {
                $result = $this->db->query("SELECT COUNT(*) as count FROM $table");
                if ($result) {
                    $count = $result->fetch_assoc()['count'];
                    $info['table_status'][$table] = $count;
                    $info['total_tables']++;
                } else {
                    $info['table_status'][$table] = 'Error: ' . $this->db->error;
                }
            }
            
            $info['last_cluster_date'] = null;
            $lastCluster = $this->db->query("SELECT MAX(id_cluster) as last_id FROM hasil_cluster");
            if ($lastCluster && $lastCluster->num_rows > 0) {
                $lastId = $lastCluster->fetch_assoc()['last_id'];
                $info['last_cluster_id'] = $lastId;
            }
            
            return $info;
            
        } catch (Exception $e) {
            return [
                'error' => 'Failed to get system info: ' . $e->getMessage()
            ];
        }
    }

    public function optimizeDatabase() #Optimasi tabel database (pakai OPTIMIZE TABLE)
    {
        try {
            $tables = ['data_pemeliharaan', 'gardu', 'sutm', 'hasil_cluster', 'split_data', 'hasil_prediksi_risiko'];
            $optimized = [];
            
            foreach ($tables as $table) {
                $result = $this->db->query("OPTIMIZE TABLE $table");
                if ($result) {
                    $optimized[] = $table;
                }
            }
            
            return [
                'success' => true,
                'message' => 'Database berhasil dioptimasi',
                'optimized_tables' => $optimized
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Optimasi gagal: ' . $e->getMessage()
            ];
        }
    }

    public function analyzeDataDistribution()  #Analisis jumlah data gardu/SUTM, prediksi distribusi risiko, dan data risiko tinggi potensial
    {
        try {
            $data = $this->getCombinedData();
            $analysis = [
                'total_records' => count($data),
                'gardu_records' => 0,
                'sutm_records' => 0,
                'high_risk_candidates' => [],
                'feature_statistics' => []
            ];
            
            $highRiskThreshold = 50;
            
            foreach ($data as $row) {
                if ($row['nama_objek'] === 'gardu') {
                    $analysis['gardu_records']++;
                } else {
                    $analysis['sutm_records']++;
                }
                
                $riskScore = $this->calculateAdvancedRiskScore($row);
                
                if ($riskScore >= $highRiskThreshold) {
                    $analysis['high_risk_candidates'][] = [
                        'id' => $row['id_data_pemeliharaan'],
                        'penyulang' => $row['nama_penyulang'],
                        'objek' => $row['nama_objek'],
                        'risk_score' => round($riskScore, 2),
                        'tanggal' => $row['tanggal']
                    ];
                }
            }
            
            $totalData = count($data);
            $analysis['expected_distribution'] = [
                'TINGGI' => ['count' => round($totalData * 0.25), 'percentage' => '25%'],
                'SEDANG' => ['count' => round($totalData * 0.35), 'percentage' => '35%'],
                'RENDAH' => ['count' => round($totalData * 0.40), 'percentage' => '40%']
            ];
            
            return $analysis;
            
        } catch (Exception $e) {
            return [
                'error' => 'Failed to analyze data distribution: ' . $e->getMessage()
            ];
        }
    }
}