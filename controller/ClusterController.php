<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'model/ClusterModel.php';

class ClusterController 
{
    private $clusterModel;

    public function __construct() 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $this->clusterModel = new ClusterModel();
    }

    public function index()
    {
        try {
            $data = [
                'title' => 'Cluster Management',
                'statistics' => $this->clusterModel->getClusterStatistics(),
                'summary' => $this->clusterModel->getClusterSummary(),
                'results' => $this->clusterModel->getClusterResults(20)
            ];
            
            $this->view('cluster/index', $data);
        } catch (Exception $e) {
            error_log("Index error: " . $e->getMessage());
            $this->showError('Gagal memuat halaman: ' . $e->getMessage());
        }
    }

    public function performClustering()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $result = $this->clusterModel->performKMeansClustering();
                
                if ($result['success']) {
                    $this->setFlashMessage('success', $result['message']);
                } else {
                    $this->setFlashMessage('error', $result['message']);
                }
            } catch (Exception $e) {
                error_log("Clustering error: " . $e->getMessage());
                $this->setFlashMessage('error', 'Clustering gagal: ' . $e->getMessage());
            }
        }
        
        $this->redirect('cluster');
    }

    public function getResults()
    {
        try {
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
            $results = $this->clusterModel->getClusterResults($limit);
            
            $data = [
                'title' => 'Hasil Clustering',
                'results' => $results,
                'total' => count($results)
            ];
            
            $this->view('cluster/results', $data);
        } catch (Exception $e) {
            error_log("Get results error: " . $e->getMessage());
            $this->showError('Gagal memuat hasil: ' . $e->getMessage());
        }
    }

    public function getSummary()
    {
        try {
            $summary = $this->clusterModel->getClusterSummary();
            $statistics = $this->clusterModel->getClusterStatistics();
            
            $data = [
                'title' => 'Ringkasan Cluster',
                'summary' => $summary,
                'statistics' => $statistics
            ];
            
            $this->view('cluster/summary', $data);
        } catch (Exception $e) {
            error_log("Get summary error: " . $e->getMessage());
            $this->showError('Gagal memuat ringkasan: ' . $e->getMessage());
        }
    }

    public function getPenyulangAnalysis()
    {
        try {
            $analysis = $this->clusterModel->getPenyulangRiskAnalysis();
            
            $data = [
                'title' => 'Analisis Risiko Penyulang',
                'analysis' => $analysis
            ];
            
            $this->view('cluster/penyulang_analysis', $data);
        } catch (Exception $e) {
            error_log("Get analysis error: " . $e->getMessage());
            $this->showError('Gagal memuat analisis: ' . $e->getMessage());
        }
    }

    public function getDetail()
    {
        try {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            if ($id <= 0) {
                $this->setFlashMessage('error', 'ID tidak valid');
                $this->redirect('cluster');
                return;
            }

            $detail = $this->clusterModel->getDetailClusterData($id);
            
            if ($detail) {
                $data = [
                    'title' => 'Detail Cluster Data',
                    'detail' => $detail
                ];
                
                $this->view('cluster/detail', $data);
            } else {
                $this->setFlashMessage('error', 'Data tidak ditemukan');
                $this->redirect('cluster');
            }
        } catch (Exception $e) {
            error_log("Get detail error: " . $e->getMessage());
            $this->setFlashMessage('error', 'Gagal memuat detail: ' . $e->getMessage());
            $this->redirect('cluster');
        }
    }

    public function reset($postData = null)
    {
        error_log("=== RESET ACTION CALLED ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . json_encode($_POST));
        error_log("Param data: " . json_encode($postData));
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $postData !== null) {
            try {
                $data = $postData ?? $_POST;
                $type = isset($data['type']) ? $data['type'] : 'cluster';
                
                error_log("Reset type: " . $type);
                
                if ($type === 'all') {
                    $result = $this->clusterModel->resetAllData();
                } else {
                    $result = $this->clusterModel->resetClusterOnly();
                }
                
                error_log("Reset result: " . json_encode($result));
                
                if ($result['success']) {
                    $this->setFlashMessage('success', $result['message']);
                    error_log("Success message set: " . $result['message']);
                } else {
                    $this->setFlashMessage('error', $result['message']);
                    error_log("Error message set: " . $result['message']);
                }
            } catch (Exception $e) {
                error_log("Reset exception: " . $e->getMessage());
                $this->setFlashMessage('error', 'Reset gagal: ' . $e->getMessage());
            }
        } else {
            error_log("Invalid request method for reset");
        }
        
        error_log("About to redirect...");
        $this->redirect('cluster');
    }

    public function getSystemInfo()
    {
        try {
            $info = $this->clusterModel->getSystemInfo();
            
            $data = [
                'title' => 'System Information',
                'info' => $info
            ];
            
            $this->view('cluster/system_info', $data);
        } catch (Exception $e) {
            error_log("Get system info error: " . $e->getMessage());
            $this->showError('Gagal memuat system info: ' . $e->getMessage());
        }
    }

    public function optimize()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $result = $this->clusterModel->optimizeDatabase();
                
                if ($result['success']) {
                    $this->setFlashMessage('success', $result['message']);
                } else {
                    $this->setFlashMessage('error', $result['message']);
                }
            } catch (Exception $e) {
                error_log("Optimize error: " . $e->getMessage());
                $this->setFlashMessage('error', 'Optimasi gagal: ' . $e->getMessage());
            }
        }
        
        $this->redirect('cluster');
    }

    public function apiGetStatistics()
    {
        $this->clearOutput();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $statistics = $this->clusterModel->getClusterStatistics();
            echo json_encode([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function apiGetSummary()
    {
        $this->clearOutput();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $summary = $this->clusterModel->getClusterSummary();
            echo json_encode([
                'success' => true,
                'data' => $summary
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function apiPerformClustering()
    {
        $this->clearOutput();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $result = $this->clusterModel->performKMeansClustering();
                echo json_encode($result);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }
        exit;
    }

    public function apiGetResults()
    {
        $this->clearOutput();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
            $results = $this->clusterModel->getClusterResults($limit);
            
            echo json_encode([
                'success' => true,
                'data' => $results,
                'total' => count($results)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function apiReset()
    {
        $this->clearOutput();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $type = isset($input['type']) ? $input['type'] : 'cluster';
                
                if ($type === 'all') {
                    $result = $this->clusterModel->resetAllData();
                } else {
                    $result = $this->clusterModel->resetClusterOnly();
                }
                
                echo json_encode($result);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }
        exit;
    }

    private function view($view, $data = [])
    {
        extract($data);
        
        $viewPath = "view/{$view}.php";
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("View file not found: {$viewPath}");
        }
    }

    private function redirect($page)
    {
        $this->clearOutput();
        
        if (headers_sent($file, $line)) {
            error_log("Headers already sent at {$file}:{$line}");
            echo "<script>
                setTimeout(function() { 
                    window.location.href = 'index.php?controller=cluster&action=index'; 
                }, 100);
            </script>";
            exit;
        }
        
        header("Location: index.php?controller=cluster&action=index");
        exit;
    }

    private function setFlashMessage($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    private function clearOutput()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }

    private function showError($message)
    {
        echo "<div class='container mt-4'>";
        echo "<div class='alert alert-danger' role='alert'>";
        echo "<h4 class='alert-heading'>Error!</h4>";
        echo "<p>" . htmlspecialchars($message) . "</p>";
        echo "<hr>";
        echo "<a href='index.php?page=cluster' class='btn btn-primary'>Kembali ke Cluster</a>";
        echo "</div>";
        echo "</div>";
    }
}