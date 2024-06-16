<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$languageCode = 'DE';
$countryCode = 'AT';
$subdivisionCode ='SM';

$calendarTitle = 'SchulJahresKalender';
$startMonth = 9;
$startYear = 2023;
$endMonth = 7;
$endYear = 2024;

if (date("Y") == $endYear) {
	$year1 = date("Y") - 1;
	$year2 = date("Y");
}
else {
	$year1 = date("Y");
	$year2 = date("Y") + 1;
}

$months = [
    1 => "Januar",
    2 => "Februar",
    3 => "März",
    4 => "April",
    5 => "Mai",
    6 => "Juni",
    7 => "Juli",
    8 => "August",
    9 => "September",
    10 => "Oktober",
    11 => "November",
    12 => "Dezember",
];

$weekdays = ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"];

?>