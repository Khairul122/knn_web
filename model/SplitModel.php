<?php
include_once 'koneksi.php';

class SplitModel {
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
        
        if (!$this->db) {
            die("Database connection failed. Please check koneksi.php file.");
        }
    }

    public function getAllClusterData()
    {
        $query = "
            SELECT 
                hc.id_cluster,
                hc.id_data_pemeliharaan,
                hc.cluster_label,
                hc.risk_score,
                hc.tingkat_risiko,
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
            ORDER BY hc.tingkat_risiko, hc.id_cluster
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

    private function validateDataQuality($data)
    {
        $validation = [
            'is_valid' => true,
            'warnings' => [],
            'errors' => [],
            'class_distribution' => [],
            'recommendations' => []
        ];

        if (empty($data)) {
            $validation['is_valid'] = false;
            $validation['errors'][] = "Tidak ada data cluster untuk divalidasi";
            return $validation;
        }

        $classCount = [];
        $totalData = count($data);
        
        foreach ($data as $row) {
            $risk = strtoupper($row['tingkat_risiko']);
            if (!isset($classCount[$risk])) {
                $classCount[$risk] = 0;
            }
            $classCount[$risk]++;
        }

        $validation['class_distribution'] = $classCount;

        foreach ($classCount as $class => $count) {
            $percentage = ($count / $totalData) * 100;
            
            if ($count < 3) {
                $validation['errors'][] = "Kelas $class hanya memiliki $count data (< 3) - tidak dapat digunakan untuk training";
                $validation['is_valid'] = false;
            } elseif ($count < 10) {
                $validation['warnings'][] = "Kelas $class hanya memiliki $count data (< 10) - risiko overfitting tinggi";
            }
            
            if ($percentage < 5) {
                $validation['warnings'][] = "Kelas $class hanya " . round($percentage, 1) . "% dari total data - ketimpangan kelas tinggi";
            }
        }

        if ($totalData < 30) {
            $validation['warnings'][] = "Total data $totalData terlalu sedikit untuk cross validation yang reliable (minimum 30)";
        }

        $minClass = min($classCount);
        $maxClass = max($classCount);
        $imbalanceRatio = $maxClass / $minClass;
        
        if ($imbalanceRatio > 5) {
            $validation['warnings'][] = "Ketimpangan kelas sangat tinggi (rasio: " . round($imbalanceRatio, 2) . ":1)";
            $validation['recommendations'][] = "Gunakan stratified sampling dan teknik resampling";
        } elseif ($imbalanceRatio > 2) {
            $validation['warnings'][] = "Ketimpangan kelas sedang (rasio: " . round($imbalanceRatio, 2) . ":1)";
        }

        if ($validation['is_valid']) {
            $validation['recommendations'][] = "Data siap untuk training dengan stratified split";
            if ($totalData >= 100) {
                $validation['recommendations'][] = "Gunakan k-fold cross validation (k=5 atau k=10) untuk evaluasi";
            }
        }

        return $validation;
    }

    private function performCrossValidation($data, $folds = 5)
    {
        if (count($data) < $folds * 2) {
            throw new Exception("Data terlalu sedikit untuk $folds-fold cross validation (minimum: " . ($folds * 2) . ")");
        }

        $groupedData = [];
        foreach ($data as $row) {
            $risk = strtoupper($row['tingkat_risiko']);
            if (!isset($groupedData[$risk])) {
                $groupedData[$risk] = [];
            }
            $groupedData[$risk][] = $row;
        }

        foreach ($groupedData as $risk => $classData) {
            if (count($classData) < $folds) {
                throw new Exception("Kelas $risk hanya memiliki " . count($classData) . " data, tidak cukup untuk $folds-fold CV");
            }
        }

        $foldData = array_fill(0, $folds, []);
        
        foreach ($groupedData as $risk => $classData) {
            shuffle($classData);
            $classSize = count($classData);
            
            for ($i = 0; $i < $classSize; $i++) {
                $foldIndex = $i % $folds;
                $foldData[$foldIndex][] = $classData[$i];
            }
        }

        $validationResults = [];
        $totalSamples = count($data);
        
        for ($fold = 0; $fold < $folds; $fold++) {
            $testFold = $foldData[$fold];
            $trainFolds = [];
            
            for ($i = 0; $i < $folds; $i++) {
                if ($i !== $fold) {
                    $trainFolds = array_merge($trainFolds, $foldData[$i]);
                }
            }
            
            $validationResults[$fold] = [
                'train_size' => count($trainFolds),
                'test_size' => count($testFold),
                'train_ratio' => round(count($trainFolds) / $totalSamples, 3)
            ];
        }

        $avgTrainSize = array_sum(array_column($validationResults, 'train_size')) / $folds;
        $avgTestSize = array_sum(array_column($validationResults, 'test_size')) / $folds;

        return [
            'folds' => $folds,
            'total_data' => $totalSamples,
            'fold_results' => $validationResults,
            'avg_train_size' => round($avgTrainSize),
            'avg_test_size' => round($avgTestSize)
        ];
    }

    public function validateTrainingData($performCV = true, $cvFolds = 5)
    {
        try {
            $data = $this->getAllClusterData();
            $validation = $this->validateDataQuality($data);
            
            $result = [
                'data_quality' => $validation,
                'total_samples' => count($data),
                'cross_validation' => null
            ];
            
            if ($performCV && $validation['is_valid'] && count($data) >= 20) {
                try {
                    $cvResults = $this->performCrossValidation($data, $cvFolds);
                    $result['cross_validation'] = $cvResults;
                } catch (Exception $e) {
                    $result['cross_validation'] = [
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'data_quality' => [
                    'is_valid' => false,
                    'errors' => [$e->getMessage()]
                ]
            ];
        }
    }

    private function stratifiedSplit($data, $trainRatio = 0.8)
    {
        $groupedData = [];

        foreach ($data as $row) {
            $tingkatRisiko = strtoupper($row['tingkat_risiko']);
            if (!isset($groupedData[$tingkatRisiko])) {
                $groupedData[$tingkatRisiko] = [];
            }
            $groupedData[$tingkatRisiko][] = $row;
        }

        $trainData = [];
        $testData = [];
        $splitInfo = [];

        foreach ($groupedData as $tingkatRisiko => $classData) {
            $classTotal = count($classData);
            
            if ($classTotal < 4) {
                throw new Exception("Kelas $tingkatRisiko hanya memiliki $classTotal data (minimum 4 untuk split)");
            }

            mt_srand(42);
            shuffle($classData);

            $classTrainSize = max(2, (int)($classTotal * $trainRatio));
            $classTestSize = $classTotal - $classTrainSize;

            if ($classTestSize < 1) {
                $classTrainSize = $classTotal - 1;
                $classTestSize = 1;
            }

            $classTrain = array_slice($classData, 0, $classTrainSize);
            $classTest = array_slice($classData, $classTrainSize);

            $trainData = array_merge($trainData, $classTrain);
            $testData = array_merge($testData, $classTest);

            $splitInfo[$tingkatRisiko] = [
                'total' => $classTotal,
                'train' => count($classTrain),
                'test' => count($classTest),
                'train_percentage' => round((count($classTrain) / $classTotal) * 100, 2),
                'test_percentage' => round((count($classTest) / $classTotal) * 100, 2)
            ];
        }

        return [
            'train_data' => $trainData,
            'test_data' => $testData,
            'split_info' => $splitInfo
        ];
    }

    public function splitData80_20()
    {
        try {
            $this->db->begin_transaction();
            
            $this->clearSplitData();
            
            $data = $this->getAllClusterData();
            
            if (empty($data)) {
                throw new Exception("Tidak ada data cluster untuk di-split. Silakan lakukan clustering terlebih dahulu.");
            }
            
            $validation = $this->validateDataQuality($data);
            if (!$validation['is_valid']) {
                throw new Exception("Validasi data gagal: " . implode(", ", $validation['errors']));
            }
            
            $splitResult = $this->stratifiedSplit($data, 0.8);
            $trainData = $splitResult['train_data'];
            $testData = $splitResult['test_data'];
            
            $insertQuery = "INSERT INTO split_data (id_data_pemeliharaan, nama_objek, tipe_data, id_cluster) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($insertQuery);
            
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $this->db->error);
            }
            
            $successCount = 0;
            $totalData = count($data);
            
            $trainType = 'train';
            foreach ($trainData as $row) {
                $idData = $row['id_data_pemeliharaan'];
                $namaObjek = $row['nama_objek'];
                $idCluster = $row['id_cluster'];
                
                $stmt->bind_param("issi", $idData, $namaObjek, $trainType, $idCluster);
                if ($stmt->execute()) {
                    $successCount++;
                }
            }
            
            $testType = 'test';
            foreach ($testData as $row) {
                $idData = $row['id_data_pemeliharaan'];
                $namaObjek = $row['nama_objek'];
                $idCluster = $row['id_cluster'];
                
                $stmt->bind_param("issi", $idData, $namaObjek, $testType, $idCluster);
                if ($stmt->execute()) {
                    $successCount++;
                }
            }
            
            $stmt->close();
            
            if ($successCount == $totalData) {
                $this->db->commit();
                
                $warningMessage = '';
                if (!empty($validation['warnings'])) {
                    $warningMessage = ' | Peringatan: ' . implode(", ", $validation['warnings']);
                }
                
                return [
                    'success' => true,
                    'message' => 'Stratified split data berhasil dilakukan dengan validasi' . $warningMessage,
                    'total_data' => $totalData,
                    'train_data' => count($trainData),
                    'test_data' => count($testData),
                    'train_percentage' => round((count($trainData) / $totalData) * 100, 2),
                    'test_percentage' => round((count($testData) / $totalData) * 100, 2),
                    'validation_info' => $validation
                ];
            } else {
                $this->db->rollback();
                throw new Exception("Gagal menyimpan semua data split");
            }
            
        } catch (Exception $e) {
            if ($this->db) {
                $this->db->rollback();
            }
            return [
                'success' => false,
                'message' => 'Split data gagal: ' . $e->getMessage()
            ];
        }
    }

    public function clearSplitData()
    {
        $query = "DELETE FROM split_data";
        $result = $this->db->query($query);
        
        if (!$result) {
            throw new Exception("Failed to clear split data: " . $this->db->error);
        }
        
        $resetQuery = "ALTER TABLE split_data AUTO_INCREMENT = 1";
        $this->db->query($resetQuery);
        
        return true;
    }

    public function getSplitData($limit = null)
    {
        $limitClause = $limit ? "LIMIT " . intval($limit) : "";
        
        $query = "
            SELECT 
                sd.*,
                dp.tanggal,
                hc.cluster_label,
                hc.risk_score,
                hc.tingkat_risiko,
                CASE 
                    WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                    WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                    ELSE 'Unknown'
                END as nama_penyulang
            FROM split_data sd
            JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
            LEFT JOIN hasil_cluster hc ON sd.id_cluster = hc.id_cluster
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            ORDER BY sd.tipe_data, hc.tingkat_risiko, sd.id_data_pemeliharaan
            {$limitClause}
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get split data: " . $this->db->error);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function getSplitStatistics()
    {
        $query = "
            SELECT 
                COUNT(*) as total_data,
                SUM(CASE WHEN sd.tipe_data = 'train' THEN 1 ELSE 0 END) as train_count,
                SUM(CASE WHEN sd.tipe_data = 'test' THEN 1 ELSE 0 END) as test_count,
                SUM(CASE WHEN dp.nama_objek = 'gardu' THEN 1 ELSE 0 END) as gardu_count,
                SUM(CASE WHEN dp.nama_objek = 'sutm' THEN 1 ELSE 0 END) as sutm_count
            FROM split_data sd
            JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
            LEFT JOIN hasil_cluster hc ON sd.id_cluster = hc.id_cluster
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get split statistics: " . $this->db->error);
        }
        
        $stats = $result->fetch_assoc();
        
        if ($stats && $stats['total_data'] > 0) {
            $stats['train_percentage'] = round(($stats['train_count'] / $stats['total_data']) * 100, 2);
            $stats['test_percentage'] = round(($stats['test_count'] / $stats['total_data']) * 100, 2);
        } else {
            $stats = [
                'total_data' => 0,
                'train_count' => 0,
                'test_count' => 0,
                'gardu_count' => 0,
                'sutm_count' => 0,
                'train_percentage' => 0,
                'test_percentage' => 0
            ];
        }
        
        $detailQuery = "
            SELECT 
                sd.tipe_data,
                hc.tingkat_risiko,
                COUNT(*) as count
            FROM split_data sd
            JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
            LEFT JOIN hasil_cluster hc ON sd.id_cluster = hc.id_cluster
            GROUP BY sd.tipe_data, hc.tingkat_risiko
            ORDER BY hc.tingkat_risiko, sd.tipe_data
        ";
        
        $detailResult = $this->db->query($detailQuery);
        $classDistribution = [];
        
        if ($detailResult) {
            while ($row = $detailResult->fetch_assoc()) {
                $tingkatRisiko = $row['tingkat_risiko'] ?: 'UNKNOWN';
                if (!isset($classDistribution[$tingkatRisiko])) {
                    $classDistribution[$tingkatRisiko] = [
                        'train' => 0,
                        'test' => 0,
                        'total' => 0,
                        'train_percentage' => 0,
                        'test_percentage' => 0
                    ];
                }
                
                $classDistribution[$tingkatRisiko][$row['tipe_data']] = $row['count'];
                $classDistribution[$tingkatRisiko]['total'] += $row['count'];
            }
            
            foreach ($classDistribution as $class => &$dist) {
                if ($dist['total'] > 0) {
                    $dist['train_percentage'] = round(($dist['train'] / $dist['total']) * 100, 2);
                    $dist['test_percentage'] = round(($dist['test'] / $dist['total']) * 100, 2);
                }
            }
        }
        
        return [
            'total_data' => $stats['total_data'],
            'train_count' => $stats['train_count'],
            'test_count' => $stats['test_count'],
            'gardu_count' => $stats['gardu_count'],
            'sutm_count' => $stats['sutm_count'],
            'train_percentage' => $stats['train_percentage'],
            'test_percentage' => $stats['test_percentage'],
            'class_distribution' => $classDistribution
        ];
    }

    public function resetAllSplitData()
    {
        try {
            $this->db->begin_transaction();
            
            $queries = [
                "DELETE FROM split_data",
                "ALTER TABLE split_data AUTO_INCREMENT = 1"
            ];
            
            foreach ($queries as $query) {
                if (!$this->db->query($query)) {
                    throw new Exception("Reset query failed: " . $this->db->error);
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Data split berhasil direset'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Reset gagal: ' . $e->getMessage()
            ];
        }
    }
}
?>