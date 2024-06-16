<?php
require_once('../settings/settings.php');

$publicHolidaysAPI = 'https://openholidaysapi.org/PublicHolidays?countryIsoCode=' . $countryCode . '&languageIsoCode=' . $languageCode . '&validFrom=' . $startYear . '-' . $startMonth . '-01&validTo=' . $endYear . '-' . ($endMonth + 1) . '-01&subdivisionCode=' . $countryCode . '-' . $subdivisionCode;
$schoolHolidaysAPI = 'https://openholidaysapi.org/SchoolHolidays?countryIsoCode=' . $countryCode . '&languageIsoCode=' . $languageCode . '&validFrom=' . $startYear . '-' . $startMonth . '-01&validTo=' . $endYear . '-' . ($endMonth + 1) . '-01&subdivisionCode=' . $countryCode . '-' . $subdivisionCode;

function fetchHolidays($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        curl_close($ch);
        return ['error' => curl_error($ch)];
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

$publicHolidays = fetchHolidays($publicHolidaysAPI);
$schoolHolidays = fetchHolidays($schoolHolidaysAPI);

$events = [];

function processHolidays($holidays, &$events) {
    if (isset($holidays['error'])) {
        return; // Im Fehlerfall nichts hinzufügen
    }
    
    foreach ($holidays as $holiday) {
        if (isset($holiday['name']) && is_array($holiday['name'])) {
            foreach ($holiday['name'] as $name) {
                $events[] = [
                    'id' => $holiday['id'],
                    'startDate' => $holiday['startDate'],
                    'endDate' => $holiday['endDate'],
                    'type' => $holiday['type'],
                    'language' => $name['language'],
                    'title' => $name['text'],
                    'nationwide' => $holiday['nationwide'],
                ];
            }
        }
    }
}

processHolidays($publicHolidays, $events);
processHolidays($schoolHolidays, $events);

header('Content-Type: application/json');
echo json_encode($events, JSON_PRETTY_PRINT);
?>
