<?php
require_once './models/Producto.php';
//require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto
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
}