const menuIcon = document.querySelector('.menu-icon');
const menu = document.querySelector('.menu');

const cliceTabela = document.querySelector('.clice-tabela');
const menuTabelaList = document.querySelector('.menu-tabela');

menuIcon.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.classList.toggle('active');
    document.body.classList.toggle('menu-open');
});
cliceTabela.addEventListener('click', (e) => {
    e.stopPropagation();
    menuTabelaList.classList.toggle('open');
});

document.addEventListener('click', (e) => {
    if (!menuIcon.contains(e.target)) {
        menu.classList.remove('active');
        document.body.classList.remove('menu-open');
    }

    if (!menuTabelaList.contains(e.target)) menuTabelaList.classList.remove('open');
});

window.addEventListener('scroll', () => {
    const fim = window.innerHeight + window.scrollY >= document.body.offsetHeight;

    if (fim) {
        console.log('chegou no final');
    }
});
