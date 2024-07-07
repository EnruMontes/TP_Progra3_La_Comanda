<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $estado = $parametros['estado'];

      if(isset($estado))
      {
        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->estado = $estado;
        $mesa->codigo = Mesa::crearCodigoMesa();
        $mesa->crearMesa();
  
        $payload = json_encode(array("mensaje" => "Mesa creada con exito", "codigoMesa" => $mesa->codigo));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se pudo crear la mesa, se pasaron mal los parametros"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos mesa por id
      $id = $args['id'];

      if(Mesa::existeMesa($id))
      {
        $mesa = Mesa::obtenerMesa($id);
        $payload = json_encode($mesa);
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro la mesa con el id: " . $id));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Mesa::obtenerTodos();
      if(isset($lista))
      {
        $payload = json_encode(array("listaMesas" => $lista));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontraron mesas para listar"));
      }

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

      if(Mesa::existeMesa($id))
      {
        Mesa::modificarMesa($id, $codigo, $estado);
        $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro la mesa con el id: " . $id));
      }
      
      $response->getBody()->write($payload);
      return $response
      ->withHeader('Content-Type', 'application/json');
    }
      
    public function BorrarUno($request, $response, $args)
    {
      $idMesa = $args['id'];
      if(Mesa::existeMesa($idMesa))
      {
        Mesa::borrarMesa($idMesa);
        $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontro la mesa con el id: " . $idMesa));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function GuardarCSV($request, $response, $args) // GET
    {
      $nombreArchivo = "mesas.csv";
      $ruta = "archivos/" . $nombreArchivo;

      if($archivo = fopen($ruta, "w"))
      {
        // fputcsv($archivo, ['id', 'estado']);
        $lista = Mesa::obtenerTodos();
        foreach( $lista as $mesa )
        {
          fputcsv($archivo, [$mesa->id, $mesa->codigo, $mesa->estado]);
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
        $payload =  json_encode(array("mensaje" => "No se pudo leer el archivo"));
      }
                
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}