    <script>
        // ── Global JS Functions ──

        /**
         * Close alert messages
         */
        document.querySelectorAll('.alert-close').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var alert = this.closest('.alert');
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() { alert.style.display = 'none'; }, 250);
            });
        });

        /**
         * Confirm action with custom modal
         */
        function showConfirm(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
    </script>
</body>
</html>
