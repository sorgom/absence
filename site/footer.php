<?php
declare(strict_types=1);

use AbsenceApp\AppInfo;
?>
</main>

<footer class="app-footer">
    <small>Abwesenheits-App · v<?= htmlspecialchars(AppInfo::version(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></small>
</footer>
<div class="confirm-modal" id="confirm-modal" hidden>
    <div class="confirm-modal__backdrop" data-confirm-cancel></div>
    <div class="confirm-modal__panel">
        <div class="confirm-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title" aria-describedby="confirm-modal-message">
            <h2 id="confirm-modal-title">Bitte bestätigen</h2>
            <p id="confirm-modal-message">Möchten Sie fortfahren?</p>
            <div class="confirm-modal__actions">
                <button type="button" class="button-secondary" data-confirm-cancel>Abbruch</button>
                <button type="button" class="danger" data-confirm-ok>Bestätigen</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
