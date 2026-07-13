<?php
/**
 * Staff overview table.
 *
 * The overview state is stored in the session by public/personal.php.
 * Sorting is changed through POST requests so the URL remains clean.
 */
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

/** @var array<int,array<string,mixed>> $absences */
/** @var bool $activeOnly */
/** @var string $sort */
/** @var string $order */

/**
 * Returns the visible arrow for the active sort column.
 */
function staffSortIndicator(string $column, string $currentSort, string $currentOrder): string
{
    if ($column !== $currentSort) {
        return '';
    }

    return $currentOrder === 'asc' ? ' ▲' : ' ▼';
}
?>
<section class="card wide-card">
    <div class="page-title-row">
        <div>
            <h1>Übersicht Abwesenheiten</h1>
            <p class="muted">
                <?= $activeOnly ? 'Nur aktive Abwesenheiten' : 'Alle Abwesenheiten' ?> ·
                Sortierung:
                <?= Utils::h($sort === 'patient' ? 'ID' : 'Aufbruch') ?>
                <?= Utils::h($order === 'asc' ? 'aufsteigend' : 'absteigend') ?>
            </p>
        </div>

        <form method="post" action="/personal.php">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="overview_refresh">
            <button class="button-secondary compact" type="submit">Aktualisieren</button>
        </form>
    </div>

    <form class="toolbar" method="post" action="/personal.php">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="overview_view">
        <input type="hidden" name="view" value="<?= $activeOnly ? 'all' : 'active' ?>">

        <label class="switch-row">
            <input
                class="checkbox"
                type="checkbox"
                onchange="this.form.submit()"
                <?= $activeOnly ? 'checked' : '' ?>
            >
            <span>Nur aktive Abwesenheiten</span>
        </label>
    </form>

    <?php if ($absences === []): ?>
        <p class="alert notice">Keine <?= $activeOnly ? 'aktiven ' : '' ?>Abwesenheiten gefunden.</p>
    <?php else: ?>
        <div class="table-scroll" role="region" aria-label="Abwesenheiten" tabindex="0">
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>
                            <form class="sort-form" method="post" action="/personal.php">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="action" value="overview_sort">
                                <input type="hidden" name="sort" value="patient">
                                <button class="table-sort-button" type="submit">
                                    ID<?= Utils::h(staffSortIndicator('patient', $sort, $order)) ?>
                                </button>
                            </form>
                        </th>
                        <th>Grund / Ziel</th>
                        <th>
                            <form class="sort-form" method="post" action="/personal.php">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="action" value="overview_sort">
                                <input type="hidden" name="sort" value="departure">
                                <button class="table-sort-button" type="submit">
                                    Aufbruch<?= Utils::h(staffSortIndicator('departure', $sort, $order)) ?>
                                </button>
                            </form>
                        </th>
                        <th>Rückkehr</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absences as $absence): ?>
                        <tr class="<?= $absence['return_time'] === null ? 'is-active' : '' ?>">
                            <td data-label="Status">
                                <?= $absence['return_time'] === null ? '<span class="status-active">Unterwegs</span>' : 'Zurück' ?>
                            </td>
                            <td data-label="ID"><?= Utils::h((string) $absence['person_id']) ?></td>
                            <td data-label="Grund / Ziel"><?= Utils::h((string) $absence['reason_name']) ?></td>
                            <td data-label="Aufbruch"><?= Utils::localTimeElement((string) $absence['departure_time']) ?></td>
                            <td data-label="Rückkehr">
                                <?= $absence['return_time'] === null ? '' : Utils::localTimeElement((string) $absence['return_time']) ?>
                            </td>
                            <td data-label="Aktion">
                                <form method="post" action="/personal.php" class="inline-form" data-confirm-message="Möchten Sie diese Abwesenheit wirklich löschen?" data-confirm-template="Möchten Sie die Abwesenheit von {value} wirklich löschen?" data-confirm-value="<?= Utils::h((string) $absence['person_id']) ?>">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="action" value="delete_absence">
                                    <input type="hidden" name="absence_id" value="<?= (int) $absence['id'] ?>">
                                    <button type="submit" class="danger small">Löschen</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
