const menuIcon = document.querySelector('.menu-icon');
const menu = document.querySelector('.menu');

const cliceTabela = document.querySelector('.clice-tabela');
const menuTabelaList = document.querySelector('.menu-tabela');

menuIcon.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.classList.toggle('active');
});
cliceTabela.addEventListener('click', (e) => {
    e.stopPropagation();
    menuTabelaList.classList.toggle('open');
});

document.addEventListener('click', (e) => {
    if (!menu.contains(e.target) && !menuIcon.contains(e.target)) {
        menu.classList.remove('active');
    }
    if (!menuTabelaList.contains(e.target)) {
        menuTabelaList.classList.remove('open');
    }
});
