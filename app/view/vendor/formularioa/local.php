<!-- <div class="painel-wrapper">
    <div class="alert-cep" role="alert">
        Por favor, insira um cep valido.
    </div>
    <p>DDD: <span id="ddd"></span></p>
    <p>UF: <span id="uf"></span></p>
    <p>Locadora: <span id="localidade"></span></p>
    <p>logradouro: <span id="logradouro"></span></p>
    <p>Bairro: <span id="bairro"></span></p>
    <form autocomplete="off" class="form-cep">
        <div class="input-cep">
            <input type="text" name="cep" id="cep" class="dados-cep" placeholder="Digite o CEP">
        </div>
        <button type="submit"> Enviar</button>
    </form>
</div> -->
<?php
// Exemplo de uso em uma página
$user = ['privilegio' => 'admin', 'id' => 1, 'nome' => 'João'];
$form = new FormEngine('produtos', $_GET['id'] ?? null, $user);

// Para processar o envio (você mesmo pode implementar o salvamento)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega os dados do $_POST e $_FILES
    // Valida e salva no banco (não incluso nesta classe)
} ?>
<div class="painel-wrapper">
    <form class="Painel">
        <div class="top-Painel">
            <h3>Produtos</h3>
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