<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once ('./src/connectors/db_connection.php');
require_once ('./src/settings/settings.php');
require_once ('./src/apis/fetch_api.php');

//$currentDate = date('Y-m-d');
//$currentDay = date('d');
//$currentMonth = date('m');
//$currentYear = date('Y');

function genCal($month, $year) {
	$firstDOM = mktime(0, 0, 0, $month, 1, $year);
	$amountOD = date('t', $firstDOM);
	$firstWD = date('N', $firstDOM);
	$cal = array_fill(1, $amountOD + $firstWD - 1, null);
	for ($i = 1; $i <= $amountOD; $i++) {
		$cal[$firstWD + $i - 1] = $i;
	}
	return $cal;
}

function getEve($dbconnection, $month, $year) {
	$startDate = "$year-$month-01";
	$endDate = date("Y-m-t", strtotime($startDate));

	$query = $dbconnection->prepare("SELECT * FROM event WHERE start_date BETWEEN :startDate AND :endDate");
	$query->execute(['startDate' => $startDate, 'endDate' => $endDate]);
	return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getPubHolAndSchHol() {
    $apiUrl = './src/apis/fetch_api.php';
    $response = file_get_contents($apiUrl);
    return json_decode($response, true);
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
	<meta charset="UTF-8">
	<title><?php echo $calendarTitle, ' ', $year1, ' | ', $year2; ?></title>
	<link rel="stylesheet" type="text/css" href="./src/css/style.css">
</head>

<body>
	<h1><?php echo $calendarTitle, ' ', $year1, ' | ', $year2; ?></h1>
	<div class="cal">
		<?php
		$holidays = getPubHolAndSchHol();
		foreach ($months as $index => $monthName): ?>
			<div class="month">
				<h2><?php echo $monthName, ' ', $year1; ?></h2>
				<div class="weekdays">
					<?php foreach ($weekdays as $weekday): ?>
						<div class="weekday"><?php echo $weekday; ?></div>
					<?php endforeach; ?>
				</div>
				<div class="days">
					<?php
					$days = genCal($index + 1, $year1);
					$events = getEve($dbconnection, $index + 1, $year1);
					$eventsByDay = [];
					foreach ($events as $event) {
						$day = (int) date('j', strtotime($event['start_date']));
						if (!isset($eventsByDay[$day])) {
							$eventsByDay[$day] = [];
						}
						$eventsByDay[$day][] = $event;
					}
					foreach ($holidays as $holiday) {
						$day = (int) date('j', strtotime($holiday['startDate']));
						if (!isset($eventsByDay[$day])) {
							$eventsByDay[$day] = [];
						}
						$eventsByDay[$day][] = $holiday;
					}
					foreach ($days as $day) {
						if ($day) {
							//$isPast = $dayDate < $currentDate ? 'past' : '';
                			//echo "<div class='day $isPast'>";
							echo "<div class='day'>";
							echo $day;
							if (isset($eventsByDay[$day])) {
								foreach ($eventsByDay[$day] as $event) {
									echo "<div class='eve'>{$event['title']}</div>";
								}
							}
							echo "</div>";
						} else {
							echo "<div class='day'></div>";
						}
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
		<?php foreach ($months as $index => $monthName): ?>
			<div class="month">
				<h2><?php echo $monthName, ' ', $year2; ?></h2>
				<div class="weekdays">
					<?php foreach ($weekdays as $weekday): ?>
						<div class="weekday"><?php echo $weekday; ?></div>
					<?php endforeach; ?>
				</div>
				<div class="days">
					<?php
					$days = genCal($index + 1, $year2);
					$events = getEve($dbconnection, $index + 1, $year2);
					$eventsByDay = [];
					foreach ($events as $event) {
						$day = (int) date('j', strtotime($event['start_date']));
						if (!isset($eventsByDay[$day])) {
							$eventsByDay[$day] = [];
						}
						$eventsByDay[$day][] = $event;
					}
					foreach ($holidays as $holiday) {
						$day = (int) date('j', strtotime($holiday['startDate']));
						if (!isset($eventsByDay[$day])) {
							$eventsByDay[$day] = [];
						}
						$eventsByDay[$day][] = $holiday;
					}
					foreach ($days as $day) {
						if ($day) {
							echo "<div class='day'>";
							echo $day;
							if (isset($eventsByDay[$day])) {
								foreach ($eventsByDay[$day] as $event) {
									echo "<div class='eve'>{$event['title']}</div>";
								}
							}
							echo "</div>";
						} else {
							echo "<div class='day'></div>";
						}
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</body>

</html>