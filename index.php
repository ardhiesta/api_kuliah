<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

$config['addContentLengthHeader'] = false;
$config['displayErrorDetails'] = true;
// host atau alamat server mysql, bisa gunakan ip address
$config['db']['host']   = "localhost";
// username mysql
$config['db']['user']   = "user";
// password mysql
$config['db']['pass']   = "pass";
// nama database
$config['db']['dbname'] = "db_kuliah";

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

// memasukkan setting db
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

/* URL http://[host]/[namafolder]
 method GET
 menampilkan tulisan API Mahasiswa */
$app->get('/', function(){
    echo "API Mahasiswa";
});

/* URL http://[host]/[namafolder]/mahasiswa/all 
 method GET
 menampilkan data semua mahasiswa dari database */
$app->get('/mahasiswa/all', function(){
    //$this->logger->addInfo("Mahasiswa list");
    $mapper = new MahasiswaMapper($this->db);
    $mahasiswa = $mapper->getAllMhs();
    
    return json_encode($mahasiswa);
});

/* URL http://[host]/[namafolder]/mahasiswa/{nim}
 method GET
 menampilkan mahasiswa sesuai nim yang dimaksud */
$app->get('/mahasiswa/{nim}', function (Request $request, Response $response, $args) {
    $mapper = new MahasiswaMapper($this->db);
    $mahasiswa = $mapper->getMahasiswaByNim($args['nim']);
    return json_encode($mahasiswa);
});

/* URL http://[host]/[namafolder]/mahasiswa/new
 method POST
 menambahkan data mahasiswa
 
 pakai header
 Content-type: application/json
 
 contoh request body
 {
 "nim":"m2",
 "nama":"nama2",
 "alamat":"alamat2"
 }
*/
$app->post('/mahasiswa/new', function (Request $request, Response $response) {
	$data = $request->getParsedBody();
	$mapper = new MahasiswaMapper($this->db);
	$mapper->saveMahasiswa($data['nim'], $data['nama'], $data['alamat']);
	
	$obj = (object) [
		'status' => strval($response->getStatusCode())
	];
	
	echo json_encode($obj);
});

/* URL http://[host]/[namafolder]/mahasiswa/update/{old_nim}
 method PUT
 mengubah data mahasiswa sesuai nim yang dimaksud
 
 pakai header
  Content-type: application/json
  contoh request body
 {
 "nim":"m3",
 "nama":"nama2",
 "alamat":"alamat3"
 }

 "old_nim":"m3"
 old_nim ditambahkan ke url
*/
$app->put('/mahasiswa/update/{old_nim}', function (Request $request, Response $response, $args) {
	$data = $request->getParsedBody();
	$mapper = new MahasiswaMapper($this->db);
	$mapper->updateMahasiswa($args['old_nim'], $data['nim'], $data['nama'], $data['alamat']);
	
	$obj = (object) [
		'status' => strval($response->getStatusCode())
	];
	
	echo json_encode($obj);
});

/* URL http://[host]/[namafolder]/mahasiswa/{nim}
 method DELETE
 menghapus mahasiswa sesuai nim yang dimaksud */
$app->delete('/mahasiswa/{nim}', function (Request $request, Response $response, $args) {
	$mapper = new MahasiswaMapper($this->db);
    $mahasiswa = $mapper->deleteMahasiswa($args['nim']);

    $obj = (object) [
		'status' => strval($response->getStatusCode())
	];
	
	echo json_encode($obj);
});

$app->run();

/*
 * ubah composer.json, masukkan autoload
 * composer dump-autoload */
