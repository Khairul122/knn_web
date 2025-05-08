<?php
include_once 'koneksi.php';

class PemeliharaanModel {
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
        
        if (!$this->db) {
            die("Database connection failed. Please check koneksi.php file.");
        }
    }
    
    public function getAllPemeliharaan() {
        $query = "SELECT * FROM data_pemeliharaan ORDER BY tanggal DESC";
        $result = mysqli_query($this->db, $query);
        
        if (!$result) {
            return [];
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function getPemeliharaanByPeriod($bulan, $tahun) {
        $tanggal = mysqli_real_escape_string($this->db, "$bulan-$tahun");
        $query = "SELECT * FROM data_pemeliharaan WHERE tanggal = '$tanggal' ORDER BY tanggal DESC";
        $result = mysqli_query($this->db, $query);
        
        if (!$result) {
            return [];
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function getPemeliharaanByObject($objectType) {
        $objectType = mysqli_real_escape_string($this->db, $objectType);
        $query = "SELECT * FROM data_pemeliharaan WHERE nama_objek = '$objectType' ORDER BY tanggal DESC";
        $result = mysqli_query($this->db, $query);
        
        if (!$result) {
            return [];
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function getSutmDetails($subCategoryId) {
        $subCategoryId = mysqli_real_escape_string($this->db, $subCategoryId);
        $query = "SELECT * FROM sutm WHERE id_sutm = '$subCategoryId'";
        $result = mysqli_query($this->db, $query);
        
        if (!$result) {
            return null;
        }
        
        return mysqli_fetch_assoc($result);
    }
    public function getGarduDetails($subCategoryId) {
        $subCategoryId = mysqli_real_escape_string($this->db, $subCategoryId);
        $query = "SELECT * FROM gardu WHERE id_gardu = '$subCategoryId'";
        $result = mysqli_query($this->db, $query);
        
        if (!$result) {
            return null;
        }
        
        return mysqli_fetch_assoc($result);
    }
}
?>