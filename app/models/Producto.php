<?php

class Producto
{
    public $id;
    public $nombre;
    public $precio;
    public $tiempoEstimado;
    public $encargado;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre, precio, tiempoEstimado, encargado) VALUES (:nombre, :precio, :tiempoEstimado, :encargado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':encargado', $this->encargado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProductoPorNombre($producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $producto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function modificarProducto($id, $nombre, $precio, $tiempoEstimado, $encargado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE productos SET nombre = :nombre, precio = :precio, tiempoEstimado = :tiempoEstimado, encargado = :encargado WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $precio, PDO::PARAM_INT);
        $consulta->bindValue(':tiempoEstimado', $tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':encargado', $encargado, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public function crearProductoCSV()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $rta = null;

        if(!(Producto::existeProducto($this->id)))
        {
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (id, nombre, precio, tiempoEstimado, encargado) VALUES (:id, :nombre, :precio, :tiempoEstimado, :encargado)");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
            $consulta->bindValue(':encargado', $this->encargado, PDO::PARAM_STR);
            $consulta->execute();
            
            $rta = $objAccesoDatos->obtenerUltimoId();
        }
        else
        {
            Producto::modificarProducto($this->id, $this->nombre, $this->precio, $this->tiempoEstimado, $this->encargado);
            $rta = $objAccesoDatos->obtenerUltimoId();
        }

        return $rta;
    }
    
    public static function existeProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }

    public static function existeProductoPorNombre($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_INT);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }
}