<?php
class categorias
{
    public static function inserirDados()
    {
        $info = $_POST['nome'];
        return  insertCategorias::insert($info);
    }
}
