<div class="painel-wrapper">
    <div class="alert-cep" role="alert">
        Por favor, insira um cep valido.
    </div>
    <p>DDD: <span id="ddd"></span></p>
    <p>UF: <span id="uf"></span></p>
    <p>Locadora: <span id="locadora"></span></p>
    <form autocomplete="off" class="form-cep" onsubmit="verCEP()">
        <div class="input-cep">
            <input type="text" name="cep" id="cep" class="dados-cep">
        </div>
        <button type="submit"> Enviar</button>
    </form>
</div>