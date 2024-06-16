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

$months = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];
$weekdays = ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"];

?>