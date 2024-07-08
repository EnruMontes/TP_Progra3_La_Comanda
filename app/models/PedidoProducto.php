<?php

class PedidoProducto
{
    public $id;
    public $codigoMesa;
    public $codigoPedido;
    public $idProducto;
    public $nombreProducto;
    public $cantidad;
    public $precioTotal;
    public $encargado;
    public $estado;
    public $tiempoPreparacion;

    public function crearPedidoProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        // Insertar el pedido inicialmente
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos_productos (codigoPedido, cantidad, estado, tiempoPreparacion) VALUES (:codigoPedido, :cantidad, :estado, :tiempoPreparacion)");
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPreparacion', null, PDO::PARAM_INT);
        $consulta->execute();
        
        $ultimoId = $objAccesoDatos->obtenerUltimoId();
    
        $actualizarConsulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos_productos pp
            INNER JOIN productos p ON p.id = :idProducto
            INNER JOIN pedidos pd ON pd.codigoPedido = pp.codigoPedido
            SET pp.nombreProducto = p.nombre, 
                pp.encargado = p.encargado, 
                pp.precioTotal = p.precio * pp.cantidad, 
                pp.codigoMesa = pd.codigoMesa
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

    public static function existePedidoProductoCodigoPedido($codigoPedido)
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos_productos WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
    
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function existePedidoProductoEncargado($encargado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM pedidos_productos WHERE encargado = :encargado");
        $consulta->bindValue(':encargado', $encargado, PDO::PARAM_STR);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }
    
    public static function obtenerPorEncargadoPendiente($encargado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos_productos WHERE encargado = :encargado AND estado = 'pendiente'");
        $consulta->bindValue(':encargado', $encargado, PDO::PARAM_STR);
        $consulta->execute();
    
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function modificarPedidoProducto($id, $tiempoPreparacion, $encargado, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos_productos SET estado = :estado, tiempoPreparacion = :tiempoPreparacion WHERE id = :id AND encargado = :encargado AND estado = 'en preparacion'");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPreparacion', $tiempoPreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':encargado', $encargado, PDO::PARAM_STR);
        
        $resultado = $consulta->execute();
    
        return $resultado !== false;
    }

    public static function existePedidoIdPendiente($id, $encargado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM pedidos_productos WHERE id = :id AND estado = 'pendiente' AND encargado = :encargado");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':encargado', $encargado, PDO::PARAM_STR);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }

    public static function obtenerTiempoPedido($codigoMesa, $codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "SELECT AVG(tiempoPreparacion) AS promedio_Tiempo_Preparacion
        FROM pedidos_productos
        WHERE tiempoPreparacion IS NOT NULL
        AND codigoMesa = :codigoMesa
        AND codigoPedido = :codigoPedido"
        );
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function generarTiempoEstimado($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT AVG(tiempoPreparacion) AS promedio_Tiempo_Preparacion
            FROM pedidos_productos
            WHERE tiempoPreparacion IS NOT NULL
            AND codigoPedido = :codigoPedido"
        );
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
        
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        $promedioTiempoPreparacion = $resultado['promedio_Tiempo_Preparacion'];
    
        $consultaActualizar = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos
            SET tiempoEstimadoPedido = :promedioTiempoPreparacion
            WHERE codigoPedido = :codigoPedido"
        );
        $consultaActualizar->bindValue(':promedioTiempoPreparacion', $promedioTiempoPreparacion, PDO::PARAM_STR);
        $consultaActualizar->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consultaActualizar->execute();
    }

    public static function obtenerPedidosConTiempoEstimado($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempoEstimadoPedido FROM pedidos WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorEncargadoPreparacion($encargado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos_productos WHERE encargado = :encargado AND estado = 'en preparacion'");
        $consulta->bindValue(':encargado', $encargado, PDO::PARAM_STR);
        $consulta->execute();
    
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function existePedidoIdPreparacion($id, $encargado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM pedidos_productos WHERE id = :id AND estado = 'en preparacion' AND encargado = :encargado");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':encargado', $encargado, PDO::PARAM_STR);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }

    public static function existePedidoEstadoListo()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM pedidos_productos WHERE estado = 'listo para servir'");
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }

    public static function modificarEstadoMesa($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "UPDATE mesas m
        JOIN pedidos_productos pp ON m.codigo = pp.codigoMesa
        SET m.estado = :estado
        WHERE pp.estado = 'listo para servir'");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        
        $resultado = $consulta->execute();
    
        return $resultado !== false;
    }
}