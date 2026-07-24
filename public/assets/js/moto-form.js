(function () {
    'use strict';

    var input = document.getElementById('foto');
    var preview = document.getElementById('photo-preview');
    var placeholder = document.getElementById('photo-placeholder');

    if (!input || !preview || !placeholder) return;

    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        if (!file) return;

        if (!file.type.match(/^image\/(jpeg|png|webp)$/)) {
            input.value = '';
            window.alert('Seleccione una fotografía JPG, PNG o WEBP.');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            input.value = '';
            window.alert('La fotografía debe pesar menos de 5 MB.');
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('is-hidden');
        placeholder.classList.add('is-hidden');
    });
}());
