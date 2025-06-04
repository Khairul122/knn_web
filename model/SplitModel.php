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
            ORDER BY hc.id_cluster
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

    public function splitData80_20()
    {
        try {
            $this->db->begin_transaction();
            
            $this->clearSplitData();
            
            $data = $this->getAllClusterData();
            
            if (empty($data)) {
                throw new Exception("Tidak ada data cluster untuk di-split. Silakan lakukan clustering terlebih dahulu.");
            }
            
            $totalData = count($data);
            $trainSize = (int)($totalData * 0.8);
            
            shuffle($data);
            
            $trainData = array_slice($data, 0, $trainSize);
            $testData = array_slice($data, $trainSize);
            
            $insertQuery = "INSERT INTO split_data (id_data_pemeliharaan, nama_objek, tipe_data, id_cluster) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($insertQuery);
            
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $this->db->error);
            }
            
            $successCount = 0;
            
            // Fix: Create variables for bind_param references
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
                    'message' => 'Split data cluster berhasil dilakukan',
                    'total_data' => $totalData,
                    'train_data' => count($trainData),
                    'test_data' => count($testData),
                    'train_percentage' => round((count($trainData) / $totalData) * 100, 2),
                    'test_percentage' => round((count($testData) / $totalData) * 100, 2)
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
            ORDER BY sd.tipe_data, sd.id_data_pemeliharaan
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
                SUM(CASE WHEN tipe_data = 'train' THEN 1 ELSE 0 END) as train_count,
                SUM(CASE WHEN tipe_data = 'test' THEN 1 ELSE 0 END) as test_count,
                SUM(CASE WHEN nama_objek = 'gardu' THEN 1 ELSE 0 END) as gardu_count,
                SUM(CASE WHEN nama_objek = 'sutm' THEN 1 ELSE 0 END) as sutm_count
            FROM split_data
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
        
        return $stats;
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
            ORDER BY sd.id_data_pemeliharaan
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
            ORDER BY sd.id_data_pemeliharaan
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