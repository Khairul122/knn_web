<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Test Clustering System</h2>";

include_once 'koneksi.php';
include_once 'model/ClusterModel.php';

try {
    echo "<h3>1. Database Connection Test</h3>";
    if ($koneksi) {
        echo "✅ Database connected<br>";
    } else {
        echo "❌ Database connection failed<br>";
        exit;
    }

    echo "<h3>2. Data Test</h3>";
    $garduCount = $koneksi->query("SELECT COUNT(*) as count FROM gardu")->fetch_assoc()['count'];
    $sutmCount = $koneksi->query("SELECT COUNT(*) as count FROM sutm")->fetch_assoc()['count'];
    $dataCount = $koneksi->query("SELECT COUNT(*) as count FROM data_pemeliharaan")->fetch_assoc()['count'];
    
    echo "Data Pemeliharaan: $dataCount records<br>";
    echo "Gardu: $garduCount records<br>";
    echo "SUTM: $sutmCount records<br>";

    echo "<h3>3. ClusterModel Test</h3>";
    $model = new ClusterModel();
    
    $combinedData = $model->getCombinedData();
    echo "Combined Data: " . count($combinedData) . " records<br>";
    
    if (count($combinedData) > 0) {
        echo "✅ Data retrieved successfully<br>";
        echo "Sample data:<br>";
        echo "<pre>";
        print_r(array_slice($combinedData, 0, 2));
        echo "</pre>";
    } else {
        echo "❌ No combined data found<br>";
        
        echo "<h4>Debug Query:</h4>";
        $testQuery = "
            SELECT COUNT(*) as total
            FROM data_pemeliharaan dp
            LEFT JOIN gardu g ON dp.nama_objek = 'gardu' AND dp.id_sub_kategori = g.id_gardu
            LEFT JOIN sutm s ON dp.nama_objek = 'sutm' AND dp.id_sub_kategori = s.id_sutm
            WHERE (dp.nama_objek = 'gardu' AND g.id_gardu IS NOT NULL) 
               OR (dp.nama_objek = 'sutm' AND s.id_sutm IS NOT NULL)
        ";
        
        $result = $koneksi->query($testQuery);
        $count = $result->fetch_assoc()['total'];
        echo "Query result: $count records should be found<br>";
        
        if ($count == 0) {
            echo "❌ Problem with JOIN conditions<br>";
            
            $garduJoin = $koneksi->query("
                SELECT COUNT(*) as count 
                FROM data_pemeliharaan dp 
                LEFT JOIN gardu g ON dp.id_sub_kategori = g.id_gardu 
                WHERE dp.nama_objek = 'gardu'
            ")->fetch_assoc()['count'];
            
            $sutmJoin = $koneksi->query("
                SELECT COUNT(*) as count 
                FROM data_pemeliharaan dp 
                LEFT JOIN sutm s ON dp.id_sub_kategori = s.id_sutm 
                WHERE dp.nama_objek = 'sutm'
            ")->fetch_assoc()['count'];
            
            echo "Gardu JOIN: $garduJoin<br>";
            echo "SUTM JOIN: $sutmJoin<br>";
        }
        exit;
    }

    echo "<h3>4. Clustering Test</h3>";
    $result = $model->performKMeansClustering();
    
    if ($result['success']) {
        echo "✅ Clustering successful!<br>";
        echo "Message: " . $result['message'] . "<br>";
        echo "Total data: " . $result['total_data'] . "<br>";
        echo "Iterations: " . $result['iterations'] . "<br>";
        echo "Inserted: " . $result['inserted_records'] . "<br>";
        
        echo "<h3>5. Results Check</h3>";
        $stats = $model->getClusterStatistics();
        echo "<pre>";
        print_r($stats);
        echo "</pre>";
        
    } else {
        echo "❌ Clustering failed: " . $result['message'] . "<br>";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}

echo "<h3>6. Manual Query Test</h3>";
$manualQuery = "SELECT COUNT(*) as count FROM hasil_cluster";
$manualResult = $koneksi->query($manualQuery);
if ($manualResult) {
    $count = $manualResult->fetch_assoc()['count'];
    echo "hasil_cluster table has: $count records<br>";
} else {
    echo "Error querying hasil_cluster: " . $koneksi->error . "<br>";
}
?>