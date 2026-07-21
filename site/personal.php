<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use AbsenceApp\AbsenceCleanup;
use AbsenceApp\AbsenceRepository;
use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\Session;

Session::start();

$db = Database::getConnection();
AbsenceCleanup::run($db);
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$auth->requireRole(Auth::ROLE_STAFF);

if ($auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

$error = null;

$allowedView = ['active', 'ended', 'all'];
$allowedSort = ['departure', 'patient'];
$allowedOrder = ['asc', 'desc'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'overview_view') {
            $view = (string) ($_POST['view'] ?? 'active');

            if (in_array($view, $allowedView, true)) {
                Session::set('staff_overview_view', $view);
            }

            header('Location: /personal.php');
            exit;
        }

        if ($action === 'overview_sort') {
            $requestedSort = (string) ($_POST['sort'] ?? 'departure');

            if (in_array($requestedSort, $allowedSort, true)) {
                $currentSort = (string) Session::get('staff_overview_sort', 'departure');
                $currentOrder = (string) Session::get('staff_overview_order', 'asc');

                if ($requestedSort === $currentSort) {
                    Session::set('staff_overview_order', $currentOrder === 'asc' ? 'desc' : 'asc');
                } else {
                    Session::set('staff_overview_sort', $requestedSort);
                    Session::set('staff_overview_order', 'asc');
                }
            }

            header('Location: /personal.php');
            exit;
        }

        if ($action === 'overview_refresh') {
            header('Location: /personal.php');
            exit;
        }

        if ($action === 'delete_absence') {
            $repository = new AbsenceRepository($db);
            $repository->deleteById((int) ($_POST['absence_id'] ?? 0));

            header('Location: /personal.php');
            exit;
        }
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Personalbereich';
require __DIR__ . '/header.php';

$view = (string) Session::get('staff_overview_view', 'active');
$sort = (string) Session::get('staff_overview_sort', 'departure');
$order = (string) Session::get('staff_overview_order', 'asc');

$view = in_array($view, $allowedView, true) ? $view : 'active';
$sort = in_array($sort, $allowedSort, true) ? $sort : 'departure';
$order = in_array($order, $allowedOrder, true) ? $order : 'asc';

$absenceRepository = new AbsenceRepository($db);
$absences = $absenceRepository->listForStaffByView($view, $sort, $order);

require __DIR__ . '/staff_overview.php';
require __DIR__ . '/footer.php';
