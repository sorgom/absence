<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';



use AbsenceApp\AbsenceRepository;
use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\Session;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);
$error = null;

/*
 * The staff overview is a personal working view. Therefore its filter and
 * sorting state is stored in the session instead of the URL.
 */
$allowedView = ['active', 'all'];
$allowedSort = ['departure', 'patient'];
$allowedOrder = ['asc', 'desc'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$auth->isLoggedIn()) {
    $id = trim((string) ($_POST['id'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($auth->login(Auth::ROLE_STAFF, $id, $password)) {
        header('Location: /personal.php');
        exit;
    }

    $error = 'Login fehlgeschlagen.';
}

if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_STAFF && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }

    if ($error === null) {
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
    }
}

if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_STAFF && $auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

$title = 'Personalbereich';
require __DIR__ . '/header.php';

if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_STAFF) {
    $view = (string) Session::get('staff_overview_view', 'active');
    $sort = (string) Session::get('staff_overview_sort', 'departure');
    $order = (string) Session::get('staff_overview_order', 'asc');

    $view = in_array($view, $allowedView, true) ? $view : 'active';
    $sort = in_array($sort, $allowedSort, true) ? $sort : 'departure';
    $order = in_array($order, $allowedOrder, true) ? $order : 'asc';

    $activeOnly = $view !== 'all';

    $absenceRepository = new AbsenceRepository($db);
    $absences = $absenceRepository->listForStaff($activeOnly, $sort, $order);

    require __DIR__ . '/staff_overview.php';
} else {
    $headline = 'Personal-Login';
    $idLabel = 'Personal-ID';
    $action = '/personal.php';
    require __DIR__ . '/login.php';
}

require __DIR__ . '/footer.php';
