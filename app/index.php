<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './utils/AutentificadorJWT.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

require_once './middlewares/AuthMiddleware.php';
require_once './middlewares/LoggerMiddleware.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos')->add(\LoggerMiddleware::class . ':verificarRol');
  $group->get('/GuardarCSV', \UsuarioController::class . ':GuardarCSV');
  $group->get('/{id}', \UsuarioController::class . ':TraerUno');
  $group->post('/CargarCSV', \UsuarioController::class . ':CargarCSV');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
  $group->put('[/]', \UsuarioController::class . ':ModificarUno');
  $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/GuardarCSV', \ProductoController::class . ':GuardarCSV');
  $group->get('/{nombre}', \ProductoController::class . ':TraerUno');
  $group->post('/CargarCSV', \ProductoController::class . ':CargarCSV');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->put('[/]', \ProductoController::class . ':ModificarUno');
  $group->delete('/{id}', \ProductoController::class . ':BorrarUno');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/GuardarCSV', \MesaController::class . ':GuardarCSV');
  $group->get('/{id}', \MesaController::class . ':TraerUno');
  $group->post('/CargarCSV', \MesaController::class . ':CargarCSV');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->put('[/]', \MesaController::class . ':ModificarUno');
  $group->delete('/{id}', \MesaController::class . ':BorrarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/GuardarCSV', \PedidoController::class . ':GuardarCSV');
  $group->get('/{idMesa}', \PedidoController::class . ':TraerUno');
  $group->post('/CargarCSV', \PedidoController::class . ':CargarCSV');
  $group->post('[/]', \PedidoController::class . ':CargarUno');
  $group->put('[/]', \PedidoController::class . ':ModificarUno');
  $group->delete('/{id}', \PedidoController::class . ':BorrarUno');
});


$app->post('/login', \UsuarioController::class . ':LoginUsuario'); // Pasar nombre y clave existente










// JWT test
$app->group('/jwt', function (RouteCollectorProxy $group) {

  $group->post('/crearToken', function (Request $request, Response $response) {    
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $contraseña = $parametros['contraseña'];
    $sector = $parametros['sector'];

    $datos = array('usuario' => $usuario, 'contraseña' => $contraseña, 'sector' => $sector);

    $token = AutentificadorJWT::CrearToken($datos);
    $payload = json_encode(array('jwt' => $token));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/devolverPayLoad', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/devolverDatos', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/verificarToken', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $esValido = false;

    try {
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $payload = json_encode(array('valid' => $esValido));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });
});

// JWT en login
$app->group('/auth', function (RouteCollectorProxy $group) {

  $group->post('/login', function (Request $request, Response $response) {    
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $contraseña = $parametros['contraseña'];

    if($usuario == 'prueba' && $contraseña == '1234'){ // EJEMPLO!!! Acá se deberia ir a validar el usuario contra la DB
      $datos = array('usuario' => $usuario);

      $token = AutentificadorJWT::CrearToken($datos);
      $payload = json_encode(array('jwt' => $token));
    } else {
      $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

});


$app->run();