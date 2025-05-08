<?php
include_once 'koneksi.php';

class PemeliharaanModel {
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
        
        if (!$this->db) {
            die("Database connection failed.");
        }
    }
}