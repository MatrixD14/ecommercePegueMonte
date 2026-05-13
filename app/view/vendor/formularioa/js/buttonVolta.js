function buttonVoltar() {
    if (window.history.length > 1) window.history.back();
    else location.reload();
}
function PainelVoltar() {
    const painel = document.querySelector('.Painel');
    if (painel) {
        painel.parentElement.innerHTML = '';
    }
}
