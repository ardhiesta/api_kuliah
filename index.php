<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

$config['addContentLengthHeader'] = false;
$config['displayErrorDetails'] = true;
$config['db']['host']   = "hostname";
$config['db']['user']   = "user";
$config['db']['pass']   = "password";
$config['db']['dbname'] = "dbname";

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->get('/', function(){
    echo "API Mahasiswa";
});

$app->get('/all_mahasiswa', function(){
    //$this->logger->addInfo("Mahasiswa list");
    $mapper = new MahasiswaMapper($this->db);
    $mahasiswa = $mapper->getAllMhs();
    
    return json_encode($mahasiswa);
});

$app->get('/mahasiswa/{nim}', function (Request $request, Response $response, $args) {
    //$ticket_id = (int)$args['id'];
    $mapper = new MahasiswaMapper($this->db);
    $mahasiswa = $mapper->getMahasiswaByNim($args['nim']);

    //$response = $this->view->render($response, "ticketdetail.phtml", ["ticket" => $ticket]);
    return json_encode($mahasiswa);
});

/* post pakai header
 * Content-type: application/json
 * contoh request body
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
	
	echo $response->getStatusCode();
});

/* put pakai header
 * Content-type: application/json
 * contoh request body
 {
 "nim":"m3",
 "nama":"nama2",
 "alamat":"alamat3"
 }

* "old_nim":"m3"
* old_nim ditambahkan ke url
*/
$app->put('/mahasiswa/update/{old_nim}', function (Request $request, Response $response, $args) {
	$data = $request->getParsedBody();
	$mapper = new MahasiswaMapper($this->db);
	$mapper->updateMahasiswa($args['old_nim'], $data['nim'], $data['nama'], $data['alamat']);
	
	echo $response->getStatusCode();
});

$app->delete('/mahasiswa/{nim}', function (Request $request, Response $response, $args) {
	$mapper = new MahasiswaMapper($this->db);
    $mahasiswa = $mapper->deleteMahasiswa($args['nim']);

    echo $response->getStatusCode();
});

$app->run();

/*
 * ubah composer.json, masukkan autoload
 * composer dump-autoload */
