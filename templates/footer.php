<?php
declare(strict_types=1);

use AbsenceApp\AppInfo;
?>
</main>

<footer class="app-footer">
    <small>Abwesenheits-App · v<?= htmlspecialchars(AppInfo::version(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></small>
</footer>
</body>
</html>
