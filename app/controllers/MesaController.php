<?php
require_once './models/Mesa.php';
//require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $estado = $parametros['estado'];

        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->estado = $estado;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por estado
        $m = $args['estado'];
        $mesa = Mesa::obtenerMesa($m);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $id = $parametros['id'];
        $estado = $parametros['estado'];
        Mesa::modificarMesa($id, $estado);
        
        $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
      }
      
      public function BorrarUno($request, $response, $args)
      {
        $idMesa = $args['id'];
        Mesa::borrarMesa($idMesa);

        $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}