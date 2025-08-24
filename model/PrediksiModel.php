<?php
include_once 'koneksi.php';

class PrediksiModel {
    private $db;

    public function __construct() {
        global $koneksi;
        $this->db = $koneksi;
        if (!$this->db) {
            die("Database connection failed. Please check koneksi.php file.");
        }
    }

    public function getTrainingData() {
        $query = "
            SELECT dp.nama_objek, hc.cluster_label, hc.risk_score, hc.tingkat_risiko,
                   CASE WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                        WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                        ELSE 'Unknown' END as nama_penyulang,
                   CASE WHEN dp.nama_objek = 'gardu' THEN 
                        (g.t1_inspeksi + g.t1_realisasi + g.t2_inspeksi + g.t2_realisasi + g.pengukuran + 
                         g.pergantian_arrester + g.pergantian_fco + g.relokasi_gardu + g.pembangunan_gardu_siapan + 
                         g.penyimbang_beban_gardu + g.pemecahan_beban_gardu + g.perubahan_tap_charger_trafo + 
                         g.pergantian_box + g.pergantian_opstic + g.perbaikan_grounding + g.accesoris_gardu + 
                         g.pergantian_kabel_isolasi + g.pemasangan_cover_isolasi + g.pemasangan_penghalang_panjat + g.alat_ultrasonik)
                        WHEN dp.nama_objek = 'sutm' THEN 
                        (s.t1_inspeksi + s.t1_realisasi + s.t2_inspeksi + s.t2_realisasi + s.pangkas_kms + 
                         s.pangkas_batang + s.tebang + s.pin_isolator + s.suspension_isolator + s.traves_dan_armtie + 
                         s.tiang + s.accesoris_sutm + s.arrester_sutm + s.fco_sutm + s.grounding_sutm + 
                         s.perbaikan_andong_kendor + s.kawat_terburai + s.jamperan_sutm + s.skur + s.ganti_kabel_isolasi + 
                         s.pemasangan_cover_isolasi + s.pemasangan_penghalang_panjang + s.alat_ultrasonik)
                        ELSE 0 END as total_kegiatan
            FROM split_data sd
            JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
            JOIN hasil_cluster hc ON sd.id_cluster = hc.id_cluster
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            WHERE sd.tipe_data = 'train'
            ORDER BY sd.id_split
        ";
        
        $result = $this->db->query($query);
        if (!$result) throw new Exception("Failed to get training data: " . $this->db->error);
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getTestData() {
        $query = str_replace("WHERE sd.tipe_data = 'train'", "WHERE sd.tipe_data = 'test'", 
                           $this->getTrainingDataQuery());
        
        $result = $this->db->query($query);
        if (!$result) throw new Exception("Failed to get test data: " . $this->db->error);
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    private function getTrainingDataQuery() {
        return "
            SELECT dp.nama_objek, hc.cluster_label, hc.risk_score, hc.tingkat_risiko,
                   CASE WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                        WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                        ELSE 'Unknown' END as nama_penyulang,
                   CASE WHEN dp.nama_objek = 'gardu' THEN 
                        (g.t1_inspeksi + g.t1_realisasi + g.t2_inspeksi + g.t2_realisasi + g.pengukuran + 
                         g.pergantian_arrester + g.pergantian_fco + g.relokasi_gardu + g.pembangunan_gardu_siapan + 
                         g.penyimbang_beban_gardu + g.pemecahan_beban_gardu + g.perubahan_tap_charger_trafo + 
                         g.pergantian_box + g.pergantian_opstic + g.perbaikan_grounding + g.accesoris_gardu + 
                         g.pergantian_kabel_isolasi + g.pemasangan_cover_isolasi + g.pemasangan_penghalang_panjat + g.alat_ultrasonik)
                        WHEN dp.nama_objek = 'sutm' THEN 
                        (s.t1_inspeksi + s.t1_realisasi + s.t2_inspeksi + s.t2_realisasi + s.pangkas_kms + 
                         s.pangkas_batang + s.tebang + s.pin_isolator + s.suspension_isolator + s.traves_dan_armtie + 
                         s.tiang + s.accesoris_sutm + s.arrester_sutm + s.fco_sutm + s.grounding_sutm + 
                         s.perbaikan_andong_kendor + s.kawat_terburai + s.jamperan_sutm + s.skur + s.ganti_kabel_isolasi + 
                         s.pemasangan_cover_isolasi + s.pemasangan_penghalang_panjang + s.alat_ultrasonik)
                        ELSE 0 END as total_kegiatan
            FROM split_data sd
            JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
            JOIN hasil_cluster hc ON sd.id_cluster = hc.id_cluster
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            WHERE sd.tipe_data = 'train'
            ORDER BY sd.id_split
        ";
    }

    private function calculateDistance($point1, $point2) {
        $riskDiff = $point1['risk_score'] - $point2['risk_score'];
        $activityDiff = $point1['total_kegiatan'] - $point2['total_kegiatan'];
        return sqrt(pow($riskDiff, 2) + pow($activityDiff, 2));
    }

    private function findNearestNeighbors($testPoint, $trainingData, $k) {
        $distances = [];
        foreach ($trainingData as $trainPoint) {
            $distance = $this->calculateDistance($testPoint, $trainPoint);
            $distances[] = [
                'distance' => $distance,
                'tingkat_risiko' => $trainPoint['tingkat_risiko'],
                'risk_score' => $trainPoint['risk_score']
            ];
        }
        
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        return array_slice($distances, 0, $k);
    }

    private function predictClass($neighbors) {
        $votes = ['RENDAH' => 0, 'SEDANG' => 0, 'TINGGI' => 0];
        $riskSum = 0;
        
        foreach ($neighbors as $neighbor) {
            $risk = strtoupper($neighbor['tingkat_risiko']);
            if (isset($votes[$risk])) {
                $votes[$risk]++;
            }
            $riskSum += $neighbor['risk_score'];
        }
        
        $maxVotes = max($votes);
        $predictedClass = array_search($maxVotes, $votes);
        $avgRiskScore = $riskSum / count($neighbors);
        
        return [
            'class' => $predictedClass,
            'confidence' => $maxVotes / count($neighbors),
            'avg_risk_score' => $avgRiskScore
        ];
    }

    private function generateSuggestions($tingkatRisiko, $totalKegiatan, $riskScore) {
        $suggestions = [];
        
        switch (strtoupper($tingkatRisiko)) {
            case 'RENDAH':
                $suggestions[] = "Terapkan preventive maintenance dengan inspeksi berkala";
                $suggestions[] = "Pertahankan jadwal pemeliharaan rutin";
                break;
                
            case 'SEDANG':
                $suggestions[] = "Terapkan prioritas medium dengan pemeriksaan komponen";
                $suggestions[] = "Lakukan penyimbang beban gardu untuk distribusi stabil";
                if ($totalKegiatan > 10) {
                    $suggestions[] = "Evaluasi penyebab tingginya aktivitas pemeliharaan";
                }
                break;
                
            case 'TINGGI':
                $suggestions[] = "Terapkan corrective & predictive maintenance fokus perbaikan darurat";
                $suggestions[] = "Gunakan alat monitoring real-time seperti sensor suhu";
                $suggestions[] = "Pertimbangkan relokasi atau pembangunan gardu baru";
                if ($riskScore > 8) {
                    $suggestions[] = "Segera lakukan shutdown maintenance untuk pencegahan gangguan massal";
                }
                break;
        }
        
        return implode('; ', $suggestions);
    }

    private function createFolds($data, $folds = 5) {
        shuffle($data);
        $foldSize = ceil(count($data) / $folds);
        $foldData = [];
        
        for ($i = 0; $i < $folds; $i++) {
            $foldData[$i] = array_slice($data, $i * $foldSize, $foldSize);
        }
        return $foldData;
    }

    private function crossValidateKNN($data, $k, $folds = 5) {
        if (count($data) < $folds * 2) {
            throw new Exception("Data terlalu sedikit untuk cross validation");
        }
        
        $foldData = $this->createFolds($data, $folds);
        $accuracies = [];
        
        for ($i = 0; $i < $folds; $i++) {
            $testFold = $foldData[$i];
            $trainFolds = [];
            
            for ($j = 0; $j < $folds; $j++) {
                if ($i !== $j) {
                    $trainFolds = array_merge($trainFolds, $foldData[$j]);
                }
            }
            
            if (empty($trainFolds) || empty($testFold)) continue;
            
            $correct = 0;
            $total = 0;
            
            foreach ($testFold as $testPoint) {
                $neighbors = $this->findNearestNeighbors($testPoint, $trainFolds, $k);
                $prediction = $this->predictClass($neighbors);
                
                if ($prediction['class'] === strtoupper($testPoint['tingkat_risiko'])) {
                    $correct++;
                }
                $total++;
            }
            
            $accuracies[] = $total > 0 ? ($correct / $total) * 100 : 0;
        }
        
        return count($accuracies) > 0 ? array_sum($accuracies) / count($accuracies) : 0;
    }

    public function findOptimalK($maxK = 15) {
        try {
            $trainingData = $this->getTrainingData();
            
            if (empty($trainingData)) {
                throw new Exception("Tidak ada data training untuk mencari K optimal");
            }
            
            if (count($trainingData) < 10) {
                throw new Exception("Data training terlalu sedikit untuk cross validation");
            }
            
            $kResults = [];
            $bestK = 3;
            $bestAccuracy = 0;
            
            for ($k = 3; $k <= $maxK; $k += 2) {
                $accuracy = $this->crossValidateKNN($trainingData, $k);
                $kResults[] = ['k' => $k, 'accuracy' => round($accuracy, 2)];
                
                if ($accuracy > $bestAccuracy) {
                    $bestAccuracy = $accuracy;
                    $bestK = $k;
                }
            }
            
            return [
                'success' => true,
                'optimal_k' => $bestK,
                'best_accuracy' => round($bestAccuracy, 2),
                'all_results' => $kResults,
                'data_size' => count($trainingData)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function trainKNN($k_value) {
        try {
            $this->db->begin_transaction();
            $this->clearPrediksiData();
            
            $trainingData = $this->getTrainingData();
            $testData = $this->getTestData();
            
            if (empty($trainingData)) {
                throw new Exception("Tidak ada data training. Silakan lakukan split data terlebih dahulu.");
            }
            
            if (empty($testData)) {
                throw new Exception("Tidak ada data testing. Silakan lakukan split data terlebih dahulu.");
            }
            
            $predictions = [];
            foreach ($testData as $testPoint) {
                $neighbors = $this->findNearestNeighbors($testPoint, $trainingData, $k_value);
                $prediction = $this->predictClass($neighbors);
                
                $predictions[] = [
                    'nama_penyulang' => $testPoint['nama_penyulang'],
                    'predicted' => $prediction['class'],
                    'actual' => strtoupper($testPoint['tingkat_risiko']),
                    'confidence' => $prediction['confidence'],
                    'avg_risk_score' => $prediction['avg_risk_score'],
                    'total_kegiatan' => $testPoint['total_kegiatan']
                ];
            }
            
            $aggregatedPredictions = $this->aggregatePenyulangPredictions($predictions);
            $successCount = $this->savePredictions($aggregatedPredictions, $k_value);
            
            if ($successCount > 0) {
                $this->db->commit();
                
                $finalAccuracy = $this->calculateFinalAccuracy($predictions);
                $overfittingWarning = $finalAccuracy > 95 ? 
                    ' (Perhatian: Accuracy tinggi bisa mengindikasikan overfitting)' : '';
                
                return [
                    'success' => true,
                    'message' => 'Training KNN berhasil. Final K=' . $k_value . 
                               ', Accuracy=' . round($finalAccuracy, 1) . '%' . $overfittingWarning,
                    'total_penyulang' => $successCount,
                    'k_value' => $k_value,
                    'training_data_count' => count($trainingData),
                    'test_data_count' => count($testData),
                    'accuracy' => round($finalAccuracy, 1),
                    'overfitting_check' => $finalAccuracy > 95
                ];
            } else {
                $this->db->rollback();
                throw new Exception("Gagal menyimpan hasil prediksi");
            }
            
        } catch (Exception $e) {
            if ($this->db) {
                $this->db->rollback();
            }
            return ['success' => false, 'message' => 'Training KNN gagal: ' . $e->getMessage()];
        }
    }

    private function aggregatePenyulangPredictions($predictions) {
        $penyulangGroups = [];
        
        foreach ($predictions as $prediction) {
            $penyulang = $prediction['nama_penyulang'];
            
            if (!isset($penyulangGroups[$penyulang])) {
                $penyulangGroups[$penyulang] = [
                    'predictions' => [], 'risk_scores' => [], 
                    'total_kegiatan' => [], 'confidences' => []
                ];
            }
            
            $penyulangGroups[$penyulang]['predictions'][] = $prediction['predicted'];
            $penyulangGroups[$penyulang]['risk_scores'][] = $prediction['avg_risk_score'];
            $penyulangGroups[$penyulang]['total_kegiatan'][] = $prediction['total_kegiatan'];
            $penyulangGroups[$penyulang]['confidences'][] = $prediction['confidence'];
        }
        
        $aggregatedResults = [];
        
        foreach ($penyulangGroups as $penyulang => $group) {
            $predictionCounts = array_count_values($group['predictions']);
            $finalPrediction = array_search(max($predictionCounts), $predictionCounts);
            $avgRiskScore = array_sum($group['risk_scores']) / count($group['risk_scores']);
            $avgTotalKegiatan = array_sum($group['total_kegiatan']) / count($group['total_kegiatan']);
            $avgConfidence = array_sum($group['confidences']) / count($group['confidences']);
            
            $suggestions = $this->generateSuggestions($finalPrediction, $avgTotalKegiatan, $avgRiskScore);
            
            $aggregatedResults[] = [
                'nama_penyulang' => $penyulang,
                'final_prediction' => $finalPrediction,
                'avg_risk_score' => $avgRiskScore,
                'avg_total_kegiatan' => round($avgTotalKegiatan),
                'avg_confidence' => $avgConfidence,
                'prediction_counts' => $predictionCounts,
                'total_instances' => count($group['predictions']),
                'saran_perbaikan' => $suggestions
            ];
        }
        
        return $aggregatedResults;
    }

    private function savePredictions($aggregatedPredictions, $k_value) {
        $insertQuery = "INSERT INTO hasil_prediksi_risiko (nama_penyulang, tingkat_risiko, nilai_risiko, total_kegiatan, k_value, saran_perbaikan) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($insertQuery);
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->db->error);
        }
        
        $successCount = 0;
        foreach ($aggregatedPredictions as $aggPred) {
            $stmt->bind_param("ssdiss", 
                $aggPred['nama_penyulang'], 
                $aggPred['final_prediction'], 
                $aggPred['avg_risk_score'], 
                $aggPred['avg_total_kegiatan'], 
                $k_value,
                $aggPred['saran_perbaikan']
            );
            
            if ($stmt->execute()) {
                $successCount++;
            }
        }
        
        $stmt->close();
        return $successCount;
    }

    private function calculateFinalAccuracy($predictions) {
        $correctPredictions = 0;
        foreach ($predictions as $pred) {
            if ($pred['predicted'] === $pred['actual']) {
                $correctPredictions++;
            }
        }
        return (count($predictions) > 0) ? ($correctPredictions / count($predictions)) * 100 : 0;
    }

    public function getPrediksiData() {
        $query = "
            SELECT id_prediksi, nama_penyulang, tingkat_risiko, nilai_risiko, 
                   total_kegiatan, k_value, saran_perbaikan, tanggal_prediksi
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

    public function getPrediksiStatistics() {
        $query = "
            SELECT COUNT(*) as total_prediksi,
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
                'total_prediksi' => 0, 'tinggi_count' => 0, 'sedang_count' => 0, 'rendah_count' => 0,
                'tinggi_percentage' => 0, 'sedang_percentage' => 0, 'rendah_percentage' => 0,
                'avg_risk_score' => 0, 'avg_total_kegiatan' => 0, 'last_k_value' => 3
            ];
        }
        
        return $stats;
    }

    public function getConfusionMatrix() {
        $testData = $this->getTestData();
        $prediksiData = $this->getPrediksiData();
        
        if (empty($testData) || empty($prediksiData)) {
            return ['matrix' => [], 'accuracy' => 0, 'precision' => [], 'recall' => [], 'f1_score' => []];
        }
        
        $testDataAggregated = [];
        foreach ($testData as $data) {
            $penyulang = $data['nama_penyulang'];
            $tingkatRisiko = strtoupper($data['tingkat_risiko']);
            
            if (!isset($testDataAggregated[$penyulang])) {
                $testDataAggregated[$penyulang] = [];
            }
            $testDataAggregated[$penyulang][] = $tingkatRisiko;
        }
        
        $actualByPenyulang = [];
        foreach ($testDataAggregated as $penyulang => $risks) {
            $riskCounts = array_count_values($risks);
            $actualByPenyulang[$penyulang] = array_search(max($riskCounts), $riskCounts);
        }
        
        $predictedByPenyulang = [];
        foreach ($prediksiData as $data) {
            $predictedByPenyulang[$data['nama_penyulang']] = $data['tingkat_risiko'];
        }
        
        $classes = ['RENDAH', 'SEDANG', 'TINGGI'];
        $matrix = array_fill_keys($classes, array_fill_keys($classes, 0));
        $total = 0;
        $correct = 0;
        
        foreach ($actualByPenyulang as $penyulang => $actual) {
            if (isset($predictedByPenyulang[$penyulang])) {
                $predicted = $predictedByPenyulang[$penyulang];
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
            'classes' => $classes,
            'total_samples' => $total,
            'correct_predictions' => $correct
        ];
    }

    public function findOptimalKWithDetailedCV($maxK = 15, $folds = 5) {
        try {
            $trainingData = $this->getTrainingData();
            
            if (empty($trainingData)) {
                throw new Exception("Tidak ada data training untuk mencari K optimal");
            }
            
            if (count($trainingData) < $folds * 2) {
                throw new Exception("Data training terlalu sedikit untuk cross validation");
            }
            
            $kResults = [];
            $bestK = 3;
            $bestAccuracy = 0;
            $bestMetrics = [];
            
            for ($k = 3; $k <= $maxK; $k += 2) {
                $cvResult = $this->detailedCrossValidation($trainingData, $k, $folds);
                
                $kResults[] = [
                    'k' => $k,
                    'accuracy' => round($cvResult['accuracy'], 2),
                    'precision' => $cvResult['precision'],
                    'recall' => $cvResult['recall'],
                    'f1_score' => $cvResult['f1_score'],
                    'std_dev' => round($cvResult['std_dev'], 2),
                    'fold_accuracies' => $cvResult['fold_accuracies']
                ];
                
                if ($cvResult['accuracy'] > $bestAccuracy) {
                    $bestAccuracy = $cvResult['accuracy'];
                    $bestK = $k;
                    $bestMetrics = $cvResult;
                }
            }
            
            return [
                'success' => true,
                'optimal_k' => $bestK,
                'best_accuracy' => round($bestAccuracy, 2),
                'best_metrics' => $bestMetrics,
                'all_results' => $kResults,
                'data_size' => count($trainingData),
                'cv_folds' => $folds
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function detailedCrossValidation($data, $k, $folds = 5) {
        $foldData = $this->createFolds($data, $folds);
        $foldResults = [];
        
        for ($i = 0; $i < $folds; $i++) {
            $testFold = $foldData[$i];
            $trainFolds = [];
            
            for ($j = 0; $j < $folds; $j++) {
                if ($i !== $j) {
                    $trainFolds = array_merge($trainFolds, $foldData[$j]);
                }
            }
            
            if (empty($trainFolds) || empty($testFold)) continue;
            
            $foldPredictions = [];
            foreach ($testFold as $testPoint) {
                $neighbors = $this->findNearestNeighbors($testPoint, $trainFolds, $k);
                $prediction = $this->predictClass($neighbors);
                
                $foldPredictions[] = [
                    'actual' => strtoupper($testPoint['tingkat_risiko']),
                    'predicted' => $prediction['class']
                ];
            }
            
            $foldMetrics = $this->calculateDetailedMetrics($foldPredictions);
            $foldResults[] = $foldMetrics;
        }
        
        $avgAccuracy = array_sum(array_column($foldResults, 'accuracy')) / count($foldResults);
        $avgPrecision = $this->averageMetricsByClass($foldResults, 'precision');
        $avgRecall = $this->averageMetricsByClass($foldResults, 'recall');
        $avgF1Score = $this->averageMetricsByClass($foldResults, 'f1_score');
        
        $accuracies = array_column($foldResults, 'accuracy');
        $stdDev = count($accuracies) > 1 ? sqrt(array_sum(array_map(function($x) use ($avgAccuracy) { 
            return pow($x - $avgAccuracy, 2); 
        }, $accuracies)) / count($accuracies)) : 0;
        
        return [
            'accuracy' => $avgAccuracy,
            'precision' => $avgPrecision,
            'recall' => $avgRecall,
            'f1_score' => $avgF1Score,
            'std_dev' => $stdDev,
            'fold_accuracies' => $accuracies,
            'fold_results' => $foldResults
        ];
    }

    private function calculateDetailedMetrics($predictions) {
        $classes = ['RENDAH', 'SEDANG', 'TINGGI'];
        $matrix = array_fill_keys($classes, array_fill_keys($classes, 0));
        $total = 0;
        $correct = 0;
        
        foreach ($predictions as $pred) {
            $actual = $pred['actual'];
            $predicted = $pred['predicted'];
            
            if (isset($matrix[$actual][$predicted])) {
                $matrix[$actual][$predicted]++;
                $total++;
                
                if ($actual === $predicted) {
                    $correct++;
                }
            }
        }
        
        $accuracy = $total > 0 ? ($correct / $total) * 100 : 0;
        
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
            'accuracy' => $accuracy,
            'precision' => $precision,
            'recall' => $recall,
            'f1_score' => $f1_score,
            'matrix' => $matrix
        ];
    }

    private function averageMetricsByClass($foldResults, $metricName) {
        $classes = ['RENDAH', 'SEDANG', 'TINGGI'];
        $avgMetrics = [];
        
        foreach ($classes as $class) {
            $values = array_column(array_column($foldResults, $metricName), $class);
            $avgMetrics[$class] = count($values) > 0 ? round(array_sum($values) / count($values), 2) : 0;
        }
        
        return $avgMetrics;
    }

    public function getRiskDistributionData() {
        $query = "
            SELECT tingkat_risiko as label, COUNT(*) as value
            FROM hasil_prediksi_risiko
            GROUP BY tingkat_risiko
            ORDER BY CASE tingkat_risiko 
                WHEN 'TINGGI' THEN 1 
                WHEN 'SEDANG' THEN 2 
                WHEN 'RENDAH' THEN 3 
            END
        ";
        
        $result = $this->db->query($query);
        if (!$result) return [];
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getAccuracyTrendData() {
        try {
            $trainingData = $this->getTrainingData();
            $testData = $this->getTestData();
            
            if (empty($trainingData) || empty($testData)) return [];
            
            $kValues = [3, 5, 7, 9, 11, 13, 15];
            $accuracyData = [];
            
            foreach ($kValues as $k) {
                $correct = 0;
                $total = 0;
                
                foreach ($testData as $testPoint) {
                    $neighbors = $this->findNearestNeighbors($testPoint, $trainingData, $k);
                    $prediction = $this->predictClass($neighbors);
                    
                    if ($prediction['class'] === strtoupper($testPoint['tingkat_risiko'])) {
                        $correct++;
                    }
                    $total++;
                }
                
                $accuracy = $total > 0 ? ($correct / $total) * 100 : 0;
                $accuracyData[] = [
                    'k_value' => $k,
                    'accuracy' => round($accuracy, 2),
                    'total_predictions' => $total
                ];
            }
            
            return $accuracyData;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getTopRiskPenyulang($limit = 10) {
        $query = "
            SELECT nama_penyulang, tingkat_risiko, nilai_risiko, total_kegiatan
            FROM hasil_prediksi_risiko
            WHERE tingkat_risiko = 'TINGGI'
            ORDER BY nilai_risiko DESC, total_kegiatan DESC
            LIMIT ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function detectOverfitting($k_value = null) {
        try {
            $trainingData = $this->getTrainingData();
            $testData = $this->getTestData();
            
            if (empty($trainingData) || empty($testData)) {
                return [
                    'overfitting' => false,
                    'message' => 'Data tidak tersedia untuk deteksi overfitting',
                    'details' => []
                ];
            }
            
            $k = $k_value ?? 5;
            $trainingAccuracy = $this->calculateAccuracyOnData($trainingData, $trainingData, $k);
            $testAccuracy = $this->calculateAccuracyOnData($testData, $trainingData, $k);
            $accuracyGap = $trainingAccuracy - $testAccuracy;
            $isOverfitting = $accuracyGap > 15.0;
            
            return [
                'overfitting' => $isOverfitting,
                'training_accuracy' => round($trainingAccuracy, 2),
                'test_accuracy' => round($testAccuracy, 2),
                'accuracy_gap' => round($accuracyGap, 2),
                'message' => $isOverfitting ? 
                    "Model mengalami overfitting dengan gap accuracy {$accuracyGap}%" : 
                    "Model tidak mengalami overfitting"
            ];
        } catch (Exception $e) {
            return [
                'overfitting' => false,
                'message' => 'Error dalam deteksi overfitting: ' . $e->getMessage()
            ];
        }
    }

    private function calculateAccuracyOnData($testSet, $trainSet, $k) {
        $correct = 0;
        $total = 0;
        
        foreach ($testSet as $testPoint) {
            $neighbors = $this->findNearestNeighbors($testPoint, $trainSet, $k);
            $prediction = $this->predictClass($neighbors);
            
            if ($prediction['class'] === strtoupper($testPoint['tingkat_risiko'])) {
                $correct++;
            }
            $total++;
        }
        
        return $total > 0 ? ($correct / $total) * 100 : 0;
    }

    public function getModelHealthStatus() {
        try {
            $overfittingCheck = $this->detectOverfitting();
            $confusionMatrix = $this->getConfusionMatrix();
            $statistics = $this->getPrediksiStatistics();
            
            $healthScore = 100;
            $issues = [];
            
            if ($overfittingCheck['overfitting']) {
                $healthScore -= 30;
                $issues[] = 'Overfitting detected';
            }
            
            if ($confusionMatrix['accuracy'] < 70) {
                $healthScore -= 20;
                $issues[] = 'Low accuracy';
            }
            
            if ($statistics['total_prediksi'] < 10) {
                $healthScore -= 15;
                $issues[] = 'Insufficient data';
            }
            
            $healthStatus = 'EXCELLENT';
            if ($healthScore < 85) $healthStatus = 'GOOD';
            if ($healthScore < 70) $healthStatus = 'FAIR';
            if ($healthScore < 50) $healthStatus = 'POOR';
            
            return [
                'health_score' => max(0, $healthScore),
                'status' => $healthStatus,
                'issues' => $issues
            ];
        } catch (Exception $e) {
            return [
                'health_score' => 0,
                'status' => 'ERROR',
                'issues' => ['Error checking model health: ' . $e->getMessage()]
            ];
        }
    }

    public function clearPrediksiData() {
        $query = "DELETE FROM hasil_prediksi_risiko";
        $result = $this->db->query($query);
        
        if (!$result) {
            throw new Exception("Failed to clear prediction data: " . $this->db->error);
        }
        
        $this->db->query("ALTER TABLE hasil_prediksi_risiko AUTO_INCREMENT = 1");
        return true;
    }

    public function resetAllPrediksiData() {
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
            return ['success' => true, 'message' => 'Data prediksi berhasil direset'];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Reset gagal: ' . $e->getMessage()];
        }
    }
}