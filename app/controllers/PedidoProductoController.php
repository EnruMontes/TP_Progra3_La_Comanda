<?php
require_once './models/PedidoProducto.php';
require_once './models/Pedido.php';

class PedidoProductoController extends PedidoProducto
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoPedido = $parametros['codigoPedido'];
        $idProductos = json_decode($parametros['idProductos'], true);
        $cantidades = json_decode($parametros['cantidades'], true);
        $i = 0;

        if(isset($codigoPedido, $idProductos, $cantidades))
        {
            foreach($idProductos as $idProd)
            {
                // Creamos la comanda
                $comanda = new PedidoProducto();
                $comanda->codigoPedido = $codigoPedido;
                $comanda->idProducto = $idProd;
                $comanda->cantidad = $cantidades[$i];
                $comanda->crearPedidoProducto();
                $i++;
            }

            $payload = json_encode(array("mensaje" => "Comanda creada con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se pudo crear la comanda, se pasaron mal los parametros"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorCodigoPedido($request, $response, $args)
    {
        // Buscamos pedido por codigo de pedido
        $codigoPedido = $args['codigoPedido'];

        if(PedidoProducto::existePedidoProductoCodigoPedido($codigoPedido))
        {
            $pedidos = PedidoProducto::obtenerPorCodigoPedido($codigoPedido);
            $payload = json_encode(array("Lista pedidos" => $pedidos)); // No traer idPedido
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se encontro pedidos con el codigo de pedido: " . $codigoPedido));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorTipoEmpleadoPendiente($request, $response, $args)
    {
        // Buscamos el sector que ocupa el token y se lista los de ese sector
        $header = $request->getHeaderLine('Authorization');
        $token = isset($header[1]) ? trim(explode("Bearer", $header)[1]) : null;
        $parametros = (array)AutentificadorJWT::ObtenerData($token);
        $tipoEmpleado = $parametros['sector'];

        if(PedidoProducto::existePedidoProductoEncargado($tipoEmpleado))
        {
            $pedidos = PedidoProducto::obtenerPorEncargadoPendiente($tipoEmpleado);
            $payload = json_encode(array("Lista pedidos" => $pedidos));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se encontro pedidos pendientes con el encargado: " . $tipoEmpleado));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TomarPedidos($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $id = $parametros['id'];
      $tiempoPreparacion = $parametros['tiempoPreparacion'];
      $estado = $parametros['estado'];

      $header = $request->getHeaderLine('Authorization');
      $token = isset($header[1]) ? trim(explode("Bearer", $header)[1]) : null;
      $parametros = (array)AutentificadorJWT::ObtenerData($token);
      $tipoEmpleado = $parametros['sector'];

      if(PedidoProducto::existePedidoIdPendiente($id, $tipoEmpleado))
      {
        PedidoProducto::modificarPedidoProducto($id, $tiempoPreparacion, $tipoEmpleado, $estado);
        $payload = json_encode(array("mensaje" => "Pedido tomado con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro el pedido con estado pendiente con el id: " . $id));
      }
      
      $response->getBody()->write($payload);
      return $response
      ->withHeader('Content-Type', 'application/json');
    }

    public function VerTiempoPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoMesa = $parametros['codigoMesa'];
        $codigoPedido = $parametros['codigoPedido'];

        if(isset($codigoMesa, $codigoPedido))
        {
            $tiempoPromedio = PedidoProducto::obtenerTiempoPedido($codigoMesa, $codigoPedido);
            $payload = json_encode(array("Promedio tiempo pedido" => $tiempoPromedio));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se encontro pedidos con el codigo de pedido: " . $codigoPedido . " y codigo de mesa: " . $codigoMesa));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function GenerarTiempoPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoPedido = $parametros['codigoPedido'];

        if(PedidoProducto::existePedidoProductoCodigoPedido($codigoPedido))
        {
            PedidoProducto::generarTiempoEstimado($codigoPedido);
            $pedidoConTiempo = PedidoProducto::obtenerPedidosConTiempoEstimado($codigoPedido);
            $payload = json_encode(array("Tiempo estimado pedido" => $pedidoConTiempo));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se encontraron pedidos pendientes para listar"));
        }
  
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorTipoEmpleadoPreparacion($request, $response, $args)
    {
        // Buscamos el sector que ocupa el token y se lista los de ese sector
        $header = $request->getHeaderLine('Authorization');
        $token = isset($header[1]) ? trim(explode("Bearer", $header)[1]) : null;
        $parametros = (array)AutentificadorJWT::ObtenerData($token);
        $tipoEmpleado = $parametros['sector'];

        if(PedidoProducto::existePedidoProductoEncargado($tipoEmpleado))
        {
            $pedidos = PedidoProducto::obtenerPorEncargadoPreparacion($tipoEmpleado);
            $payload = json_encode(array("Lista pedidos en preparacion" => $pedidos));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se encontro pedidos en preparacion con el encargado: " . $tipoEmpleado));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TomarPedidoListo($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $id = $parametros['id'];
      $estado = $parametros['estado'];

      $header = $request->getHeaderLine('Authorization');
      $token = isset($header[1]) ? trim(explode("Bearer", $header)[1]) : null;
      $parametros = (array)AutentificadorJWT::ObtenerData($token);
      $tipoEmpleado = $parametros['sector'];

      if(PedidoProducto::existePedidoIdPreparacion($id, $tipoEmpleado))
      {
        PedidoProducto::modificarPedidoProducto($id, 0, $tipoEmpleado, $estado);
        $payload = json_encode(array("mensaje" => "Pedido tomado con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro el pedido con estado en preparacion con el id: " . $id));
      }
      
      $response->getBody()->write($payload);
      return $response
      ->withHeader('Content-Type', 'application/json');
    }

    public function PedidosListos($request, $response, $args)
    {
        if(PedidoProducto::existePedidoEstadoListo())
        {
          PedidoProducto::modificarEstadoMesa("cliente comiendo");
          $payload = json_encode(array("mensaje" => "Pedido tomado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No hay pedidos listos para servir"));
        }
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}