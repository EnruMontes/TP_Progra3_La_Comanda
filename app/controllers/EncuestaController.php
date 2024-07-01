<?php
require_once './models/Encuesta.php';

class EncuestaController extends Encuesta
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idPedido = $parametros['idPedido'];
        $puntajeMozo = $parametros['puntajeMozo'];
        $puntajeMesa = $parametros['puntajeMesa'];
        $puntajeRestaurante = $parametros['puntajeRestaurante'];
        $puntajeCocinero = $parametros['puntajeCocinero'];
        $descripcion = $parametros['descripcion'];
    
        $encuesta = new Encuesta();
        $encuesta->idPedido = $idPedido;
        $encuesta->puntajeMozo = $puntajeMozo;
        $encuesta->puntajeMesa = $puntajeMesa;
        $encuesta->puntajeRestaurante = $puntajeRestaurante;
        $encuesta->puntajeCocinero = $puntajeCocinero;
        $encuesta->promedio = $encuesta->calcularPromedio();
        $encuesta->descripcion = $descripcion;
        $encuesta->crearEncuesta();
    
        $payload = json_encode(array("mensaje" => "Encuesta cargada con exito"));
    
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Encuesta::obtenerTodos();
        $payload = json_encode(array("Lista encuestas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarMejoresComentarios($request, $response, $args)
    {
        $lista = Encuesta::traerMejoresComentarios();
        $payload = json_encode(array("Mejores comentarios" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}