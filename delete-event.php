<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$eventId = $_GET['id'] ?? null;

if (!$eventId) {
    header("Location: events.php");
    exit();
}

require_once __DIR__ . '/data-access/CalendarDataAccess.php';
$dbFile = __DIR__ . '/calendar.db';
$dao = new CalendarDataAccess($dbFile);

$event = $dao->getEventById($eventId);

if ($event && (int)$event->getUserId() === (int)$_SESSION['user_id']) {
    $dao->deleteEvent($eventId);
}

header("Location: events.php");
exit();