<?php

class Pedido
{
    public $id;
    public $codigoMesa;
    public $nombreCliente;
    public $estado;
    public $rutaFoto;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigoMesa, nombreCliente, estado, rutaFoto) VALUES (:codigoMesa, :nombreCliente, :estado, :rutaFoto)");
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':rutaFoto', $this->rutaFoto, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoMesa, nombreCliente, estado, rutaFoto FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoMesa, nombreCliente, estado, rutaFoto FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($id, $codigoMesa, $nombreCliente, $estado, $rutaFoto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET codigoMesa = :codigoMesa, nombreCliente = :nombreCliente, estado = :estado, rutaFoto = :rutaFoto WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':rutaFoto', $rutaFoto, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public function crearPedidoCSV()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $rta = null;

        if(!($this->existeProducto()))
        {
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (id, codigoMesa, nombreCliente, estado, rutaFoto) VALUES (:id, :codigoMesa, :nombreCliente, :estado, :rutaFoto");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
            $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':rutaFoto', $this->rutaFoto, PDO::PARAM_STR);
            $consulta->execute();
            
            $rta = $objAccesoDatos->obtenerUltimoId();
        }
        else
        {
            Pedido::modificarPedido($this->id, $this->codigoMesa, $this->nombreCliente, $this->estado, $this->rutaFoto);   
            $rta = $objAccesoDatos->obtenerUltimoId();
        }

        return $rta;
    }
    
    public function existeProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }
}