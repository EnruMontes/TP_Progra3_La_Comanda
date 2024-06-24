<?php
require_once './models/Pedido.php';
//require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
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

    public function GuardarCSV($request, $response, $args) // GET
    {
      if($archivo = fopen("csv/pedidos.csv", "w"))
      {
        $lista = Pedido::obtenerTodos();
        foreach( $lista as $pedido )
        {
            fputcsv($archivo, [$pedido->id, $pedido->estado, $pedido->idMesa, $pedido->precio, $pedido->nombreCliente]);
        }
        fclose($archivo);
        $payload =  json_encode(array("mensaje" => "La lista de pedidos se guardo correctamente"));
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo abrir el archivo de pedidos.csv"));
      }
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargarCSV($request, $response, $args) // GET
    {
      if(($archivo = fopen("csv/pedidos.csv", "r")) !== false)
      {
        Pedido::borrarPedidos();
        while (($filaPedido = fgetcsv($archivo, 0, ',')) !== false)
        {
          $nuevoPedido = new Pedido();
          $nuevoPedido->id = $filaPedido[0];
          $nuevoPedido->estado = $filaPedido[1];
          $nuevoPedido->idMesa = $filaPedido[2];
          $nuevoPedido->precio = $filaPedido[3];
          $nuevoPedido->nombreCliente = $filaPedido[4];
          $nuevoPedido->crearPedidoCSV();
        }
        fclose($archivo);
        $payload =  json_encode(array("mensaje" => "Los pedidos se cargaron correctamente"));
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo leer el archivo de pedidos.csv"));
      }
                
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}