<?php

add_action('scraping_rates_hook', 'insertExchangeRates');


function insertExchangeRates()
{
    global $wpdb;

    $results = getExchangeRates();

    foreach ($results as $result) {
        $wpdb->insert('wp_exchange_rates', ['currency' => $result[0], 'first_rate' => $result[1], 'second_rate' =>
            $result[2], 'difference' => $result[3]]);
    }

}


function getExchangeRates()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.nbkr.kg');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);

    preg_match("/<div.*id=\"sticker-exrates\".*>(.*|\n)*\s<\/div>/", $content, $match);
    $fields = explode('<td ', $match[0]);
    unset($fields[0]);

    $results = [];
    $i       = 0;

    foreach ($fields as $field) {
        if (preg_match('/>(.*)</', $field, $finalMatch)) {
            $results[$i][] = $finalMatch[1];
            if (count($results[$i]) % 4 === 0) {
                $i++;
            }
        } else {
            continue;
        }
    }

    return $results;
}
