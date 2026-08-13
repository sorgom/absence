<?php
declare(strict_types=1);

use AbsenceApp\Utils;

/** @var array<int,array<string,mixed>> $entries */
?>
<section class="card wide-card">
    <div class="page-title-row">
        <h1>Protokoll</h1>
    </div>

    <?php if ($entries === []): ?>
        <p class="alert notice">Keine Protokoll-Einträge vorhanden.</p>
    <?php else: ?>
        <div class="table-scroll" role="region" aria-label="Protokoll" tabindex="0">
            <table class="protocol-table">
                <thead>
                    <tr>
                        <th>Datum / Uhrzeit</th>
                        <th>User ID</th>
                        <th>Aktion</th>
                        <th>Element</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td data-label="Datum / Uhrzeit"><?= Utils::localTimeElement((string) $entry['created_at']) ?></td>
                            <td data-label="User ID"><?= Utils::h((string) $entry['staff_user_id']) ?></td>
                            <td data-label="Aktion"><?= Utils::h((string) $entry['action']) ?></td>
                            <td data-label="Element"><?= Utils::h((string) ($entry['affected_element'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
