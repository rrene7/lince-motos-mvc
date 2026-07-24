</main>
<footer class="footer">Prototipo institucional · <?= date('Y') ?></footer>
<script>
document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        if (!confirm(form.dataset.confirm)) event.preventDefault();
    });
});
</script>
</body>
</html>
