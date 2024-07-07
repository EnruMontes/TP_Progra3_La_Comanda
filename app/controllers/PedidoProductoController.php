<?php
require_once './models/PedidoProducto.php';

class PedidoProductoController extends PedidoProducto
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoPedido = $parametros['codigoPedido'];
        $idProductos = json_decode($parametros['idProductos'], true);
        $cantidades = json_decode($parametros['cantidades'], true);
        $i = 0;

        if(isset($codigoPedido, $idProductos, $cantidades))
        {
            foreach($idProductos as $idProd)
            {
                // Creamos la comanda
                $comanda = new PedidoProducto();
                $comanda->codigoPedido = $codigoPedido;
                $comanda->idProducto = $idProd;
                $comanda->cantidad = $cantidades[$i];
                $comanda->crearPedidoProducto();
                $i++;
            }

            $payload = json_encode(array("mensaje" => "Comanda creada con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se pudo crear la comanda, se pasaron mal los parametros"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorCodigoPedido($request, $response, $args)
    {
        // Buscamos pedido por codigo de pedido
        $codigoPedido = $args['codigoPedido'];

        if(PedidoProducto::existePedidoProducto($codigoPedido))
        {
            $pedidos = PedidoProducto::obtenerPorCodigoPedido($codigoPedido);
            $payload = json_encode(array("Lista pedidos" => $pedidos)); // No traer idPedido
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No se encontro pedidos con el codigo de pedido: " . $codigoPedido));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}