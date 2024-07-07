<?php

class PedidoProducto
{
    public $id;
    public $codigoPedido;
    public $idProducto;
    public $nombreProducto;
    public $cantidad;
    public $precioTotal;

    public function crearPedidoProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        // Insertar el pedido inicialmente
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos_productos (codigoPedido, cantidad) VALUES (:codigoPedido, :cantidad)");
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->execute();
        
        $ultimoId = $objAccesoDatos->obtenerUltimoId();
    
        // Actualizar nombreProducto y precioTotal basándose en el idProducto
        $actualizarConsulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos_productos pp
             INNER JOIN productos p ON p.id = :idProducto
             SET pp.nombreProducto = p.nombre, pp.precioTotal = p.precio * pp.cantidad
             WHERE pp.id = :id"
        );
        $actualizarConsulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $actualizarConsulta->bindValue(':id', $ultimoId, PDO::PARAM_INT);
        $actualizarConsulta->execute();

        PedidoProducto::actualizarPrecioFinalPedidos();
    
        return $ultimoId;
    }

    public static function actualizarPrecioFinalPedidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos p
            INNER JOIN (
            SELECT codigoPedido, SUM(precioTotal) AS sumaPrecioTotal
            FROM pedidos_productos
            GROUP BY codigoPedido
            ) pp ON p.codigoPedido = pp.codigoPedido
            SET p.precioFinal = pp.sumaPrecioTotal"
        );
        $consulta->execute();
    }

    public static function existePedidoProducto($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM pedidos_productos WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_INT);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }

    public static function obtenerPorCodigoPedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoPedido, nombreProducto, cantidad, precioTotal FROM pedidos_productos WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
    
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }
    
}