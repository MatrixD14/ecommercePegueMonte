async function carregarProdutos(container) {
    try {
        const resposta = await fetch('/listprodutos');
        if (!resposta.ok) throw new Error('Erro ao carregar produtos');
        const produtos = await resposta.json();

        if (produtos.length === 0) {
            container.innerHTML = '<p style="text-align:center">Nenhum produto encontrado.</p>';
            return;
        }

        // Gera os cards
        // data-produto='$data-produto='${encodeURIComponent(JSON.stringify(produto))}'}'
        const cardsHTML = produtos
            .map(
                (produto) => `
                <div class="card" data-produto='${encodeURIComponent(JSON.stringify(produto))}'>
                    <img class="card-imagem" src="${produto.imagem}" alt="${produto.nome}" loading="lazy">
                    <div class="card-conteudo">
                        <h3 class="card-titulo">${produto.nome}</h3>
                        <div class="card-preco">${produto.precoFormatado}</div>
                    </div>
                </div>
            `,
            )
            .join('');
        container.innerHTML = cardsHTML;

        // Adiciona evento aos botões (opcional)
        document.querySelectorAll('.card').forEach((card) => {
            card.addEventListener('click', () => {
                const produtoData = card.dataset.produto;
                if (produtoData) {
                    const produto = JSON.parse(decodeURIComponent(produtoData));
                    window.location.href = `/produto/${encodeURIComponent(produto.arquivo)}`;
                }
            });
        });
    } catch (erro) {
        console.error(erro);
        container.innerHTML = '<p style="color:red">Erro ao carregar produtos. Tente novamente.</p>';
    }
}

function initgerationCart() {
    const container = document.getElementById('produtos-container');
    if (container) {
        container.innerHTML = '<div class="loading">Carregando produtos...</div>';
        carregarProdutos(container);
    }
}
document.addEventListener('DOMContentLoaded', initgerationCart);
document.addEventListener('contentUpdated', initgerationCart);
