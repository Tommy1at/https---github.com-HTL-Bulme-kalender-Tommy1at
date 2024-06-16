<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('./src/connectors/db_connection.php');
require_once('./src/settings/settings.php');

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
    $data = json_decode($response, true);

    if (!is_array($data)) {
        return [];
    }

    return $data;
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title><?php echo $calendarTitle, ' ', $startYear, ' - ', $endYear; ?></title>
    <link rel="stylesheet" type="text/css" href="./src/css/style.css">
</head>

<body>
    <h1><?php echo $calendarTitle, ' ', $startYear, ' - ', $endYear; ?></h1>
    <div class="cal">
        <?php
        $holidays = getPubHolAndSchHol();
        $currentMonth = $startMonth;
        $currentYear = $startYear;
        $today = new DateTime();

        while ($currentYear < $endYear || ($currentYear == $endYear && $currentMonth <= $endMonth)) {
            $monthYear = new DateTime("$currentYear-$currentMonth-01");
            $isPastMonth = $monthYear < new DateTime('- 1 month') ? 'past' : '';

            echo "<div class='month $isPastMonth'>";
            echo "<h2>{$months[$currentMonth]} $currentYear</h2>";
            echo "<div class='weekdays'>";
            foreach ($weekdays as $weekday) {
                echo "<div class='weekday'>$weekday</div>";
            }
            echo "</div>";
            echo "<div class='days'>";

            $days = genCal($currentMonth, $currentYear);
            $events = getEve($dbconnection, $currentMonth, $currentYear);
            $eventsByDay = [];
            foreach ($events as $event) {
                $day = (int) date('j', strtotime($event['start_date']));
                if (!isset($eventsByDay[$day])) {
                    $eventsByDay[$day] = [];
                }
                $eventsByDay[$day][] = $event;
            }
            if (is_array($holidays)) {
                foreach ($holidays as $holiday) {
                    $day = (int) date('j', strtotime($holiday['startDate']));
                    if (!isset($eventsByDay[$day])) {
                        $eventsByDay[$day] = [];
                    }
                    $eventsByDay[$day][] = $event;
                }
            }
            foreach ($days as $day) {
                if ($day) {
                    $dayDate = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                    $isPast = $dayDate < $today->format('Y-m-d') ? 'past' : '';
                    echo "<div class='day $isPast'>$day";
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

            echo "</div>";
            echo "</div>";

            if ($currentMonth == 12) {
                $currentMonth = 1;
                $currentYear++;
            } else {
                $currentMonth++;
            }
        }
        ?>
    </div>
</body>

</html>
