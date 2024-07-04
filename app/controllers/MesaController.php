<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo = $parametros['codigo'];
    $estado = $parametros['estado'];

    // Creamos la mesa
    $mesa = new Mesa();
    $mesa->estado = $estado;
    $mesa->codigo = $codigo;
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
    $codigo = $parametros['codigo'];
    $estado = $parametros['estado'];
    Mesa::modificarMesa($id, $codigo, $estado);
    
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
    $nombreArchivo = "mesas.csv";

    if($archivo = fopen($nombreArchivo, "w"))
    {
      // fputcsv($archivo, ['id', 'estado']);
      $lista = Mesa::obtenerTodos();
      foreach( $lista as $mesa )
      {
        fputcsv($archivo, [$mesa->id, $mesa->codigo, $mesa->estado]);
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

  public function CargarCSV($request, $response, $args) // POST
  {
    $parametros = $request->getUploadedFiles();
    $archivo = isset($parametros['archivo']) ? $parametros['archivo'] : null;
    $tempFilePath = $archivo->getStream()->getMetadata('uri'); // Obtener la ruta temporal del archivo

    if(($handle = fopen($tempFilePath, "r")) !== false)
    {
      while (($filaMesa = fgetcsv($handle, 0, ',')) !== false)
      {
        $nuevaMesa = new Mesa();
        $nuevaMesa->id = $filaMesa[0];
        $nuevaMesa->codigo = $filaMesa[1];
        $nuevaMesa->estado = $filaMesa[2];
        $nuevaMesa->crearMesaCSV();
      }
      fclose($handle);
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