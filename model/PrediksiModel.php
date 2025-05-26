<?php
include_once 'koneksi.php';

class PrediksiModel
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;

        if (!$this->db) {
            die("Database connection failed.");
        }
    }

    public function getFormData()
    {
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

    public function getFeatureData()
    {
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

    public function getTrainingDataFromClustering()
    {
        $query = "SELECT 
                    dp.id_data_pemeliharaan,
                    dp.tanggal,
                    dp.nama_objek,
                    CASE 
                        WHEN dp.nama_objek = 'gardu' THEN g.nama_penyulang
                        WHEN dp.nama_objek = 'sutm' THEN s.nama_penyulang
                        ELSE 'Unknown'
                    END as nama_penyulang,
                    hc.cluster_label,
                    
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
                    COALESCE(g.alat_ultrasonik, 0) + COALESCE(s.alat_ultrasonik, 0) as ultrasonik,
                    
                    (COALESCE(g.pengukuran, 0) + 
                     COALESCE(g.pergantian_arrester, 0) + 
                     COALESCE(g.pergantian_fco, 0) + 
                     COALESCE(g.perbaikan_grounding, 0) +
                     COALESCE(s.pangkas_batang, 0) + 
                     COALESCE(s.tebang, 0) + 
                     COALESCE(s.pin_isolator, 0) + 
                     COALESCE(s.arrester_sutm, 0) + 
                     COALESCE(s.fco_sutm, 0) + 
                     COALESCE(s.grounding_sutm, 0) +
                     COALESCE(g.pemasangan_cover_isolasi, 0) + 
                     COALESCE(s.pemasangan_cover_isolasi, 0) +
                     COALESCE(g.pemasangan_penghalang_panjat, 0) + 
                     COALESCE(s.pemasangan_penghalang_panjang, 0) +
                     COALESCE(g.alat_ultrasonik, 0) + 
                     COALESCE(s.alat_ultrasonik, 0)) as total_kegiatan_utama
                     
                  FROM split_data sd
                  JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
                  JOIN hasil_cluster hc ON hc.id_data_pemeliharaan = dp.id_data_pemeliharaan
                  LEFT JOIN gardu g ON dp.id_sub_kategori = g.id_gardu AND dp.nama_objek = 'gardu'
                  LEFT JOIN sutm s ON dp.id_sub_kategori = s.id_sutm AND dp.nama_objek = 'sutm'
                  WHERE sd.tipe_data = 'train'
                  ORDER BY dp.tanggal DESC";
        
        $result = mysqli_query($this->db, $query);
        $trainingData = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $trainingData[] = $row;
            }
        }

        return $trainingData;
    }

    public function getTestDataFromSplit()
    {
        $query = "SELECT 
                    dp.id_data_pemeliharaan,
                    dp.tanggal,
                    dp.nama_objek,
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
                    COALESCE(g.alat_ultrasonik, 0) + COALESCE(s.alat_ultrasonik, 0) as ultrasonik,
                    
                    (COALESCE(g.pengukuran, 0) + 
                     COALESCE(g.pergantian_arrester, 0) + 
                     COALESCE(g.pergantian_fco, 0) + 
                     COALESCE(g.perbaikan_grounding, 0) +
                     COALESCE(s.pangkas_batang, 0) + 
                     COALESCE(s.tebang, 0) + 
                     COALESCE(s.pin_isolator, 0) + 
                     COALESCE(s.arrester_sutm, 0) + 
                     COALESCE(s.fco_sutm, 0) + 
                     COALESCE(s.grounding_sutm, 0) +
                     COALESCE(g.pemasangan_cover_isolasi, 0) + 
                     COALESCE(s.pemasangan_cover_isolasi, 0) +
                     COALESCE(g.pemasangan_penghalang_panjat, 0) + 
                     COALESCE(s.pemasangan_penghalang_panjang, 0) +
                     COALESCE(g.alat_ultrasonik, 0) + 
                     COALESCE(s.alat_ultrasonik, 0)) as total_kegiatan_utama
                     
                  FROM split_data sd
                  JOIN data_pemeliharaan dp ON sd.id_data_pemeliharaan = dp.id_data_pemeliharaan
                  LEFT JOIN gardu g ON dp.id_sub_kategori = g.id_gardu AND dp.nama_objek = 'gardu'
                  LEFT JOIN sutm s ON dp.id_sub_kategori = s.id_sutm AND dp.nama_objek = 'sutm'
                  WHERE sd.tipe_data = 'test'
                  ORDER BY dp.tanggal DESC";
        
        $result = mysqli_query($this->db, $query);
        $testData = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $testData[] = $row;
            }
        }

        return $testData;
    }

    private function normalizeFeatures($features)
    {
        if (empty($features)) return [];
        
        $featureNames = [
            't1_inspeksi', 't1_realisasi', 't2_inspeksi', 't2_realisasi',
            'gardu_pengukuran', 'gardu_arrester', 'gardu_fco', 'gardu_grounding',
            'sutm_pangkas_kms', 'sutm_pangkas_batang', 'sutm_tebang', 'sutm_pin_isolator',
            'sutm_arrester', 'sutm_fco', 'sutm_grounding',
            'cover_isolasi', 'penghalang_panjat', 'ultrasonik', 'total_kegiatan_utama'
        ];
        
        $mins = [];
        $maxs = [];
        
        foreach ($featureNames as $feature) {
            $values = array_column($features, $feature);
            $mins[$feature] = min($values);
            $maxs[$feature] = max($values);
        }
        
        $normalized = [];
        foreach ($features as $row) {
            $normalizedRow = $row;
            foreach ($featureNames as $feature) {
                $range = $maxs[$feature] - $mins[$feature];
                if ($range > 0) {
                    $normalizedRow[$feature] = ($row[$feature] - $mins[$feature]) / $range;
                } else {
                    $normalizedRow[$feature] = 0;
                }
            }
            $normalized[] = $normalizedRow;
        }
        
        return $normalized;
    }

    private function euclideanDistance($point1, $point2)
    {
        $featureNames = [
            't1_inspeksi', 't1_realisasi', 't2_inspeksi', 't2_realisasi',
            'gardu_pengukuran', 'gardu_arrester', 'gardu_fco', 'gardu_grounding',
            'sutm_pangkas_kms', 'sutm_pangkas_batang', 'sutm_tebang', 'sutm_pin_isolator',
            'sutm_arrester', 'sutm_fco', 'sutm_grounding',
            'cover_isolasi', 'penghalang_panjat', 'ultrasonik', 'total_kegiatan_utama'
        ];
        
        $sum = 0;
        foreach ($featureNames as $feature) {
            $sum += pow(($point1[$feature] ?? 0) - ($point2[$feature] ?? 0), 2);
        }
        
        return sqrt($sum);
    }

    private function classifyRiskFromCluster($clusterLabel, $totalKegiatan)
    {
        if ($clusterLabel == 0) {
            if ($totalKegiatan > 150) {
                return ['tingkat' => 'TINGGI', 'nilai' => 75 + (rand(0, 25000) / 1000)];
            } else {
                return ['tingkat' => 'SEDANG', 'nilai' => 45 + (rand(0, 30000) / 1000)];
            }
        } elseif ($clusterLabel == 1) {
            if ($totalKegiatan > 100) {
                return ['tingkat' => 'SEDANG', 'nilai' => 40 + (rand(0, 35000) / 1000)];
            } else {
                return ['tingkat' => 'RENDAH', 'nilai' => 20 + (rand(0, 20000) / 1000)];
            }
        } else {
            if ($totalKegiatan > 50) {
                return ['tingkat' => 'SEDANG', 'nilai' => 35 + (rand(0, 25000) / 1000)];
            } else {
                return ['tingkat' => 'RENDAH', 'nilai' => 10 + (rand(0, 25000) / 1000)];
            }
        }
    }

    public function runKnnPrediction($k = 3)
    {
        $trainingData = $this->getTrainingDataFromClustering();
        $testData = $this->getTestDataFromSplit();
        
        if (empty($trainingData)) {
            throw new Exception("Tidak ada data training. Lakukan clustering dan split data terlebih dahulu.");
        }
        
        if (empty($testData)) {
            throw new Exception("Tidak ada data testing. Lakukan split data terlebih dahulu.");
        }
        
        $normalizedTraining = $this->normalizeFeatures($trainingData);
        $normalizedTest = $this->normalizeFeatures($testData);
        
        $predictions = [];
        $accuracy = 0;
        $totalPredictions = 0;
        
        foreach ($normalizedTest as $testPoint) {
            $distances = [];
            
            foreach ($normalizedTraining as $trainPoint) {
                $distance = $this->euclideanDistance($testPoint, $trainPoint);
                $distances[] = [
                    'distance' => $distance,
                    'cluster_label' => $trainPoint['cluster_label'],
                    'total_kegiatan' => $trainPoint['total_kegiatan_utama'],
                    'nama_penyulang' => $trainPoint['nama_penyulang']
                ];
            }
            
            usort($distances, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
            
            $kNearest = array_slice($distances, 0, $k);
            
            $clusterVotes = [];
            $totalKegiatanSum = 0;
            
            foreach ($kNearest as $neighbor) {
                $cluster = $neighbor['cluster_label'];
                $clusterVotes[$cluster] = ($clusterVotes[$cluster] ?? 0) + 1;
                $totalKegiatanSum += $neighbor['total_kegiatan'];
            }
            
            arsort($clusterVotes);
            $predictedCluster = array_key_first($clusterVotes);
            $avgTotalKegiatan = $totalKegiatanSum / $k;
            
            $riskClassification = $this->classifyRiskFromCluster($predictedCluster, $avgTotalKegiatan);
            
            $predictions[] = [
                'id_data_pemeliharaan' => $testPoint['id_data_pemeliharaan'],
                'nama_penyulang' => $testPoint['nama_penyulang'],
                'tingkat_risiko' => $riskClassification['tingkat'],
                'nilai_risiko' => $riskClassification['nilai'],
                'total_kegiatan' => $testPoint['total_kegiatan_utama'],
                'predicted_cluster' => $predictedCluster,
                'confidence' => ($clusterVotes[$predictedCluster] / $k) * 100,
                'k_neighbors' => $k
            ];
            
            $totalPredictions++;
        }
        
        $penyulangPredictions = [];
        foreach ($predictions as $pred) {
            $penyulang = $pred['nama_penyulang'];
            if (!isset($penyulangPredictions[$penyulang])) {
                $penyulangPredictions[$penyulang] = [];
            }
            $penyulangPredictions[$penyulang][] = $pred;
        }
        
        $finalPredictions = [];
        foreach ($penyulangPredictions as $penyulang => $preds) {
            $tinggiCount = 0;
            $sedangCount = 0;
            $rendahCount = 0;
            $totalNilai = 0;
            $totalKegiatan = 0;
            $totalConfidence = 0;
            
            foreach ($preds as $pred) {
                switch ($pred['tingkat_risiko']) {
                    case 'TINGGI':
                        $tinggiCount++;
                        break;
                    case 'SEDANG':
                        $sedangCount++;
                        break;
                    case 'RENDAH':
                        $rendahCount++;
                        break;
                }
                $totalNilai += $pred['nilai_risiko'];
                $totalKegiatan += $pred['total_kegiatan'];
                $totalConfidence += $pred['confidence'];
            }
            
            $finalTingkat = 'RENDAH';
            if ($tinggiCount >= $sedangCount && $tinggiCount >= $rendahCount) {
                $finalTingkat = 'TINGGI';
            } elseif ($sedangCount >= $rendahCount) {
                $finalTingkat = 'SEDANG';
            }
            
            $finalPredictions[] = [
                'nama_penyulang' => $penyulang,
                'tingkat_risiko' => $finalTingkat,
                'nilai_risiko' => $totalNilai / count($preds),
                'total_kegiatan' => $totalKegiatan / count($preds),
                'confidence' => $totalConfidence / count($preds),
                'predictions_count' => count($preds)
            ];
        }
        
        $this->savePredictions($finalPredictions, $k);
        
        return [
            'predictions' => $finalPredictions,
            'accuracy' => ($totalPredictions > 0) ? ($accuracy / $totalPredictions) * 100 : 0,
            'total_predictions' => $totalPredictions,
            'k_value' => $k,
            'training_count' => count($trainingData),
            'test_count' => count($testData)
        ];
    }

    private function savePredictions($predictions, $k)
    {
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

    public function getPredictionResults($tingkatRisiko = null)
    {
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
                    k_value,
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

    public function getPredictionSummary()
    {
        $query = "SELECT 
                    tingkat_risiko, 
                    COUNT(*) as jumlah_penyulang,
                    AVG(nilai_risiko) as rata_nilai_risiko,
                    AVG(total_kegiatan) as rata_total_kegiatan
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

    public function getLastPrediction()
    {
        $query = "SELECT tanggal_prediksi, k_value, COUNT(*) as total_predictions
                  FROM hasil_prediksi_risiko
                  GROUP BY tanggal_prediksi, k_value
                  ORDER BY tanggal_prediksi DESC
                  LIMIT 1";

        $result = mysqli_query($this->db, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return null;
    }

    public function getDataStatus()
    {
        $clusteringCount = 0;
        $splitCount = 0;
        $trainingCount = 0;
        $testCount = 0;
        
        $query = "SELECT COUNT(*) as count FROM hasil_cluster";
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $clusteringCount = $row['count'];
        }
        
        $query = "SELECT COUNT(*) as count FROM split_data";
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $splitCount = $row['count'];
        }
        
        $query = "SELECT COUNT(*) as count FROM split_data WHERE tipe_data = 'train'";
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $trainingCount = $row['count'];
        }
        
        $query = "SELECT COUNT(*) as count FROM split_data WHERE tipe_data = 'test'";
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $testCount = $row['count'];
        }
        
        return [
            'clustering_count' => $clusteringCount,
            'split_count' => $splitCount,
            'training_count' => $trainingCount,
            'test_count' => $testCount,
            'has_clustering' => $clusteringCount > 0,
            'has_split' => $splitCount > 0,
            'ready_for_prediction' => $clusteringCount > 0 && $splitCount > 0
        ];
    }
}
?>