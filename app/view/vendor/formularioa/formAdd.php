<?php
// $user = ['privilegio' => 'usuario', 'id' => 1, 'nome' => 'João'];
$table = $currentTable ?? $_GET['table'] ?? null;
if (!$table || !in_array($table, ['categorias', 'produtos'])) {
    die('Tabela inválida');
}
try {
    $form = new FormEngine($table, null, null);
} catch (Exception $e) {
}
?>
<div class="painel-wrapper">
    <form action="/processCategoria" method="post" class="Painel">
        <div class="top-Painel">
            <h3>Adicionar Mais Categorias</h3>
            <hr>
        </div>
        <div class="editar-dados">
            <?= $form->render() ?>
        </div>
        <div class="buttons-cal-conf">
            <button type="button" onclick="buttonVoltar()" id="cancel">Cancelar</button>
            <button id="confirm">Confirmar</button>
        </div>
    </form>