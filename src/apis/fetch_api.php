<?php
require_once ('./src/settings/settings.php');

$publicHolidaysAPI = 'https://openholidaysapi.org/PublicHolidays?countryIsoCode='. $countryCode. '&languageIsoCode='. $languageCode. '&validFrom='. $startYear. '-'. $startMonth. '-01&validTo='. $endYear. '-'. ($endMonth + 1). '-01&subdivisionCode='. $countryCode. '-'. $subdivisionCode;
$schoolHolidaysAPI = 'https://openholidaysapi.org/SchoolHolidays?countryIsoCode='. $countryCode. '&languageIsoCode='. $languageCode. '&validFrom='. $startYear. '-'. $startMonth. '-01&validTo='. $endYear. '-'. ($endMonth + 1). '-01&subdivisionCode='. $countryCode. '-'. $subdivisionCode;

//echo $publicHolidaysAPI;
//echo $schoolHolidaysAPI;

$cph = curl_init();
$csh = curl_init();

curl_setopt($cph, CURLOPT_URL, $publicHolidaysAPI);
curl_setopt($cph, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($cph, CURLOPT_HTTPHEADER, 1);
curl_setopt($csh, CURLOPT_URL, $schoolHolidaysAPI);
curl_setopt($csh, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($csh, CURLOPT_HTTPHEADER, 1);

$response = curl_exec($cph);
$response = curl_exec($csh);

curl_close($cph);
curl_close($csh);

$publicholidays = json_decode($response, true);
$schoolholidays = json_decode($response, true);

$events = [];

foreach ($publicholidays as $publicholiday) {
    if (isset($publicholiday['name']) && is_array($publicholiday['name'])) {
        foreach ($publicholiday['name'] as $name) {
            $events[] = [
                'id' => $publicholiday['id'],
                'startDate' => $publicholiday['startDate'],
                'endDate' => $publicholiday['endDate'],
                'type' => $publicholiday['type'],
                'language' => $name['language'],
                'title' => $name['text'],
                'nationwide' => $publicholiday['nationwide'],
            ];
        }
    }
}

foreach ($schoolholidays as $schoolholiday) {
    if (isset($schoolholiday['name']) && is_array($schoolholiday['name'])) {
        foreach ($schoolholiday['name'] as $name) {
            $events[] = [
                'id' => $schoolholiday['id'],
                'startDate' => $schoolholiday['startDate'],
                'endDate' => $schoolholiday['endDate'],
                'type' => $schoolholiday['type'],
                'language' => $name['language'],
                'title' => $name['text'],
                'nationwide' => $schoolholiday['nationwide'],
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($events, JSON_PRETTY_PRINT);
?>
