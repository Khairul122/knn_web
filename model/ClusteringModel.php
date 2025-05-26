<?php
include_once 'koneksi.php';

class ClusteringModel {
    private $db;
    
    public function __construct() {
        global $koneksi;
        $this->db = $koneksi;
        if (!$this->db) {
            throw new Exception("Database connection failed: " . mysqli_connect_error());
        }
    }
    
    public function getAllMaintenanceData() {
        $query = "SELECT dp.*, 
                         CASE 
                             WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                             WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                             ELSE 'Unknown'
                         END as nama_penyulang
                  FROM data_pemeliharaan dp
                  LEFT JOIN gardu g ON dp.id_sub_kategori = g.id_gardu AND dp.nama_objek = 'gardu'
                  LEFT JOIN sutm s ON dp.id_sub_kategori = s.id_sutm AND dp.nama_objek = 'sutm'
                  ORDER BY dp.tanggal DESC";
        
        $result = mysqli_query($this->db, $query);
        if (!$result) {
            throw new Exception("Query error: " . mysqli_error($this->db));
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    public function getMaintenanceDataWithFeatures() {
        $query = "SELECT dp.id_data_pemeliharaan,
                         dp.tanggal,
                         dp.nama_objek,
                         dp.id_sub_kategori,
                         CASE 
                             WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                             WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                             ELSE 'Unknown'
                         END as nama_penyulang,
                         
                         COALESCE(g.t1_inspeksi, 0) + COALESCE(s.t1_inspeksi, 0) as t1_inspeksi,
                         COALESCE(g.t1_realisasi, 0) + COALESCE(s.t1_realisasi, 0) as t1_realisasi,
                         COALESCE(g.t2_inspeksi, 0) + COALESCE(s.t2_inspeksi, 0) as t2_inspeksi,
                         COALESCE(g.t2_realisasi, 0) + COALESCE(s.t2_realisasi, 0) as t2_realisasi,
                         COALESCE(g.pengukuran, 0) as gardu_pengukuran,
                         COALESCE(g.pergantian_arrester, 0) as gardu_arrester,
                         COALESCE(g.pergantian_fco, 0) as gardu_fco,
                         COALESCE(g.perbaikan_grounding, 0) as gardu_grounding,
                         COALESCE(s.pangkas_kms, 0) as sutm_pangkas_kms,
                         COALESCE(s.pangkas_batang, 0) as sutm_pangkas_batang,
                         COALESCE(s.tebang, 0) as sutm_tebang,
                         COALESCE(s.pin_isolator, 0) as sutm_pin_isolator,
                         COALESCE(s.arrester_sutm, 0) as sutm_arrester,
                         COALESCE(s.fco_sutm, 0) as sutm_fco,
                         COALESCE(s.grounding_sutm, 0) as sutm_grounding,
                         COALESCE(g.pemasangan_cover_isolasi, 0) + COALESCE(s.pemasangan_cover_isolasi, 0) as cover_isolasi,
                         COALESCE(g.pemasangan_penghalang_panjat, 0) + COALESCE(s.pemasangan_penghalang_panjang, 0) as penghalang_panjat,
                         COALESCE(g.alat_ultrasonik, 0) + COALESCE(s.alat_ultrasonik, 0) as ultrasonik
                         
                  FROM data_pemeliharaan dp
                  LEFT JOIN gardu g ON dp.id_sub_kategori = g.id_gardu AND dp.nama_objek = 'gardu'
                  LEFT JOIN sutm s ON dp.id_sub_kategori = s.id_sutm AND dp.nama_objek = 'sutm'
                  ORDER BY dp.tanggal DESC";
        
        $result = mysqli_query($this->db, $query);
        if (!$result) {
            throw new Exception("Query error: " . mysqli_error($this->db));
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    public function performKMeansClustering($data, $k = 3, $maxIterations = 100) {
        if (empty($data) || $k <= 0) {
            throw new Exception("Data kosong atau nilai K tidak valid");
        }
        
        $features = [];
        $featureNames = [
            't1_inspeksi', 't1_realisasi', 't2_inspeksi', 't2_realisasi',
            'gardu_pengukuran', 'gardu_arrester', 'gardu_fco', 'gardu_grounding',
            'sutm_pangkas_kms', 'sutm_pangkas_batang', 'sutm_tebang', 'sutm_pin_isolator',
            'sutm_arrester', 'sutm_fco', 'sutm_grounding',
            'cover_isolasi', 'penghalang_panjat', 'ultrasonik'
        ];
        
        foreach ($data as $row) {
            $featureVector = [];
            foreach ($featureNames as $feature) {
                $featureVector[] = floatval($row[$feature] ?? 0);
            }
            $features[] = $featureVector;
        }
        
        if (empty($features)) {
            throw new Exception("Gagal mengekstrak features dari data");
        }
        
        $normalizedFeatures = $this->normalizeFeatures($features);
        $centroids = $this->initializeCentroids($normalizedFeatures, $k);
        $assignments = array_fill(0, count($normalizedFeatures), 0);
        
        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            $newAssignments = [];
            
            foreach ($normalizedFeatures as $point) {
                $minDistance = PHP_FLOAT_MAX;
                $closestCentroid = 0;
                
                for ($c = 0; $c < $k; $c++) {
                    $distance = $this->euclideanDistance($point, $centroids[$c]);
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $closestCentroid = $c;
                    }
                }
                $newAssignments[] = $closestCentroid;
            }
            
            if ($newAssignments === $assignments) {
                break;
            }
            
            $assignments = $newAssignments;
            
            $newCentroids = [];
            for ($c = 0; $c < $k; $c++) {
                $clusterPoints = [];
                for ($i = 0; $i < count($assignments); $i++) {
                    if ($assignments[$i] === $c) {
                        $clusterPoints[] = $normalizedFeatures[$i];
                    }
                }
                
                if (!empty($clusterPoints)) {
                    $newCentroids[$c] = $this->calculateCentroid($clusterPoints);
                } else {
                    $newCentroids[$c] = $centroids[$c];
                }
            }
            $centroids = $newCentroids;
        }
        
        $result = [];
        for ($i = 0; $i < count($data); $i++) {
            $result[] = array_merge($data[$i], ['cluster_label' => $assignments[$i]]);
        }
        
        return $result;
    }
    
    private function normalizeFeatures($features) {
        if (empty($features)) return [];
        
        $featureCount = count($features[0]);
        $mins = array_fill(0, $featureCount, PHP_FLOAT_MAX);
        $maxs = array_fill(0, $featureCount, PHP_FLOAT_MIN);
        
        foreach ($features as $feature) {
            for ($i = 0; $i < $featureCount; $i++) {
                $mins[$i] = min($mins[$i], $feature[$i]);
                $maxs[$i] = max($maxs[$i], $feature[$i]);
            }
        }
        
        $normalized = [];
        foreach ($features as $feature) {
            $normalizedFeature = [];
            for ($i = 0; $i < $featureCount; $i++) {
                $range = $maxs[$i] - $mins[$i];
                if ($range > 0) {
                    $normalizedFeature[] = ($feature[$i] - $mins[$i]) / $range;
                } else {
                    $normalizedFeature[] = 0;
                }
            }
            $normalized[] = $normalizedFeature;
        }
        
        return $normalized;
    }
    
    private function initializeCentroids($features, $k) {
        if (empty($features)) return [];
        
        $centroids = [];
        $featureCount = count($features[0]);
        
        for ($i = 0; $i < $k; $i++) {
            $centroid = [];
            for ($j = 0; $j < $featureCount; $j++) {
                $centroid[] = mt_rand() / mt_getrandmax();
            }
            $centroids[] = $centroid;
        }
        
        return $centroids;
    }
    
    private function euclideanDistance($point1, $point2) {
        $sum = 0;
        for ($i = 0; $i < count($point1); $i++) {
            $sum += pow($point1[$i] - $point2[$i], 2);
        }
        return sqrt($sum);
    }
    
    private function calculateCentroid($points) {
        if (empty($points)) return [];
        
        $centroid = array_fill(0, count($points[0]), 0);
        
        foreach ($points as $point) {
            for ($i = 0; $i < count($point); $i++) {
                $centroid[$i] += $point[$i];
            }
        }
        
        $pointCount = count($points);
        for ($i = 0; $i < count($centroid); $i++) {
            $centroid[$i] /= $pointCount;
        }
        
        return $centroid;
    }
    
    public function saveClusteringResults($clusteredData) {
        if (empty($clusteredData)) {
            throw new Exception("Tidak ada data cluster untuk disimpan");
        }
        
        mysqli_begin_transaction($this->db);
        
        try {
            $clearQuery = "DELETE FROM hasil_cluster";
            $result = mysqli_query($this->db, $clearQuery);
            
            if (!$result) {
                throw new Exception("Gagal menghapus data cluster lama: " . mysqli_error($this->db));
            }
            
            $successCount = 0;
            foreach ($clusteredData as $data) {
                if (!isset($data['id_data_pemeliharaan']) || !isset($data['nama_objek']) || !isset($data['cluster_label'])) {
                    continue;
                }
                
                $id_data_pemeliharaan = intval($data['id_data_pemeliharaan']);
                $nama_objek = mysqli_real_escape_string($this->db, $data['nama_objek']);
                $cluster_label = intval($data['cluster_label']);
                
                $query = "INSERT INTO hasil_cluster (id_data_pemeliharaan, nama_objek, cluster_label) 
                         VALUES ($id_data_pemeliharaan, '$nama_objek', $cluster_label)";
                
                $result = mysqli_query($this->db, $query);
                if (!$result) {
                    throw new Exception("Gagal insert data cluster: " . mysqli_error($this->db));
                }
                $successCount++;
            }
            
            mysqli_commit($this->db);
            return $successCount;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            throw $e;
        }
    }
    
    public function getClusteringResults() {
        $query = "SELECT hc.*, dp.tanggal,
                         CASE 
                             WHEN hc.nama_objek = 'gardu' THEN g.nama_penyulang
                             WHEN hc.nama_objek = 'sutm' THEN s.nama_penyulang
                             ELSE 'Unknown'
                         END as nama_penyulang
                  FROM hasil_cluster hc
                  JOIN data_pemeliharaan dp ON hc.id_data_pemeliharaan = dp.id_data_pemeliharaan
                  LEFT JOIN gardu g ON dp.id_sub_kategori = g.id_gardu AND hc.nama_objek = 'gardu'
                  LEFT JOIN sutm s ON dp.id_sub_kategori = s.id_sutm AND hc.nama_objek = 'sutm'
                  ORDER BY hc.cluster_label, dp.tanggal DESC";
        
        $result = mysqli_query($this->db, $query);
        if (!$result) {
            throw new Exception("Query error: " . mysqli_error($this->db));
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    public function splitData($testRatio = 0.2) {
        $allData = $this->getAllMaintenanceData();
        
        if (empty($allData)) {
            throw new Exception("Tidak ada data untuk di-split");
        }
        
        shuffle($allData);
        
        $totalCount = count($allData);
        $testCount = intval($totalCount * $testRatio);
        $trainCount = $totalCount - $testCount;
        
        mysqli_begin_transaction($this->db);
        
        try {
            $clearQuery = "DELETE FROM split_data";
            $result = mysqli_query($this->db, $clearQuery);
            
            if (!$result) {
                throw new Exception("Gagal menghapus data split lama: " . mysqli_error($this->db));
            }
            
            $successCount = 0;
            
            for ($i = 0; $i < $trainCount; $i++) {
                if (!isset($allData[$i]['id_data_pemeliharaan']) || !isset($allData[$i]['nama_objek'])) {
                    continue;
                }
                
                $id_data_pemeliharaan = intval($allData[$i]['id_data_pemeliharaan']);
                $nama_objek = mysqli_real_escape_string($this->db, $allData[$i]['nama_objek']);
                
                $query = "INSERT INTO split_data (id_data_pemeliharaan, nama_objek, tipe_data) 
                         VALUES ($id_data_pemeliharaan, '$nama_objek', 'train')";
                
                $result = mysqli_query($this->db, $query);
                if (!$result) {
                    throw new Exception("Gagal insert data training: " . mysqli_error($this->db));
                }
                $successCount++;
            }
            
            for ($i = $trainCount; $i < $totalCount; $i++) {
                if (!isset($allData[$i]['id_data_pemeliharaan']) || !isset($allData[$i]['nama_objek'])) {
                    continue;
                }
                
                $id_data_pemeliharaan = intval($allData[$i]['id_data_pemeliharaan']);
                $nama_objek = mysqli_real_escape_string($this->db, $allData[$i]['nama_objek']);
                
                $query = "INSERT INTO split_data (id_data_pemeliharaan, nama_objek, tipe_data) 
                         VALUES ($id_data_pemeliharaan, '$nama_objek', 'test')";
                
                $result = mysqli_query($this->db, $query);
                if (!$result) {
                    throw new Exception("Gagal insert data testing: " . mysqli_error($this->db));
                }
                $successCount++;
            }
            
            mysqli_commit($this->db);
            
            return [
                'success' => true,
                'total_data' => $totalCount,
                'train_count' => $trainCount,
                'test_count' => $testCount,
                'saved_count' => $successCount
            ];
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            throw $e;
        }
    }
    
    public function getSplitDataResults() {
        $query = "SELECT sd.*, dp.tanggal,
                         CASE 
                             WHEN sd.nama_objek = 'gardu' THEN g.nama_penyulang
                             WHEN sd.nama_objek = 'sutm' THEN s.nama_penyulang
                             ELSE 'Unknown'
                         END as nama_penyulang
                  FROM split_data sd
                  JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
                  LEFT JOIN gardu g ON dp.id_sub_kategori = g.id_gardu AND sd.nama_objek = 'gardu'
                  LEFT JOIN sutm s ON dp.id_sub_kategori = s.id_sutm AND sd.nama_objek = 'sutm'
                  ORDER BY sd.tipe_data, dp.tanggal DESC";
        
        $result = mysqli_query($this->db, $query);
        if (!$result) {
            throw new Exception("Query error: " . mysqli_error($this->db));
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    public function getClusterStats() {
        $query = "SELECT 
                      cluster_label,
                      COUNT(*) as count,
                      ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM hasil_cluster), 2) as percentage
                  FROM hasil_cluster 
                  GROUP BY cluster_label
                  ORDER BY cluster_label";
        
        $result = mysqli_query($this->db, $query);
        if (!$result) {
            return [];
        }
        
        $stats = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $stats[] = $row;
        }
        return $stats;
    }
    
    public function getSplitDataStats() {
        $query = "SELECT 
                      tipe_data,
                      COUNT(*) as count,
                      ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM split_data), 2) as percentage
                  FROM split_data 
                  GROUP BY tipe_data";
        
        $result = mysqli_query($this->db, $query);
        if (!$result) {
            return [];
        }
        
        $stats = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $stats[] = $row;
        }
        return $stats;
    }
    
    public function testConnection() {
        $query = "SELECT 1";
        $result = mysqli_query($this->db, $query);
        return $result !== false;
    }
}
?>