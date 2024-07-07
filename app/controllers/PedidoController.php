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
      
      if(isset($codigoMesa, $nombreCliente, $estado))
      {
        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->codigoMesa = $codigoMesa;
        $pedido->codigoPedido = Pedido::crearCodigoPedido();
        $pedido->nombreCliente = $nombreCliente;
        $pedido->estado = $estado;
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito", "codigoPedido" => $pedido->codigoPedido));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se pudo crear el pedido, se pasaron mal los parametros"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos pedido por id
      $id = $args['id'];

      if(Pedido::existePedido($id))
      {
        $pedido = Pedido::obtenerPedido($id);
        $payload = json_encode($pedido);
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro el pedido con el id: " . $id));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Pedido::obtenerTodos();

      if(isset($lista))
      {
        $payload = json_encode(array("listaPedidos" => $lista));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontraron pedidos para listar"));
      }

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

      if(Pedido::existePedido($id))
      {
        Pedido::modificarPedido($id, $codigoMesa, $nombreCliente, $estado);
        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro el pedido con el id: " . $id));
      }
      
      $response->getBody()->write($payload);
      return $response
      ->withHeader('Content-Type', 'application/json');
    }
      
    public function BorrarUno($request, $response, $args)
    {
      $idPedido = $args['id'];
      
      if(Pedido::existePedido($idPedido))
      {
        Pedido::borrarPedido($idPedido);
        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro el pedido con el id: " . $idPedido));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function GuardarCSV($request, $response, $args) // GET
    {
      $nombreArchivo = "pedidos.csv";
      $ruta = "archivos/" . $nombreArchivo;

      if($archivo = fopen($ruta, "w"))
      {
        $lista = Pedido::obtenerTodos();
        foreach( $lista as $pedido )
        {
          fputcsv($archivo, [$pedido->id, $pedido->codigoMesa, $pedido->nombreCliente, $pedido->estado, $pedido->rutaFoto]);
        }
        fclose($archivo);

        // Leer el archivo CSV recién creado
        $csvContent = file_get_contents($ruta);

        // Establecer la respuesta con el contenido del archivo CSV
        $response->getBody()->write($csvContent);
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename=' . $nombreArchivo);
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo abrir el archivo"));
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
        $payload =  json_encode(array("mensaje" => "No se pudo leer el archivo"));
      }
      
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function guardarFoto($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoMesa = $parametros['codigoMesa'];
      $archivo = isset($_FILES['foto']) ? $_FILES['foto'] : null;

      if(isset($codigoMesa) && $archivo['name'] != "")
      {
        $tempFilePath = $archivo['tmp_name']; // Ruta temporal del archivo
        Pedido::guardarImagenPedido("archivos/ImagenesPedidos/", $codigoMesa, $tempFilePath);
        Pedido::guardarImagenSQL($codigoMesa);

        $payload = json_encode(array("mensaje" => "Imagen guardada con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "Hubo un error al guardar la foto"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}