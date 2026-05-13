document.addEventListener('click', function (e) {
    const fakeField = e.target.closest('.fake-file-field');
    if (fakeField) {
        const targetId = fakeField.getAttribute('data-target');
        if (targetId) {
            const fileInput = document.getElementById(targetId);
            if (fileInput) {
                fileInput.click();
            }
        }
        e.preventDefault();
    }
});

document.addEventListener('change', function (e) {
    if (e.target && e.target.classList && e.target.classList.contains('real-file-input')) {
        const fileInput = e.target;
        const fakeId = 'fake-' + fileInput.id;
        const fakeField = document.getElementById(fakeId);
        if (fakeField) {
            const files = fileInput.files;
            if (files.length) {
                fakeField.value = files[0].name;
            } else {
                fakeField.value = 'Seleciona um arquivo';
            }
        }
    }
});
