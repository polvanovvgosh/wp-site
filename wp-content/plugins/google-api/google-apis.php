<?php
/**
 * Plugin Name: Google api's plugin
 * Description: Allow to use google api's to write backend in wordpress
 * Version: 1.0
 */

require __DIR__.'/vendor/autoload.php';

add_action('upload_report_data', 'uploadOrders');

function set_cron_schedule()
{
    if (!wp_next_scheduled('upload_report_data')) {
        wp_schedule_event(time(), 'daily', 'upload_report_data');
    }
}

/**
 * Returns an authorized API client.
 *
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('wordpress-site');
    $client->setScopes(Google_Service_Drive::DRIVE);
    $client->setAuthConfig(
        __DIR__.'/client_secret_922551308385-m8bfrgcnqk16vtvjun85ue0db14v5oc2.apps.googleusercontent.com.json'
    );
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $tokenPath = __DIR__.'/token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }

    return $client;
}

$ordersDir = wp_get_upload_dir()['basedir'].'/orders/';

$client  = getClient();
$service = new Google_Service_Drive($client);

function uploadOrders($ordersDir, $service)
{
    $orders = glob($ordersDir.'*');
    foreach ($orders as $order) {
        $fileMetadata = new Google_Service_Drive_DriveFile(['name' => basename($order)]);
        $content      = file_get_contents($order);
        $service->files->create(
            $fileMetadata,
            [
                'data'       => $content,
                'mimeType'   => 'application/vnd.ms-excel',
                'uploadType' => 'multipart',
                'fields'     => 'id',
            ]
        );
    }

}

function clearOrders($ordersDir)
{
    if (file_exists($ordersDir)) {
        foreach (glob($ordersDir.'*') as $file) {
            unlink($file);
        }
    }
}
