<?php

class Pedido
{
    public $id;
    public $codigoMesa;
    public $codigoPedido;
    public $nombreCliente;
    public $precioFinal;
    public $rutaFoto;
    public $tiempoEstimadoPedido;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigoMesa, codigoPedido, nombreCliente, rutaFoto, tiempoEstimadoPedido) VALUES (:codigoMesa, :codigoPedido, :nombreCliente, :rutaFoto, :tiempoEstimadoPedido)");
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':rutaFoto', null, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimadoPedido', null, PDO::PARAM_STR);
        $consulta->execute();

        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = 'cliente esperando pedido' WHERE codigo = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($id, $codigoMesa, $nombreCliente)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET codigoMesa = :codigoMesa, nombreCliente = :nombreCliente, rutaFoto = :rutaFoto WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $nombreCliente, PDO::PARAM_STR);
        $rutaFoto = $codigoMesa . ".jpg";
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

        if(!(Pedido::existePedido($this->id)))
        {
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (id, codigoMesa, nombreCliente, rutaFoto) VALUES (:id, :codigoMesa, :nombreCliente, :rutaFoto");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
            $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
            $consulta->bindValue(':rutaFoto', $this->rutaFoto, PDO::PARAM_STR);
            $consulta->execute();
            
            $rta = $objAccesoDatos->obtenerUltimoId();
        }
        else
        {
            Pedido::modificarPedido($this->id, $this->codigoMesa, $this->nombreCliente);   
            $rta = $objAccesoDatos->obtenerUltimoId();
        }

        return $rta;
    }
    
    public static function existePedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }

    public static function guardarImagenPedido($path, $codigoMesa, $tempName)
    {
        $rta = false;
        // Ruta a donde se quiere mover el archivo
        $destino = $path . $codigoMesa . ".jpg";
        
        if(move_uploaded_file($tempName, $destino))
        {
            $rta = true;
        }
        return $rta;
    }

    public static function guardarImagenSQL($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET rutaFoto = :rutaFoto WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $rutaFoto = $codigoMesa . ".jpg";
        $consulta->bindValue(':rutaFoto', $rutaFoto, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function crearCodigoPedido($longitud = 5) 
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigoRandom = '';
        $max = strlen($caracteres) - 1;
    
        for ($i = 0; $i < $longitud; $i++) {
            $codigoRandom .= $caracteres[random_int(0, $max)];
        }
    
        return $codigoRandom;
    }
}