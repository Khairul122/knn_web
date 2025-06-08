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
            shuffle($classData);
            
            $classTotal = count($classData);
            $classTrainSize = (int)($classTotal * $trainRatio);
            
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
            
            $splitResult = $this->stratifiedSplit($data, 0.8);
            $trainData = $splitResult['train_data'];
            $testData = $splitResult['test_data'];
            $splitInfo = $splitResult['split_info'];
            
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
                return [
                    'success' => true,
                    'message' => 'Stratified split data berhasil dilakukan per tingkat risiko',
                    'total_data' => $totalData,
                    'train_data' => count($trainData),
                    'test_data' => count($testData),
                    'train_percentage' => round((count($trainData) / $totalData) * 100, 2),
                    'test_percentage' => round((count($testData) / $totalData) * 100, 2),
                    'stratified_info' => $splitInfo
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

    public function getTrainData()
    {
        $query = "
            SELECT sd.*, dp.tanggal,
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
            WHERE sd.tipe_data = 'train'
            ORDER BY hc.tingkat_risiko, sd.id_data_pemeliharaan
        ";
        
        return $this->executeQuery($query);
    }

    public function getTestData()
    {
        $query = "
            SELECT sd.*, dp.tanggal,
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
            WHERE sd.tipe_data = 'test'
            ORDER BY hc.tingkat_risiko, sd.id_data_pemeliharaan
        ";
        
        return $this->executeQuery($query);
    }

    private function executeQuery($query)
    {
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Query execution failed: " . $this->db->error);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function getClassBalance()
    {
        $query = "
            SELECT 
                hc.tingkat_risiko,
                sd.tipe_data,
                COUNT(*) as count
            FROM split_data sd
            LEFT JOIN hasil_cluster hc ON sd.id_cluster = hc.id_cluster
            GROUP BY hc.tingkat_risiko, sd.tipe_data
            ORDER BY hc.tingkat_risiko, sd.tipe_data
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get class balance: " . $this->db->error);
        }
        
        $classBalance = [];
        $totalTrain = 0;
        $totalTest = 0;
        
        while ($row = $result->fetch_assoc()) {
            $tingkatRisiko = $row['tingkat_risiko'];
            $tipeData = $row['tipe_data'];
            $count = $row['count'];
            
            if (!isset($classBalance[$tingkatRisiko])) {
                $classBalance[$tingkatRisiko] = [
                    'train' => 0,
                    'test' => 0,
                    'total' => 0
                ];
            }
            
            $classBalance[$tingkatRisiko][$tipeData] = $count;
            $classBalance[$tingkatRisiko]['total'] += $count;
            
            if ($tipeData == 'train') {
                $totalTrain += $count;
            } else {
                $totalTest += $count;
            }
        }
        
        foreach ($classBalance as $class => &$balance) {
            if ($balance['total'] > 0) {
                $balance['train_ratio'] = round($balance['train'] / $balance['total'], 3);
                $balance['test_ratio'] = round($balance['test'] / $balance['total'], 3);
            }
            
            if ($totalTrain > 0) {
                $balance['train_distribution'] = round(($balance['train'] / $totalTrain) * 100, 2);
            }
            
            if ($totalTest > 0) {
                $balance['test_distribution'] = round(($balance['test'] / $totalTest) * 100, 2);
            }
        }
        
        return [
            'class_balance' => $classBalance,
            'total_train' => $totalTrain,
            'total_test' => $totalTest,
            'overall_ratio' => $totalTrain + $totalTest > 0 ? round($totalTrain / ($totalTrain + $totalTest), 3) : 0
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