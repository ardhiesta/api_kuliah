<?php
// class untuk melakukan query db ke tabel tbl_mahasiswa
class MahasiswaMapper extends Mapper
{
	// mengambil semua record di tbl_mahasiswa
    public function getAllMhs() {
        $sql = "SELECT * from tbl_mahasiswa";
        $stmt = $this->db->query($sql);
        
        $results = $stmt->fetchAll();
        
        return $results;
    }

	// mengambil record dari tbl_mahasiswa berdasarkan nim mahasiswa
    public function getMahasiswaByNim($nim) {
        $sql = "SELECT * from tbl_mahasiswa where nim = :nim";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["nim" => $nim]);

        if($result) {
            return $stmt->fetch();
        }

    }

	// memasukkan data mahasiswa baru ke tbl_mahasiswa
    public function saveMahasiswa($nim, $nama, $alamat) {
        $sql = "insert into tbl_mahasiswa
            (nim, nama, alamat) values
            (:nim, :nama, :alamat)";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "nim" => $nim,
            "nama" => $nama,
            "alamat" => $alamat,
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }
    }
    
    // mengupdate data mahasiswa
    public function updateMahasiswa($old_nim, $nim, $nama, $alamat) {
        $sql = "update tbl_mahasiswa set nim = :nim, nama = :nama, alamat = :alamat where nim = :old_nim";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "old_nim" => $old_nim,
            "nim" => $nim,
            "nama" => $nama,
            "alamat" => $alamat,
        ]);

        if(!$result) {
            throw new Exception("could not update record");
        }
    }
    
    // menghapus data mahasiswa
    public function deleteMahasiswa($nim) {
		$sql = "delete from tbl_mahasiswa where nim = :nim";
		
		$stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "nim" => $nim
        ]);

        if(!$result) {
            throw new Exception("could not delete record");
        }
	}
}
