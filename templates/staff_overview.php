<?php
/**
 * Staff overview table.
 *
 * Shows active absences by default. Staff members may switch to all records
 * and choose between the sort orders required by the specification.
 */
declare(strict_types=1);

use AbsenceApp\Utils;

/** @var array<int,array<string,mixed>> $absences */
/** @var bool $activeOnly */
/** @var string $sort */
?>
<section class="card wide-card">
    <div class="page-title-row">
        <div>
            <h1>Übersicht Abwesenheiten</h1>
            <p class="muted">Aktueller Login: <?= Utils::h((string) $auth->currentUserId()) ?></p>
        </div>
        <a class="button-secondary compact" href="/personal.php">Aktualisieren</a>
    </div>

    <form class="toolbar" method="get" action="/personal.php">
        <label class="switch-row">
            <input class="checkbox" type="checkbox" name="view" value="all" <?= !$activeOnly ? 'checked' : '' ?>>
            <span>Alle Abwesenheiten anzeigen</span>
        </label>

        <label class="inline-select">
            Sortierung
            <select name="sort">
                <option value="departure" <?= $sort === 'departure' ? 'selected' : '' ?>>Aufbruch</option>
                <option value="patient" <?= $sort === 'patient' ? 'selected' : '' ?>>Patienten-ID</option>
            </select>
        </label>

        <button class="compact" type="submit">Anzeigen</button>
    </form>

    <?php if ($absences === []): ?>
        <p class="alert notice">Keine <?= $activeOnly ? 'aktiven ' : '' ?>Abwesenheiten gefunden.</p>
    <?php else: ?>
        <div class="table-scroll" role="region" aria-label="Abwesenheiten" tabindex="0">
            <table>
                <thead>
                    <tr>
                        <th>Patienten-ID</th>
                        <th>Grund / Ziel</th>
                        <th>Aufbruch</th>
                        <th>Rückkehr</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absences as $absence): ?>
                        <tr class="<?= $absence['return_time'] === null ? 'is-active' : '' ?>">
                            <td data-label="Patienten-ID"><?= Utils::h((string) $absence['patient_id']) ?></td>
                            <td data-label="Grund / Ziel"><?= Utils::h((string) $absence['reason_name']) ?></td>
                            <td data-label="Aufbruch"><?= Utils::h((string) $absence['departure_time']) ?></td>
                            <td data-label="Rückkehr">
                                <?= $absence['return_time'] === null ? '<span class="status-active">aktiv</span>' : Utils::h((string) $absence['return_time']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
