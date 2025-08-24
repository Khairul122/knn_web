<?php
include_once 'koneksi.php';

class PrediksiModel {
    private $db;
    private $features = ['total_kegiatan', 't1_inspeksi', 't1_realisasi', 't2_inspeksi', 't2_realisasi', 'pengukuran'];
    private $classes = ['RENDAH', 'SEDANG', 'TINGGI'];

    public function __construct() {
        global $koneksi;
        $this->db = $koneksi;
        if (!$this->db) {
            die("Database connection failed. Please check koneksi.php file.");
        }
    }

    public function getTrainingData() {
        return $this->getData('train');
    }

    public function getTestData() {
        return $this->getData('test');
    }

    private function getData($type) {
        $query = "
            SELECT dp.nama_objek, hc.tingkat_risiko, hc.risk_score as nilai_risiko,
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
                        ELSE 0 END as total_kegiatan,
                   CASE WHEN dp.nama_objek = 'gardu' THEN g.t1_inspeksi 
                        WHEN dp.nama_objek = 'sutm' THEN s.t1_inspeksi ELSE 0 END as t1_inspeksi,
                   CASE WHEN dp.nama_objek = 'gardu' THEN g.t1_realisasi 
                        WHEN dp.nama_objek = 'sutm' THEN s.t1_realisasi ELSE 0 END as t1_realisasi,
                   CASE WHEN dp.nama_objek = 'gardu' THEN g.t2_inspeksi 
                        WHEN dp.nama_objek = 'sutm' THEN s.t2_inspeksi ELSE 0 END as t2_inspeksi,
                   CASE WHEN dp.nama_objek = 'gardu' THEN g.t2_realisasi 
                        WHEN dp.nama_objek = 'sutm' THEN s.t2_realisasi ELSE 0 END as t2_realisasi,
                   CASE WHEN dp.nama_objek = 'gardu' THEN g.pengukuran ELSE 0 END as pengukuran
            FROM split_data sd
            JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
            JOIN hasil_cluster hc ON sd.id_cluster = hc.id_cluster
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            WHERE sd.tipe_data = ?
            ORDER BY sd.id_split
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            throw new Exception("Failed to get {$type} data: " . $this->db->error);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }

    private function calculateDistance($point1, $point2) {
        $sum = 0;
        foreach ($this->features as $feature) {
            $diff = $point1[$feature] - $point2[$feature];
            $sum += pow($diff, 2);
        }
        return sqrt($sum);
    }

    private function findNearestNeighbors($testPoint, $trainingData, $k) {
        $distances = [];
        
        foreach ($trainingData as $trainPoint) {
            $distance = $this->calculateDistance($testPoint, $trainPoint);
            $distances[] = [
                'distance' => $distance,
                'tingkat_risiko' => $trainPoint['tingkat_risiko'],
                'nilai_risiko' => $trainPoint['nilai_risiko']
            ];
        }
        
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        return array_slice($distances, 0, $k);
    }

    private function predictClass($neighbors) {
        $votes = array_fill_keys($this->classes, 0);
        $riskSum = 0;
        
        foreach ($neighbors as $neighbor) {
            $risk = strtoupper($neighbor['tingkat_risiko']);
            if (isset($votes[$risk])) {
                $votes[$risk]++;
            }
            $riskSum += $neighbor['nilai_risiko'];
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

    private function generateSuggestions($tingkatRisiko, $totalKegiatan, $nilaiRisiko = 0) {
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
                if ($nilaiRisiko > 8) {
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

    private function validateData($data, $minSize = 10) {
        if (empty($data)) {
            throw new Exception("Data tidak tersedia");
        }
        
        if (count($data) < $minSize) {
            throw new Exception("Data terlalu sedikit, minimal {$minSize} data diperlukan");
        }
    }

    public function findOptimalK($maxK = 15) {
        try {
            $trainingData = $this->getTrainingData();
            $this->validateData($trainingData);
            
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

    public function findOptimalKWithDetailedCV($maxK = 15, $folds = 5) {
        try {
            $trainingData = $this->getTrainingData();
            $this->validateData($trainingData, $folds * 2);
            
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

    private function crossValidateKNN($data, $k, $folds = 5) {
        $foldData = $this->createFolds($data, $folds);
        $accuracies = [];
        
        for ($i = 0; $i < $folds; $i++) {
            $testFold = $foldData[$i];
            $trainFolds = $this->getTrainingFolds($foldData, $i);
            
            if (empty($trainFolds) || empty($testFold)) continue;
            
            $accuracy = $this->calculateFoldAccuracy($testFold, $trainFolds, $k);
            $accuracies[] = $accuracy;
        }
        
        return count($accuracies) > 0 ? array_sum($accuracies) / count($accuracies) : 0;
    }

    private function detailedCrossValidation($data, $k, $folds = 5) {
        $foldData = $this->createFolds($data, $folds);
        $foldResults = [];
        
        for ($i = 0; $i < $folds; $i++) {
            $testFold = $foldData[$i];
            $trainFolds = $this->getTrainingFolds($foldData, $i);
            
            if (empty($trainFolds) || empty($testFold)) continue;
            
            $foldPredictions = $this->predictFold($testFold, $trainFolds, $k);
            $foldMetrics = $this->calculateDetailedMetrics($foldPredictions);
            $foldResults[] = $foldMetrics;
        }
        
        return $this->aggregateFoldResults($foldResults);
    }

    private function getTrainingFolds($foldData, $excludeIndex) {
        $trainFolds = [];
        for ($j = 0; $j < count($foldData); $j++) {
            if ($j !== $excludeIndex) {
                $trainFolds = array_merge($trainFolds, $foldData[$j]);
            }
        }
        return $trainFolds;
    }

    private function calculateFoldAccuracy($testFold, $trainFolds, $k) {
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
        
        return $total > 0 ? ($correct / $total) * 100 : 0;
    }

    private function predictFold($testFold, $trainFolds, $k) {
        $predictions = [];
        foreach ($testFold as $testPoint) {
            $neighbors = $this->findNearestNeighbors($testPoint, $trainFolds, $k);
            $prediction = $this->predictClass($neighbors);
            
            $predictions[] = [
                'actual' => strtoupper($testPoint['tingkat_risiko']),
                'predicted' => $prediction['class']
            ];
        }
        return $predictions;
    }

    private function calculateDetailedMetrics($predictions) {
        $matrix = array_fill_keys($this->classes, array_fill_keys($this->classes, 0));
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
        
        return [
            'accuracy' => $accuracy,
            'precision' => $this->calculatePrecision($matrix),
            'recall' => $this->calculateRecall($matrix),
            'f1_score' => $this->calculateF1Score($matrix),
            'matrix' => $matrix
        ];
    }

    private function calculatePrecision($matrix) {
        $precision = [];
        foreach ($this->classes as $class) {
            $tp = $matrix[$class][$class];
            $fp = array_sum(array_column($matrix, $class)) - $tp;
            $precision[$class] = ($tp + $fp) > 0 ? round(($tp / ($tp + $fp)) * 100, 2) : 0;
        }
        return $precision;
    }

    private function calculateRecall($matrix) {
        $recall = [];
        foreach ($this->classes as $class) {
            $tp = $matrix[$class][$class];
            $fn = array_sum($matrix[$class]) - $tp;
            $recall[$class] = ($tp + $fn) > 0 ? round(($tp / ($tp + $fn)) * 100, 2) : 0;
        }
        return $recall;
    }

    private function calculateF1Score($matrix) {
        $precision = $this->calculatePrecision($matrix);
        $recall = $this->calculateRecall($matrix);
        $f1_score = [];
        
        foreach ($this->classes as $class) {
            $p = $precision[$class];
            $r = $recall[$class];
            $f1_score[$class] = ($p + $r) > 0 ? round((2 * $p * $r) / ($p + $r), 2) : 0;
        }
        return $f1_score;
    }

    private function aggregateFoldResults($foldResults) {
        if (empty($foldResults)) {
            return ['accuracy' => 0, 'precision' => [], 'recall' => [], 'f1_score' => [], 'std_dev' => 0];
        }
        
        $avgAccuracy = array_sum(array_column($foldResults, 'accuracy')) / count($foldResults);
        $avgPrecision = $this->averageMetricsByClass($foldResults, 'precision');
        $avgRecall = $this->averageMetricsByClass($foldResults, 'recall');
        $avgF1Score = $this->averageMetricsByClass($foldResults, 'f1_score');
        
        $accuracies = array_column($foldResults, 'accuracy');
        $stdDev = $this->calculateStandardDeviation($accuracies, $avgAccuracy);
        
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

    private function averageMetricsByClass($foldResults, $metricName) {
        $avgMetrics = [];
        foreach ($this->classes as $class) {
            $values = array_column(array_column($foldResults, $metricName), $class);
            $avgMetrics[$class] = count($values) > 0 ? round(array_sum($values) / count($values), 2) : 0;
        }
        return $avgMetrics;
    }

    private function calculateStandardDeviation($values, $mean) {
        if (count($values) <= 1) return 0;
        
        $sum = array_sum(array_map(function($x) use ($mean) { 
            return pow($x - $mean, 2); 
        }, $values));
        
        return sqrt($sum / count($values));
    }

    public function trainKNN($k_value) {
        try {
            $this->db->begin_transaction();
            $this->clearPrediksiData();
            
            $trainingData = $this->getTrainingData();
            $testData = $this->getTestData();
            
            $this->validateData($trainingData, 5);
            $this->validateData($testData, 1);
            
            $predictions = $this->makePredictions($testData, $trainingData, $k_value);
            $aggregatedPredictions = $this->aggregatePenyulangPredictions($predictions);
            $successCount = $this->savePredictions($aggregatedPredictions, $k_value);
            
            if ($successCount > 0) {
                $this->db->commit();
                return $this->getTrainingResult($predictions, $k_value, $trainingData, $testData, $successCount);
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

    private function makePredictions($testData, $trainingData, $k_value) {
        $predictions = [];
        foreach ($testData as $testPoint) {
            $neighbors = $this->findNearestNeighbors($testPoint, $trainingData, $k_value);
            $prediction = $this->predictClass($neighbors);
            
            $predictions[] = [
                'nama_penyulang' => $testPoint['nama_penyulang'],
                'predicted' => $prediction['class'],
                'actual' => strtoupper($testPoint['tingkat_risiko']),
                'confidence' => $prediction['confidence'],
                'total_kegiatan' => $testPoint['total_kegiatan'],
                'avg_risk_score' => $prediction['avg_risk_score']
            ];
        }
        return $predictions;
    }

    private function aggregatePenyulangPredictions($predictions) {
        $penyulangGroups = [];
        
        foreach ($predictions as $prediction) {
            $penyulang = $prediction['nama_penyulang'];
            
            if (!isset($penyulangGroups[$penyulang])) {
                $penyulangGroups[$penyulang] = [
                    'predictions' => [], 
                    'total_kegiatan' => [], 
                    'confidences' => [],
                    'risk_scores' => []
                ];
            }
            
            $penyulangGroups[$penyulang]['predictions'][] = $prediction['predicted'];
            $penyulangGroups[$penyulang]['total_kegiatan'][] = $prediction['total_kegiatan'];
            $penyulangGroups[$penyulang]['confidences'][] = $prediction['confidence'];
            $penyulangGroups[$penyulang]['risk_scores'][] = $prediction['avg_risk_score'];
        }
        
        $aggregatedResults = [];
        
        foreach ($penyulangGroups as $penyulang => $group) {
            $predictionCounts = array_count_values($group['predictions']);
            $finalPrediction = array_search(max($predictionCounts), $predictionCounts);
            $avgTotalKegiatan = array_sum($group['total_kegiatan']) / count($group['total_kegiatan']);
            $avgConfidence = array_sum($group['confidences']) / count($group['confidences']);
            $avgRiskScore = array_sum($group['risk_scores']) / count($group['risk_scores']);
            
            $suggestions = $this->generateSuggestions($finalPrediction, $avgTotalKegiatan, $avgRiskScore);
            
            $aggregatedResults[] = [
                'nama_penyulang' => $penyulang,
                'final_prediction' => $finalPrediction,
                'avg_total_kegiatan' => round($avgTotalKegiatan),
                'avg_confidence' => $avgConfidence,
                'avg_risk_score' => round($avgRiskScore, 2),
                'prediction_counts' => $predictionCounts,
                'total_instances' => count($group['predictions']),
                'saran_perbaikan' => $suggestions
            ];
        }
        
        return $aggregatedResults;
    }

    private function savePredictions($aggregatedPredictions, $k_value) {
        $query = "INSERT INTO hasil_prediksi_risiko (nama_penyulang, tingkat_risiko, nilai_risiko, total_kegiatan, k_value, saran_perbaikan) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
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

    private function getTrainingResult($predictions, $k_value, $trainingData, $testData, $successCount) {
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
            SELECT id_prediksi, nama_penyulang, tingkat_risiko, nilai_risiko, total_kegiatan, k_value, saran_perbaikan, tanggal_prediksi
            FROM hasil_prediksi_risiko
            ORDER BY tanggal_prediksi DESC, nama_penyulang
        ";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception("Failed to get prediction data: " . $this->db->error);
        }
        
        return $this->fetchAllResults($result);
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
        return $this->processStatistics($stats);
    }

    private function processStatistics($stats) {
        if ($stats && $stats['total_prediksi'] > 0) {
            $total = $stats['total_prediksi'];
            $stats['tinggi_percentage'] = round(($stats['tinggi_count'] / $total) * 100, 2);
            $stats['sedang_percentage'] = round(($stats['sedang_count'] / $total) * 100, 2);
            $stats['rendah_percentage'] = round(($stats['rendah_count'] / $total) * 100, 2);
            $stats['avg_risk_score'] = round($stats['avg_risk_score'], 2);
            $stats['avg_total_kegiatan'] = round($stats['avg_total_kegiatan'], 0);
        } else {
            $stats = $this->getEmptyStatistics();
        }
        return $stats;
    }

    private function getEmptyStatistics() {
        return [
            'total_prediksi' => 0, 'tinggi_count' => 0, 'sedang_count' => 0, 'rendah_count' => 0,
            'tinggi_percentage' => 0, 'sedang_percentage' => 0, 'rendah_percentage' => 0,
            'avg_risk_score' => 0, 'avg_total_kegiatan' => 0, 'last_k_value' => 3
        ];
    }

    public function getConfusionMatrix() {
        try {
            $trainingData = $this->getTrainingData();
            $testData = $this->getTestData();
            $prediksiData = $this->getPrediksiData();
            
            if (empty($testData) || empty($trainingData)) {
                return $this->getEmptyConfusionMatrix();
            }
            
            $k_value = !empty($prediksiData) ? $prediksiData[0]['k_value'] : 5;
            return $this->calculateConfusionMatrix($testData, $trainingData, $k_value);
            
        } catch (Exception $e) {
            return $this->getEmptyConfusionMatrix();
        }
    }

    private function calculateConfusionMatrix($testData, $trainingData, $k_value) {
        $matrix = array_fill_keys($this->classes, array_fill_keys($this->classes, 0));
        $total = 0;
        $correct = 0;
        
        foreach ($testData as $testPoint) {
            $neighbors = $this->findNearestNeighbors($testPoint, $trainingData, $k_value);
            $prediction = $this->predictClass($neighbors);
            
            $actual = strtoupper($testPoint['tingkat_risiko']);
            $predicted = $prediction['class'];
            
            $matrix[$actual][$predicted]++;
            $total++;
            
            if ($actual === $predicted) {
                $correct++;
            }
        }
        
        $accuracy = $total > 0 ? round(($correct / $total) * 100, 2) : 0;
        
        return [
            'matrix' => $matrix,
            'accuracy' => $accuracy,
            'precision' => $this->calculatePrecision($matrix),
            'recall' => $this->calculateRecall($matrix),
            'f1_score' => $this->calculateF1Score($matrix),
            'classes' => $this->classes,
            'total_samples' => $total,
            'correct_predictions' => $correct
        ];
    }

    private function getEmptyConfusionMatrix() {
        return [
            'matrix' => [], 'accuracy' => 0, 'precision' => [], 'recall' => [], 'f1_score' => [],
            'classes' => $this->classes, 'total_samples' => 0, 'correct_predictions' => 0
        ];
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
        return $result ? $this->fetchAllResults($result) : [];
    }

    public function getAccuracyTrendData() {
        try {
            $trainingData = $this->getTrainingData();
            $testData = $this->getTestData();
            
            if (empty($trainingData) || empty($testData)) return [];
            
            $kValues = [3, 5, 7, 9, 11, 13, 15];
            $accuracyData = [];
            
            foreach ($kValues as $k) {
                $accuracy = $this->calculateAccuracyForK($testData, $trainingData, $k);
                $accuracyData[] = [
                    'k_value' => $k,
                    'accuracy' => round($accuracy, 2),
                    'total_predictions' => count($testData)
                ];
            }
            
            return $accuracyData;
        } catch (Exception $e) {
            return [];
        }
    }

    private function calculateAccuracyForK($testData, $trainingData, $k) {
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
        
        return $total > 0 ? ($correct / $total) * 100 : 0;
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
        
        $data = $this->fetchAllResults($result);
        $stmt->close();
        return $data;
    }

    public function detectOverfitting($k_value = null) {
        try {
            $trainingData = $this->getTrainingData();
            $testData = $this->getTestData();
            
            if (empty($trainingData) || empty($testData)) {
                return $this->getEmptyOverfittingResult('Data tidak tersedia untuk deteksi overfitting');
            }
            
            $k = $k_value ?? 5;
            $trainingAccuracy = $this->calculateAccuracyForK($trainingData, $trainingData, $k);
            $testAccuracy = $this->calculateAccuracyForK($testData, $trainingData, $k);
            $accuracyGap = $trainingAccuracy - $testAccuracy;
            $isOverfitting = $accuracyGap > 15.0;
            
            return [
                'overfitting' => $isOverfitting,
                'training_accuracy' => round($trainingAccuracy, 2),
                'test_accuracy' => round($testAccuracy, 2),
                'accuracy_gap' => round($accuracyGap, 2),
                'message' => $isOverfitting ? 
                    "Model mengalami overfitting dengan gap accuracy " . round($accuracyGap, 2) . "%" : 
                    "Model tidak mengalami overfitting"
            ];
        } catch (Exception $e) {
            return $this->getEmptyOverfittingResult('Error dalam deteksi overfitting: ' . $e->getMessage());
        }
    }

    private function getEmptyOverfittingResult($message) {
        return [
            'overfitting' => false,
            'message' => $message,
            'training_accuracy' => 0,
            'test_accuracy' => 0,
            'accuracy_gap' => 0
        ];
    }

    public function getModelHealthStatus() {
        try {
            $overfittingCheck = $this->detectOverfitting();
            $confusionMatrix = $this->getConfusionMatrix();
            $statistics = $this->getPrediksiStatistics();
            
            return $this->calculateHealthStatus($overfittingCheck, $confusionMatrix, $statistics);
        } catch (Exception $e) {
            return $this->getErrorHealthStatus($e->getMessage());
        }
    }

    private function calculateHealthStatus($overfittingCheck, $confusionMatrix, $statistics) {
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
        
        $healthStatus = $this->getHealthStatusLabel($healthScore);
        
        return [
            'health_score' => max(0, $healthScore),
            'status' => $healthStatus,
            'issues' => $issues
        ];
    }

    private function getHealthStatusLabel($score) {
        if ($score >= 85) return 'EXCELLENT';
        if ($score >= 70) return 'GOOD';
        if ($score >= 50) return 'FAIR';
        return 'POOR';
    }

    private function getErrorHealthStatus($errorMessage) {
        return [
            'health_score' => 0,
            'status' => 'ERROR',
            'issues' => ['Error checking model health: ' . $errorMessage]
        ];
    }

    public function clearPrediksiData() {
        $queries = [
            "DELETE FROM hasil_prediksi_risiko",
            "ALTER TABLE hasil_prediksi_risiko AUTO_INCREMENT = 1"
        ];
        
        foreach ($queries as $query) {
            if (!$this->db->query($query)) {
                throw new Exception("Failed to clear prediction data: " . $this->db->error);
            }
        }
        
        return true;
    }

    public function resetAllPrediksiData() {
        try {
            $this->db->begin_transaction();
            $this->clearPrediksiData();
            $this->db->commit();
            
            return ['success' => true, 'message' => 'Data prediksi berhasil direset'];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Reset gagal: ' . $e->getMessage()];
        }
    }

    private function fetchAllResults($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}