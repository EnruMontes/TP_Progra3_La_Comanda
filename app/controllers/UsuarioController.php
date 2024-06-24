<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $id = $parametros['id'];
        Usuario::modificarUsuario($usuario, $clave, $id);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $idUsuario = $args['id'];
        Usuario::borrarUsuario($idUsuario);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function GuardarCSV($request, $response, $args) // GET
    {
      if($archivo = fopen("csv/usuarios.csv", "w"))
      {
        $lista = Usuario::obtenerTodos();
        foreach( $lista as $usuario )
        {
            fputcsv($archivo, [$usuario->id, $usuario->usuario, $usuario->clave]);
        }
        fclose($archivo);
        $payload =  json_encode(array("mensaje" => "La lista de usuarios se guardo correctamente"));
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo abrir el archivo de usuarios.csv"));
      }
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargarCSV($request, $response, $args) // GET
    {
      if(($archivo = fopen("csv/usuarios.csv", "r")) !== false)
      {
        Usuario::borrarUsuarios();
        while (($filaPedido = fgetcsv($archivo, 0, ',')) !== false)
        {
          $nuevoUsuario = new Usuario();
          $nuevoUsuario->id = $filaPedido[0];
          $nuevoUsuario->usuario = $filaPedido[1];
          $nuevoUsuario->clave = $filaPedido[2];
          $nuevoUsuario->crearUsuarioCSV();
        }
        fclose($archivo);
        $payload =  json_encode(array("mensaje" => "Los usuarios se cargaron correctamente"));
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo leer el archivo de usuarios.csv"));
      }
                
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}