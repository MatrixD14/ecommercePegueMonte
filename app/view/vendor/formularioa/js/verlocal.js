let $ = document.querySelector.bind(document);
let cacheCEP = JSON.parse(localStorage.getItem('cacheCEP')) || {};
let pendingRequests = new Map();
function getElements() {
    return {
        form: $('.form-cep'),
        alertDiv: $('.alert-cep'),
        dddEl: $('#ddd'),
        ufEl: $('#uf'),
        bairroEl: $('#bairro'),
        localidadeEl: $('#localidade'),
        logradouroEl: $('#logradouro'),
        cepInput: $('#cep'),
    };
}
function log(els, err) {
    return (els.alertDiv.textContent = err);
}
function limparCampos(eles) {
    eles.dddEl.textContent = '';
    eles.ufEl.textContent = '';
    eles.bairroEl.textContent = '';
    eles.localidadeEl.textContent = '';
    eles.logradouroEl.textContent = '';
}
function mostrarErro(eles, show = true) {
    if (!eles.alertDiv) return;
    if (show) {
        eles.alertDiv.classList.add('active');
        limparCampos(eles);
    } else {
        eles.alertDiv.classList.remove('active');
    }
}
function preencher(eles, data) {
    if (data.bairro === '') {
        mostrarErro(eles, true);
        log(eles, 'não é um CEP, válido');
        return;
    }
    mostrarErro(eles, false);

    log(eles, 'Por favor, insira um cep valido.');
    eles.dddEl.textContent = data.ddd || '';
    eles.ufEl.textContent = data.uf || '';
    eles.bairroEl.textContent = data.bairro || '';
    eles.localidadeEl.textContent = data.localidade || '';
    eles.logradouroEl.textContent = data.logradouro || '';
}
async function verCEP(e) {
    e.preventDefault();
    const els = getElements();
    const cepNum = els.cepInput.value.replace(/\D/g, '');
    if (cepNum.length !== 8) {
        mostrarErro(els, true);
        log(els, 'não é um CEP, válido');
        return null;
    }
    if (cacheCEP[cepNum]) {
        preencher(els, cacheCEP[cepNum]);
        return;
    }
    if (pendingRequests.has(cepNum)) return;

    const request = fetch(`https://viacep.com.br/ws/${cepNum}/json/`)
        .then(async (res) => {
            if (!res.ok) throw new Error('HTTP error');
            const data = await res.json();
            if (data.erro) log(els, 'não é um CEP, válido');
            cacheCEP[cepNum] = data;
            localStorage.setItem('cacheCEP', JSON.stringify(cacheCEP));
            preencher(els, data);
        })
        .catch((err) => {
            mostrarErro(els, true);
        })
        .finally(() => {
            pendingRequests.delete(cepNum);
        });
}
function inicializarFormularioCEP() {
    const els = getElements();
    if (els.form) {
        els.form.removeEventListener('submit', verCEP);
        els.form.addEventListener('submit', verCEP);
    }
}
document.addEventListener('DOMContentLoaded', inicializarFormularioCEP);

document.addEventListener('contentUpdated', inicializarFormularioCEP);
