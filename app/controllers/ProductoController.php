<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];
      $precio = $parametros['precio'];
      $tiempoEstimado = $parametros['tiempoEstimado'];
      $encargado = $parametros['encargado'];

      if(isset($nombre, $precio, $encargado))
      {
        // Creamos el producto
        $prod = new Producto();
        $prod->nombre = $nombre;
        $prod->precio = $precio;
        $prod->tiempoEstimado = $tiempoEstimado;
        $prod->encargado = $encargado;
        $prod->crearProducto();
  
        $payload = json_encode(array("mensaje" => "Producto creado con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se pudo crear el producto, se pasaron mal los parametros"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos producto por nombre
      $prodNombre = $args['nombre'];
      $producto = Producto::obtenerProductoPorNombre($prodNombre);

      if($producto)
      {
        $payload = json_encode($producto);
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro el producto con el nombre: " . $prodNombre));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Producto::obtenerTodos();
      if(isset($lista))
      {
        $payload = json_encode(array("listaProductos" => $lista));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontraron productos para listar"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      
      $id = $parametros['id'];
      $nombre = $parametros['nombre'];
      $precio = $parametros['precio'];
      $tiempoEstimado = $parametros['tiempoEstimado'];
      $encargado = $parametros['encargado'];

      if(Producto::existeProducto($id))
      {
        Producto::modificarProducto($id, $nombre, $precio, $tiempoEstimado, $encargado);
        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro el producto con el id: " . $id));
      }
      
      $response->getBody()->write($payload);
      return $response
      ->withHeader('Content-Type', 'application/json');
    }
    
    public function BorrarUno($request, $response, $args)
    {
      $idProducto = $args['id'];
      
      if(isset($idProducto))
      {
        Producto::borrarProducto($idProducto);
        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro el producto con el id: " . $idProducto));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function GuardarCSV($request, $response, $args) // GET
    {
      $nombreArchivo = "productos.csv";
      $ruta = "archivos/" . $nombreArchivo;

      if($archivo = fopen($ruta, "w"))
      {
        $lista = Producto::obtenerTodos();
        foreach( $lista as $producto )
        {
          fputcsv($archivo, [$producto->id, $producto->nombre, $producto->precio, $producto->tiempoEstimado, $producto->encargado]);
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
          $nuevoProducto->precio = $filaPedido[2];
          $nuevoProducto->tiempoEstimado = $filaPedido[3];
          $nuevoProducto->encargado = $filaPedido[4];
          $nuevoProducto->crearProductoCSV();
        }
        fclose($handle);
        $payload =  json_encode(array("mensaje" => "Los productos se cargaron correctamente"));
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo leer el archivo"));
      }
                
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}