<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav class="topbar">
    <a class="NameSite" href="/PengueMonte">Pegue e Monte</a>
    <div id="box-pesquisa">
        <input type="text" name="pesquisa" id="pesquisa" placeholder="inicia e 2 caracter">
        <button type="submit">
            <svg class="icon">
                <use href="#icon-lupa"></use>
            </svg>
        </button>
    </div>
    <p class="menu-oculto"><svg class="icon menu-icon">
            <use href="#icon-menu" xlink:href="#icon-menu"></use>
        </svg></p>
    <div class=" menu">
        <div class="carinho">
            <p>
                <a class="effect-button-link button-menu ajax-link <?= $uri === '/carinho' ? 'active' : '' ?>" href="/carinho">
                    <svg class="icon">
                        <use href="#icon-carinho-mercado"></use>
                    </svg> <?= !isset($_SESSION["espera"]) ? "vazio" : $_SESSION["espera"] ?>
                </a>

            </p>
        </div>
        <div class="menu-tabela">
            <p class="effect-button-link button-menu clice-tabela">
                <svg class="icon">
                    <use href="#icon-list"></use>
                </svg> Tabelas <svg class="icon icon-seta-baixa">
                    <use href="#icon-iconMutidirecao"></use>
                </svg>
            </p>
            <ul class=" menu-list menu-tabela-list">
                <li><a class="effect-button-link ajax-link " href="/agendamentos">agendamentos</a></li>
                <li><a class="effect-button-link ajax-link " href="/salas">salas</a></li>
                <li><a class="effect-button-link ajax-link " href="/cursos">cursos</a></li>
                <li><a class="effect-button-link ajax-link " href="/usuarios">usuários</a></li>
                <li><a class="effect-button-link ajax-link " href="/turmas">turmas</a></li>

            </ul>
        </div>
        <div class="menu-Sair">
            <p><a class="effect-button-link button-menu " href="/">
                    <svg class="icon">
                        <use href="#icon-seta-right"></use>
                    </svg> Sair
                </a></p>
        </div>
    </div>
</nav>