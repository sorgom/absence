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
/** @var string $view */
/** @var string $sort */
/** @var string $order */

function staffSortIndicator(string $column, string $currentSort, string $currentOrder): string
{
    if ($column !== $currentSort) {
        return '';
    }

    return $currentOrder === 'asc' ? ' ▲' : ' ▼';
}

function staffOverviewViewLabel(string $view): string
{
    return match ($view) {
        'ended' => 'Beendet',
        'all' => 'Alle',
        default => 'Aktiv',
    };
}

function staffOverviewEmptyLabel(string $view): string
{
    return match ($view) {
        'ended' => 'beendeten ',
        'all' => '',
        default => 'aktiven ',
    };
}
?>
<section class="card wide-card">
    <div class="page-title-row">
        <div>
            <h1>Übersicht Abwesenheiten</h1>
            <p class="muted">
                Auswahl:
                <?= Utils::h(staffOverviewViewLabel($view)) ?> ·
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

    <div class="overview-controls">
        <form class="overview-view-form" method="post" action="/personal.php">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="overview_view">

            <fieldset class="radio-group radio-group-compact overview-view-choice">
                <legend>Auswahl</legend>

                <label>
                    <input type="radio" name="view" value="active" <?= $view === 'active' ? 'checked' : '' ?> onchange="this.form.submit()">
                    Aktiv
                </label>

                <label>
                    <input type="radio" name="view" value="ended" <?= $view === 'ended' ? 'checked' : '' ?> onchange="this.form.submit()">
                    Beendet
                </label>

                <label>
                    <input type="radio" name="view" value="all" <?= $view === 'all' ? 'checked' : '' ?> onchange="this.form.submit()">
                    Alle
                </label>
            </fieldset>
        </form>

        <details class="overview-sort-popup">
            <summary>Sortieren</summary>
            <div class="overview-sort-popup__panel">
                <form method="post" action="/personal.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="overview_sort">
                    <input type="hidden" name="sort" value="patient">
                    <button class="button-secondary compact" type="submit">
                        ID<?= Utils::h(staffSortIndicator('patient', $sort, $order)) ?>
                    </button>
                </form>

                <form method="post" action="/personal.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="overview_sort">
                    <input type="hidden" name="sort" value="departure">
                    <button class="button-secondary compact" type="submit">
                        Aufbruch<?= Utils::h(staffSortIndicator('departure', $sort, $order)) ?>
                    </button>
                </form>
            </div>
        </details>
    </div>

    <?php if ($absences === []): ?>
        <p class="alert notice">Keine <?= staffOverviewEmptyLabel($view) ?>Abwesenheiten gefunden.</p>
    <?php else: ?>
        <div class="table-scroll" role="region" aria-label="Abwesenheiten" tabindex="0">
            <table class="absence-overview-table">
                <thead>
                    <tr>
                        <th class="status-column" aria-label="Status"></th>
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
                        <th class="action-column" aria-label="Aktion"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absences as $absence): ?>
                        <?php $isActive = $absence['return_time'] === null; ?>
                        <tr class="<?= $isActive ? 'is-active' : '' ?>">
                            <td class="status-cell" data-label="">
                                <span
                                    class="overview-status-dot <?= $isActive ? 'is-active' : 'is-inactive' ?>"
                                    aria-label="<?= $isActive ? 'Aktiv' : 'Beendet' ?>"
                                    title="<?= $isActive ? 'Aktiv' : 'Beendet' ?>"
                                ></span>
                            </td>
                            <td data-label="ID"><?= Utils::h((string) $absence['person_id']) ?></td>
                            <td data-label="Grund / Ziel"><?= Utils::h((string) $absence['reason_name']) ?></td>
                            <td data-label="Aufbruch"><?= Utils::localTimeElement((string) $absence['departure_time']) ?></td>
                            <td class="<?= $isActive ? 'empty-on-mobile' : '' ?>" data-label="Rückkehr">
                                <?= $isActive ? '' : Utils::localTimeElement((string) $absence['return_time']) ?>
                            </td>
                            <td class="action-cell" data-label="">
                                <form method="post" action="/personal.php" class="inline-form" data-confirm-message="Möchten Sie diese Abwesenheit wirklich löschen?" data-confirm-template="Möchten Sie die Abwesenheit von {value} wirklich löschen?" data-confirm-value="<?= Utils::h((string) $absence['person_id']) ?>">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="action" value="delete_absence">
                                    <input type="hidden" name="absence_id" value="<?= (int) $absence['id'] ?>">
                                    <button type="submit" class="icon-button danger-icon" aria-label="Abwesenheit löschen"></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
