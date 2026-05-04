let $ = document.querySelector.bind(document);
function verCEP() {
    $('.form-cep').addEventListener('submit', (e) => {
        e.preventDefault();
        const cep_digitado = $('cep').value;
        if (cep_digitado.length == 8) {
            const requestoperations = {
                method: 'GET',
                redirect: 'follow',
            };
        }
        fetch(`https://viecep.com.br/ws/${cep_digitado}/json`, requestoperations)
            .then((response) => response.text())
            .then((result) => {
                const info = JSON.parse(result);
                if (info.erro) {
                    $('alert-cep').classList.add('active');
                } else {
                    $('alert-cep').classList.contains('active');
                    $('alert-cep').classList.remove('active');
                }
                const ddd = info.ddd;
                const uf = info.uf;
                const localidade = info.localidade;
                $('ddd').textContent = ddd;
                $('uf').textContent = uf;
                $('localidade').textContent = localidade;
            });
    });
}
