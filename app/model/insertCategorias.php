<?php
class insertCategorias
{
    public static function insert($info)
    {
        $conect = Database::connects();
        $trans = $conect->prepare('insert into categorias(nome) values(?)');
        if (!$trans) throw new Exception("Erro : " . $conect->error);
        $trans->bind_param('s', $info);
        $trans->execute();
        return $trans->get_result();
    }
    public static function delete($id)
    {
        $conect = Database::connects();
        $trans = $conect->prepare('delete from categorias where id=?');
        if (!$trans) throw new Exception("Erro : " . $conect->error);
        $trans->bind_param('i', $id);
        $trans->execute();
        return $trans->get_result()->fetch_assoc();
    }
    public static function altere($info, $id)
    {
        $conect = Database::connects();
        $trans = $conect->prepare('update categorias set nome=? where id=?');
        if (!$trans) throw new Exception("Erro : " . $conect->error);
        $trans->bind_param('si', $info, $id);
        $trans->execute();
        return $trans->get_result()->fetch_assoc();
    }
}
