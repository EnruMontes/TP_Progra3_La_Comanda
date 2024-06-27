<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

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
      $nombreArchivo = "pedidos.csv";
      $filePath = "archivos/" . $nombreArchivo;

      if($archivo = fopen($filePath, "w"))
      {
        $lista = Pedido::obtenerTodos();
        foreach( $lista as $pedido )
        {
            fputcsv($archivo, [$pedido->id, $pedido->estado, $pedido->idMesa, $pedido->precio, $pedido->nombreCliente]);
        }
        fclose($archivo);

        // Leer el archivo CSV recién creado
        $csvContent = file_get_contents($filePath);

        // Establecer la respuesta con el contenido del archivo CSV
        $response->getBody()->write($csvContent);
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename=' . $nombreArchivo);
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo abrir el archivo de pedidos.csv"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
      }
    }

    public function CargarCSV($request, $response, $args) // GET
    {
      $parametros = $request->getUploadedFiles();
      $archivo = isset($parametros['archivo']) ? $parametros['archivo'] : null;
      $tempFilePath = $archivo->getStream()->getMetadata('uri'); // Obtener la ruta temporal del archivo

      if(($handle = fopen($tempFilePath, "r")) !== false)
      {
        while (($filaPedido = fgetcsv($handle, 0, ',')) !== false)
        {
          $nuevoPedido = new Pedido();
          $nuevoPedido->id = $filaPedido[0];
          $nuevoPedido->estado = $filaPedido[1];
          $nuevoPedido->idMesa = $filaPedido[2];
          $nuevoPedido->precio = $filaPedido[3];
          $nuevoPedido->nombreCliente = $filaPedido[4];
          $nuevoPedido->crearPedidoCSV();
        }
        fclose($handle);
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