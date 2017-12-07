<?php
// abstract class PHP untuk memuat object db
// dari class ini diturunkan ke class lain untuk melakukan query db
abstract class Mapper {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }
}
