<?php
require_once './models/Encuesta.php';

class EncuestaController extends Encuesta
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoMesa = $parametros['codigoMesa'];
      $puntajeMozo = $parametros['puntajeMozo'];
      $puntajeMesa = $parametros['puntajeMesa'];
      $puntajeRestaurante = $parametros['puntajeRestaurante'];
      $puntajeCocinero = $parametros['puntajeCocinero'];
      $descripcion = $parametros['descripcion'];
  
      if(isset($codigoMesa, $puntajeMozo, $puntajeMesam, $puntajeRestaurante, $puntajeCocinero, $descripcion))
      {
        $encuesta = new Encuesta();
        $encuesta->codigoMesa = $codigoMesa;
        $encuesta->puntajeMozo = $puntajeMozo;
        $encuesta->puntajeMesa = $puntajeMesa;
        $encuesta->puntajeRestaurante = $puntajeRestaurante;
        $encuesta->puntajeCocinero = $puntajeCocinero;
        $encuesta->promedio = $encuesta->calcularPromedio();
        $encuesta->descripcion = $descripcion;
        $encuesta->crearEncuesta();
    
        $payload = json_encode(array("mensaje" => "Encuesta cargada con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se pudo crear la encuesta, se pasaron mal los parametros"));
      }
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Encuesta::obtenerTodos();

      if(isset($lista))
      {
        $payload = json_encode(array("Lista encuestas" => $lista));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontraron encuestas para listar"));
      }
      
      $response->getBody()->write($payload);
      return $response
      ->withHeader('Content-Type', 'application/json');
    }
    
    public function ListarMejoresComentarios($request, $response, $args)
    {
      $lista = Encuesta::traerMejoresComentarios();
      
      if(isset($lista))
      {
        $payload = json_encode(array("Mejores comentarios" => $lista));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se encontraron buenos comentarios para listar para listar (Promedio > 6)"));
      }
      
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}