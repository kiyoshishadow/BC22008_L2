<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Config\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$app = AppFactory::create();

// Base path configurable via variable de entorno `BASE_PATH`.
// En Render normalmente la app está en la raíz ('/'), mientras que en XAMPP puede ser '/APIBC22008'.
$computedBase = getenv('BASE_PATH') !== false && getenv('BASE_PATH') !== ''
    ? getenv('BASE_PATH')
    : str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Slim funciona mejor con cadena vacía para la raíz '/'.
$basePath = $computedBase === '/' ? '' : $computedBase;
$app->setBasePath($basePath);
$app->addBodyParsingMiddleware();

// Si el cliente usa /index.php en la URL, quita esa parte para que Slim encuentre la ruta normal.
$app->add(function (Request $request, $handler) {
    $uri = $request->getUri();
    $path = $uri->getPath();

    if (str_contains($path, '/index.php')) {
        $path = str_replace('/index.php', '', $path);
        $uri = $uri->withPath($path);
        $request = $request->withUri($uri);
    }

    return $handler->handle($request);
});

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$db = (new DB())->connect();

$app->post('/doctores', function (Request $request, Response $response) use ($db) {
    $data = $request->getParsedBody();
    $data = is_array($data) ? array_filter($data, fn($value) => $value !== null && $value !== '') : [];

    if (empty($data)) {
        $response->getBody()->write(json_encode(['error' => 'Debe enviar datos válidos para crear un doctor.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $columns = array_keys($data);
    $placeholders = array_map(fn($column) => ':' . $column, $columns);
    $sql = sprintf(
        'INSERT INTO doctores (%s) VALUES (%s)',
        implode(', ', $columns),
        implode(', ', $placeholders)
    );

    $stmt = $db->prepare($sql);
    $stmt->execute($data);

    $response->getBody()->write(json_encode(['success' => true, 'id' => $db->lastInsertId()]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->get('/doctores', function (Request $request, Response $response) use ($db) {
    $stmt = $db->query('SELECT * FROM doctores');
    $doctores = $stmt->fetchAll();

    $response->getBody()->write(json_encode($doctores));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/hospitales', function (Request $request, Response $response) use ($db) {
    $stmt = $db->query('SELECT * FROM hospitales');
    $hospitales = $stmt->fetchAll();

    $response->getBody()->write(json_encode($hospitales));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/hospitales', function (Request $request, Response $response) use ($db) {
    $data = $request->getParsedBody();
    $data = is_array($data) ? array_filter($data, fn($value) => $value !== null && $value !== '') : [];

    if (empty($data)) {
        $response->getBody()->write(json_encode(['error' => 'Debe enviar datos válidos para crear un hospital.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $columns = array_keys($data);
    $placeholders = array_map(fn($column) => ':' . $column, $columns);
    $sql = sprintf(
        'INSERT INTO hospitales (%s) VALUES (%s)',
        implode(', ', $columns),
        implode(', ', $placeholders)
    );

    $stmt = $db->prepare($sql);
    $stmt->execute($data);

    $response->getBody()->write(json_encode(['success' => true, 'id' => $db->lastInsertId()]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->get('/hospitales/{id}', function (Request $request, Response $response, array $args) use ($db) {
    $stmt = $db->prepare('SELECT * FROM hospitales WHERE id_hospital = :id');
    $stmt->execute(['id' => $args['id']]);
    $hospital = $stmt->fetch();

    if (!$hospital) {
        $response->getBody()->write(json_encode(['error' => 'Hospital no encontrado.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $response->getBody()->write(json_encode($hospital));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
