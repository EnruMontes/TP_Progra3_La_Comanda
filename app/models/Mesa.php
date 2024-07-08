<?php

class Mesa
{
    public $id;
    public $codigo;
    public $estado;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (estado, codigo) VALUES (:estado, :codigo)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa($id, $codigo, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET codigo = :codigo, estado = :estado WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public function crearMesaCSV()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $rta = null;

        if(!(Mesa::existeMesa($this->id)))
        {
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (id, codigo, estado) VALUES (:id, :codigo, :estado)");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->execute();
            
            $rta = $objAccesoDatos->obtenerUltimoId();
        }
        else
        {
            Mesa::modificarMesa($this->id, $this->codigo, $this->estado);
            $rta = $objAccesoDatos->obtenerUltimoId();
        }

        return $rta;
    }
    
    public static function existeMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }

    public static function crearCodigoMesa($longitud = 5) 
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigoRandom = '';
        $max = strlen($caracteres) - 1;
    
        for ($i = 0; $i < $longitud; $i++) {
            $codigoRandom .= $caracteres[random_int(0, $max)];
        }
    
        return $codigoRandom;
    }

    public static function existeMesaEstado($codigoMesa, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM mesas WHERE codigo = :codigoMesa AND estado = :estado");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        
        // Verifica si la consulta devuelve alguna fila
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        // Devuelve true si se encontró una fila, false si no
        return $resultado !== false;
    }

    public static function modificarEstadoMesa($codigo, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = :estado WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
    }
}