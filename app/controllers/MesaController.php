<?php
require_once './models/Mesa.php';
//require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $estado = $parametros['estado'];

        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->estado = $estado;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por id
        $mId = $args['id'];
        $mesa = Mesa::obtenerMesa($mId);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $id = $parametros['id'];
        $estado = $parametros['estado'];
        Mesa::modificarMesa($id, $estado);
        
        $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
      }
      
      public function BorrarUno($request, $response, $args)
      {
        $idMesa = $args['id'];
        Mesa::borrarMesa($idMesa);

        $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function GuardarCSV($request, $response, $args) // GET
    {
      if($archivo = fopen("csv/mesas.csv", "w"))
      {
        $lista = Mesa::obtenerTodos();
        foreach( $lista as $mesa )
        {
            fputcsv($archivo, [$mesa->id, $mesa->estado]);
        }
        fclose($archivo);
        $payload =  json_encode(array("mensaje" => "La lista de mesas se guardo correctamente"));
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo abrir el archivo de mesas.csv"));
      }
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargarCSV($request, $response, $args) // GET
    {
      if(($archivo = fopen("csv/mesas.csv", "r")) !== false)
      {
        Mesa::borrarMesas();
        while (($filaMesa = fgetcsv($archivo, 0, ',')) !== false)
        {
          $nuevaMesa = new Mesa();
          $nuevaMesa->id = $filaMesa[0];
          $nuevaMesa->estado = $filaMesa[1];
          $nuevaMesa->crearMesaCSV();
        }
        fclose($archivo);
        $payload =  json_encode(array("mensaje" => "Las mesas se cargaron correctamente"));
      }
      else
      {
        $payload =  json_encode(array("mensaje" => "No se pudo leer el archivo de mesas.csv"));
      }
                
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}