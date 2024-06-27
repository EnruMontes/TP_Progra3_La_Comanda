<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $usuario = $parametros['usuario'];
        $tiempo = $parametros['tiempo'];
        $estado = $parametros['estado'];

        // Creamos el producto
        $prod = new Producto();
        $prod->nombre = $nombre;
        $prod->usuario = $usuario;
        $prod->tiempo = $tiempo;
        $prod->estado = $estado;
        $prod->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por nombre
        $prod = $args['nombre'];
        $producto = Producto::obtenerProducto($prod);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $id = $parametros['id'];
        $nombre = $parametros['nombre'];
        $usuario = $parametros['usuario'];
        $tiempo = $parametros['tiempo'];
        $estado = $parametros['estado'];
        Producto::modificarProducto($id, $nombre, $usuario, $tiempo, $estado);
        
        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
      }
      
      public function BorrarUno($request, $response, $args)
      {
        $idProducto = $args['id'];
        Producto::borrarProducto($idProducto);

        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function GuardarCSV($request, $response, $args) // GET
    {
      $nombreArchivo = "productos.csv";
      $filePath = "archivos/" . $nombreArchivo;

      if($archivo = fopen($filePath, "w"))
      {
        $lista = Producto::obtenerTodos();
        foreach( $lista as $producto )
        {
            fputcsv($archivo, [$producto->id, $producto->nombre, $producto->usuario, $producto->tiempo, $producto->estado]);
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
          $nuevoProducto = new Producto();
          $nuevoProducto->id = $filaPedido[0];
          $nuevoProducto->nombre = $filaPedido[1];
          $nuevoProducto->usuario = $filaPedido[2];
          $nuevoProducto->tiempo = $filaPedido[3];
          $nuevoProducto->estado = $filaPedido[4];
          $nuevoProducto->crearProductoCSV();
        }
        fclose($handle);
        $payload =  json_encode(array("mensaje" => "Los productos se cargaron correctamente"));
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo leer el archivo de productos.csv"));
      }
                
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}