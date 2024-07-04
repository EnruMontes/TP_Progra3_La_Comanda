<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoMesa = $parametros['codigoMesa'];
      $nombreCliente = $parametros['nombreCliente'];
      $estado = $parametros['estado'];
      $rutaFoto = $parametros['rutaFoto'];

      // Creamos el pedido
      $ped = new Pedido();
      $ped->codigoMesa = $codigoMesa;
      $ped->nombreCliente = $nombreCliente;
      $ped->estado = $estado;
      $ped->rutaFoto = $rutaFoto;
      $ped->crearPedido();

      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por id
        $ped = $args['id'];
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
        $codigoMesa = $parametros['codigoMesa'];
        $nombreCliente = $parametros['nombreCliente'];
        $estado = $parametros['estado'];
        $rutaFoto = $parametros['rutaFoto'];
        Pedido::modificarPedido($id, $codigoMesa, $nombreCliente, $estado, $rutaFoto);
        
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

      if($archivo = fopen($nombreArchivo, "w"))
      {
        $lista = Pedido::obtenerTodos();
        foreach( $lista as $pedido )
        {
          fputcsv($archivo, [$pedido->id, $pedido->codigoMesa, $pedido->nombreCliente, $pedido->estado, $pedido->rutaFoto]);
        }
        fclose($archivo);

        // Leer el archivo CSV recién creado
        $csvContent = file_get_contents($nombreArchivo);

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

    public function CargarCSV($request, $response, $args)
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
          $nuevoPedido->codigoMesa = $filaPedido[1];
          $nuevoPedido->nombreCliente = $filaPedido[2];
          $nuevoPedido->estado = $filaPedido[3];
          $nuevoPedido->rutaFoto = $filaPedido[4];
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