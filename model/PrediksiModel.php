<?php
include_once 'koneksi.php';

class PrediksiModel {
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
        
        if (!$this->db) {
            die("Database connection failed. Please check koneksi.php file.");
        }
    }

    public function getTrainingData()
    {
        $query = "
            SELECT 
                dp.nama_objek,
                hc.cluster_label,
                hc.risk_score,
                hc.tingkat_risiko,
                CASE 
                    WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                    WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                    ELSE 'Unknown'
                END as nama_penyulang,
                CASE 
                    WHEN dp.nama_objek = 'gardu' THEN 
                        (g.t1_inspeksi + g.t1_realisasi + g.t2_inspeksi + g.t2_realisasi + 
                         g.pengukuran + g.pergantian_arrester + g.pergantian_fco + 
                         g.relokasi_gardu + g.pembangunan_gardu_siapan + g.penyimbang_beban_gardu + 
                         g.pemecahan_beban_gardu + g.perubahan_tap_charger_trafo + g.pergantian_box + 
                         g.pergantian_opstic + g.perbaikan_grounding + g.accesoris_gardu + 
                         g.pergantian_kabel_isolasi + g.pemasangan_cover_isolasi + 
                         g.pemasangan_penghalang_panjat + g.alat_ultrasonik)
                    WHEN dp.nama_objek = 'sutm' THEN 
                        (s.t1_inspeksi + s.t1_realisasi + s.t2_inspeksi + s.t2_realisasi + 
                         s.pangkas_kms + s.pangkas_batang + s.tebang + s.pin_isolator + 
                         s.suspension_isolator + s.traves_dan_armtie + s.tiang + s.accesoris_sutm + 
                         s.arrester_sutm + s.fco_sutm + s.grounding_sutm + s.perbaikan_andong_kendor + 
                         s.kawat_terburai + s.jamperan_sutm + s.skur + s.ganti_kabel_isolasi + 
                         s.pemasangan_cover_isolasi + s.pemasangan_penghalang_panjang + s.alat_ultrasonik)
                    ELSE 0
                END as total_kegiatan
            FROM split_data sd
            JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
            JOIN hasil_cluster hc ON sd.id_cluster = hc.id_cluster
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            WHERE sd.tipe_data = 'train'
            ORDER BY sd.id_split
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get training data: " . $this->db->error);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function trainKNN($k_value)
    {
        try {
            $this->db->begin_transaction();
            
            $this->clearPrediksiData();
            
            $trainingData = $this->getTrainingData();
            
            if (empty($trainingData)) {
                throw new Exception("Tidak ada data training. Silakan lakukan split data terlebih dahulu.");
            }
            
            $penyulangStats = [];
            
            foreach ($trainingData as $data) {
                $penyulang = $data['nama_penyulang'];
                
                if (!isset($penyulangStats[$penyulang])) {
                    $penyulangStats[$penyulang] = [
                        'tinggi' => 0,
                        'sedang' => 0,
                        'rendah' => 0,
                        'total_kegiatan' => 0,
                        'total_risk_score' => 0,
                        'count' => 0
                    ];
                }
                
                $tingkatRisiko = strtolower($data['tingkat_risiko']);
                $penyulangStats[$penyulang][$tingkatRisiko]++;
                $penyulangStats[$penyulang]['total_kegiatan'] += (float)$data['total_kegiatan'];
                $penyulangStats[$penyulang]['total_risk_score'] += (float)$data['risk_score'];
                $penyulangStats[$penyulang]['count']++;
            }
            
            $insertQuery = "INSERT INTO hasil_prediksi_risiko (nama_penyulang, tingkat_risiko, nilai_risiko, total_kegiatan, k_value) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($insertQuery);
            
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $this->db->error);
            }
            
            $successCount = 0;
            
            foreach ($penyulangStats as $penyulang => $stats) {
                $avgRiskScore = $stats['total_risk_score'] / $stats['count'];
                $avgTotalKegiatan = (int)($stats['total_kegiatan'] / $stats['count']);
                
                $maxCount = max($stats['tinggi'], $stats['sedang'], $stats['rendah']);
                
                if ($stats['tinggi'] == $maxCount) {
                    $predictedRisk = 'TINGGI';
                } elseif ($stats['sedang'] == $maxCount) {
                    $predictedRisk = 'SEDANG';
                } else {
                    $predictedRisk = 'RENDAH';
                }
                
                $stmt->bind_param("ssdii", $penyulang, $predictedRisk, $avgRiskScore, $avgTotalKegiatan, $k_value);
                
                if ($stmt->execute()) {
                    $successCount++;
                }
            }
            
            $stmt->close();
            
            if ($successCount > 0) {
                $this->db->commit();
                return [
                    'success' => true,
                    'message' => 'Training KNN berhasil dilakukan',
                    'total_penyulang' => $successCount,
                    'k_value' => $k_value,
                    'training_data_count' => count($trainingData)
                ];
            } else {
                $this->db->rollback();
                throw new Exception("Gagal menyimpan hasil prediksi");
            }
            
        } catch (Exception $e) {
            if ($this->db) {
                $this->db->rollback();
            }
            return [
                'success' => false,
                'message' => 'Training KNN gagal: ' . $e->getMessage()
            ];
        }
    }

    public function getPrediksiData()
    {
        $query = "
            SELECT 
                id_prediksi,
                nama_penyulang,
                tingkat_risiko,
                nilai_risiko,
                total_kegiatan,
                k_value,
                tanggal_prediksi
            FROM hasil_prediksi_risiko
            ORDER BY tanggal_prediksi DESC, nama_penyulang
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get prediction data: " . $this->db->error);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function getPrediksiStatistics()
    {
        $query = "
            SELECT 
                COUNT(*) as total_prediksi,
                SUM(CASE WHEN tingkat_risiko = 'TINGGI' THEN 1 ELSE 0 END) as tinggi_count,
                SUM(CASE WHEN tingkat_risiko = 'SEDANG' THEN 1 ELSE 0 END) as sedang_count,
                SUM(CASE WHEN tingkat_risiko = 'RENDAH' THEN 1 ELSE 0 END) as rendah_count,
                AVG(nilai_risiko) as avg_risk_score,
                AVG(total_kegiatan) as avg_total_kegiatan,
                MAX(k_value) as last_k_value
            FROM hasil_prediksi_risiko
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get prediction statistics: " . $this->db->error);
        }
        
        $stats = $result->fetch_assoc();
        
        if ($stats && $stats['total_prediksi'] > 0) {
            $stats['tinggi_percentage'] = round(($stats['tinggi_count'] / $stats['total_prediksi']) * 100, 2);
            $stats['sedang_percentage'] = round(($stats['sedang_count'] / $stats['total_prediksi']) * 100, 2);
            $stats['rendah_percentage'] = round(($stats['rendah_count'] / $stats['total_prediksi']) * 100, 2);
            $stats['avg_risk_score'] = round($stats['avg_risk_score'], 2);
            $stats['avg_total_kegiatan'] = round($stats['avg_total_kegiatan'], 0);
        } else {
            $stats = [
                'total_prediksi' => 0,
                'tinggi_count' => 0,
                'sedang_count' => 0,
                'rendah_count' => 0,
                'tinggi_percentage' => 0,
                'sedang_percentage' => 0,
                'rendah_percentage' => 0,
                'avg_risk_score' => 0,
                'avg_total_kegiatan' => 0,
                'last_k_value' => 3
            ];
        }
        
        return $stats;
    }

    public function getConfusionMatrix()
    {
        $trainingData = $this->getTrainingData();
        $prediksiData = $this->getPrediksiData();
        
        if (empty($trainingData) || empty($prediksiData)) {
            return [
                'matrix' => [],
                'accuracy' => 0,
                'precision' => [],
                'recall' => [],
                'f1_score' => []
            ];
        }
        
        $actualByPenyulang = [];
        foreach ($trainingData as $data) {
            $penyulang = $data['nama_penyulang'];
            if (!isset($actualByPenyulang[$penyulang])) {
                $actualByPenyulang[$penyulang] = [];
            }
            $actualByPenyulang[$penyulang][] = strtoupper($data['tingkat_risiko']);
        }
        
        $predictedByPenyulang = [];
        foreach ($prediksiData as $data) {
            $predictedByPenyulang[$data['nama_penyulang']] = $data['tingkat_risiko'];
        }
        
        $classes = ['RENDAH', 'SEDANG', 'TINGGI'];
        $matrix = array_fill_keys($classes, array_fill_keys($classes, 0));
        $total = 0;
        $correct = 0;
        
        foreach ($actualByPenyulang as $penyulang => $actualClasses) {
            if (isset($predictedByPenyulang[$penyulang])) {
                $predicted = $predictedByPenyulang[$penyulang];
                $mostFrequentActual = array_count_values($actualClasses);
                arsort($mostFrequentActual);
                $actual = array_key_first($mostFrequentActual);
                
                $matrix[$actual][$predicted]++;
                $total++;
                
                if ($actual === $predicted) {
                    $correct++;
                }
            }
        }
        
        $accuracy = $total > 0 ? round(($correct / $total) * 100, 2) : 0;
        
        $precision = [];
        $recall = [];
        $f1_score = [];
        
        foreach ($classes as $class) {
            $tp = $matrix[$class][$class];
            $fp = array_sum(array_column($matrix, $class)) - $tp;
            $fn = array_sum($matrix[$class]) - $tp;
            
            $precision[$class] = ($tp + $fp) > 0 ? round(($tp / ($tp + $fp)) * 100, 2) : 0;
            $recall[$class] = ($tp + $fn) > 0 ? round(($tp / ($tp + $fn)) * 100, 2) : 0;
            
            $f1_score[$class] = ($precision[$class] + $recall[$class]) > 0 
                ? round((2 * $precision[$class] * $recall[$class]) / ($precision[$class] + $recall[$class]), 2) 
                : 0;
        }
        
        return [
            'matrix' => $matrix,
            'accuracy' => $accuracy,
            'precision' => $precision,
            'recall' => $recall,
            'f1_score' => $f1_score,
            'classes' => $classes
        ];
    }

    public function getOverfittingAnalysis()
    {
        $trainingData = $this->getTrainingData();
        $prediksiData = $this->getPrediksiData();
        
        if (empty($trainingData) || empty($prediksiData)) {
            return ['status' => 'no_data'];
        }
        
        $trainDistribution = ['RENDAH' => 0, 'SEDANG' => 0, 'TINGGI' => 0];
        $predDistribution = ['RENDAH' => 0, 'SEDANG' => 0, 'TINGGI' => 0];
        
        foreach ($trainingData as $data) {
            $risk = strtoupper($data['tingkat_risiko']);
            if (isset($trainDistribution[$risk])) {
                $trainDistribution[$risk]++;
            }
        }
        
        foreach ($prediksiData as $data) {
            $risk = strtoupper($data['tingkat_risiko']);
            if (isset($predDistribution[$risk])) {
                $predDistribution[$risk]++;
            }
        }
        
        $riskScores = array_column($prediksiData, 'nilai_risiko');
        $meanRisk = array_sum($riskScores) / count($riskScores);
        $variance = 0;
        foreach ($riskScores as $score) {
            $variance += pow($score - $meanRisk, 2);
        }
        $variance = $variance / count($riskScores);
        $stdDev = sqrt($variance);
        
        $penyulangAnalysis = [];
        foreach ($prediksiData as $pred) {
            $penyulang = $pred['nama_penyulang'];
            $trainRecords = array_filter($trainingData, function($train) use ($penyulang) {
                return $train['nama_penyulang'] === $penyulang;
            });
            
            $penyulangAnalysis[$penyulang] = [
                'predicted_risk' => $pred['tingkat_risiko'],
                'train_record_count' => count($trainRecords),
                'risk_distribution' => ['RENDAH' => 0, 'SEDANG' => 0, 'TINGGI' => 0]
            ];
            
            foreach ($trainRecords as $record) {
                $risk = strtoupper($record['tingkat_risiko']);
                if (isset($penyulangAnalysis[$penyulang]['risk_distribution'][$risk])) {
                    $penyulangAnalysis[$penyulang]['risk_distribution'][$risk]++;
                }
            }
        }
        
        $overfittingScore = 0;
        $warnings = [];
        
        $confusionMatrix = $this->getConfusionMatrix();
        if ($confusionMatrix['accuracy'] > 95) {
            $overfittingScore += 30;
            $warnings[] = 'Accuracy terlalu tinggi (' . $confusionMatrix['accuracy'] . '%) - kemungkinan overfitting';
        }
        
        if ($stdDev < 5) {
            $overfittingScore += 20;
            $warnings[] = 'Variance prediksi terlalu rendah (σ=' . round($stdDev, 2) . ') - model mungkin terlalu simple';
        }
        
        $unbalancedCount = 0;
        foreach ($penyulangAnalysis as $analysis) {
            if ($analysis['train_record_count'] < 5) {
                $unbalancedCount++;
            }
        }
        
        if ($unbalancedCount > count($penyulangAnalysis) * 0.3) {
            $overfittingScore += 25;
            $warnings[] = 'Banyak penyulang dengan data training sedikit (<5 records)';
        }
        
        $overfittingScore += 15;
        $warnings[] = 'Data training dan testing dari periode yang sama - potensi data leakage';
        
        $riskLevel = 'LOW';
        if ($overfittingScore > 70) {
            $riskLevel = 'HIGH';
        } elseif ($overfittingScore > 40) {
            $riskLevel = 'MEDIUM';
        }
        
        return [
            'status' => 'analyzed',
            'overfitting_score' => $overfittingScore,
            'risk_level' => $riskLevel,
            'warnings' => $warnings,
            'train_distribution' => $trainDistribution,
            'pred_distribution' => $predDistribution,
            'variance_analysis' => [
                'mean_risk' => round($meanRisk, 2),
                'std_dev' => round($stdDev, 2),
                'variance' => round($variance, 2)
            ],
            'penyulang_analysis' => $penyulangAnalysis,
            'recommendations' => $this->getOverfittingRecommendations($overfittingScore, $riskLevel)
        ];
    }

    private function getOverfittingRecommendations($score, $riskLevel)
    {
        $recommendations = [];
        
        if ($riskLevel === 'HIGH') {
            $recommendations[] = 'Gunakan Cross-Validation untuk evaluasi yang lebih robust';
            $recommendations[] = 'Pisahkan data berdasarkan periode waktu (temporal split)';
            $recommendations[] = 'Tingkatkan nilai K (coba K=5 atau K=7)';
            $recommendations[] = 'Tambahkan regularization atau feature selection';
        } elseif ($riskLevel === 'MEDIUM') {
            $recommendations[] = 'Monitor performa model pada data baru';
            $recommendations[] = 'Coba variasi nilai K yang berbeda';
            $recommendations[] = 'Validasi hasil dengan expert domain';
        } else {
            $recommendations[] = 'Model terlihat sehat, monitor terus performa';
            $recommendations[] = 'Dokumentasikan assumption dan limitation model';
        }
        
        $recommendations[] = 'Implementasikan validation set terpisah';
        $recommendations[] = 'Lakukan feature importance analysis';
        
        return $recommendations;
    }

    public function getDataStatus()
    {
        $trainingCount = 0;
        $prediksiCount = 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM split_data WHERE tipe_data = 'train'";
            $result = $this->db->query($query);
            if ($result) {
                $row = $result->fetch_assoc();
                $trainingCount = $row['count'];
            }
            
            $query2 = "SELECT COUNT(*) as count FROM hasil_prediksi_risiko";
            $result2 = $this->db->query($query2);
            if ($result2) {
                $row2 = $result2->fetch_assoc();
                $prediksiCount = $row2['count'];
            }
        } catch (Exception $e) {
        }
        
        return [
            'training_data_available' => $trainingCount > 0,
            'training_count' => $trainingCount,
            'prediction_count' => $prediksiCount,
            'ready_for_training' => $trainingCount > 0
        ];
    }

    public function clearPrediksiData()
    {
        $query = "DELETE FROM hasil_prediksi_risiko";
        $result = $this->db->query($query);
        
        if (!$result) {
            throw new Exception("Failed to clear prediction data: " . $this->db->error);
        }
        
        $resetQuery = "ALTER TABLE hasil_prediksi_risiko AUTO_INCREMENT = 1";
        $this->db->query($resetQuery);
        
        return true;
    }

    public function resetAllPrediksiData()
    {
        try {
            $this->db->begin_transaction();
            
            $queries = [
                "DELETE FROM hasil_prediksi_risiko",
                "ALTER TABLE hasil_prediksi_risiko AUTO_INCREMENT = 1"
            ];
            
            foreach ($queries as $query) {
                if (!$this->db->query($query)) {
                    throw new Exception("Reset query failed: " . $this->db->error);
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Data prediksi berhasil direset'
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