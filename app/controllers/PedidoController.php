<?php
require_once './models/Pedido.php';
//require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $estado = $parametros['estado'];
        $idMesa = $parametros['idMesa'];
        $precio = $parametros['precio'];
        $nombreCliente = $parametros['nombreCliente'];

        // Creamos el pedido
        $ped = new Pedido();
        $ped->estado = $estado;
        $ped->idMesa = $idMesa;
        $ped->precio = $precio;
        $ped->nombreCliente = $nombreCliente;
        $ped->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por idMesa
        $ped = $args['idMesa'];
        $pedido = Pedido::obtenerPedido($ped);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $id = $parametros['id'];
        $estado = $parametros['estado'];
        $idMesa = $parametros['idMesa'];
        $precio = $parametros['precio'];
        $nombreCliente = $parametros['nombreCliente'];
        Pedido::modificarPedido($id, $estado, $idMesa, $precio, $nombreCliente);
        
        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
      }
      
      public function BorrarUno($request, $response, $args)
      {
        $idPedido = $args['id'];
        Pedido::borrarPedido($idPedido);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}