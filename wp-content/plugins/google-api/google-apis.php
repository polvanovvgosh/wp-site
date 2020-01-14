<?php
/**
 * Plugin Name: Google api's plugin
 * Description: Allow to use google api's to write backend in wordpress
 * Version: 1.0
 */
require_once  __DIR__. '/admin/admin.php';
require __DIR__.'/vendor/autoload.php';

add_action( 'plugins_loaded', 'googleapi_load_plugin_textdomain' );

function googleapi_load_plugin_textdomain() {
    load_plugin_textdomain( 'googleapi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('upload_report_data', 'uploadOrders', 10, 2);
register_activation_hook(__FILE__, 'set_cron_schedule_google_api');

function set_cron_schedule_google_api()
{
    if (!wp_next_scheduled('upload_report_data')) {
        wp_schedule_event(time(), 'twicedaily', 'upload_report_data');
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

function uploadOrders() {

    $allOptions = get_option('google_api_options');
    $ordersDir = $allOptions['folder'];
    $ordersDir = wp_get_upload_dir()['basedir'] . $ordersDir ;

    $client  = getClient();
    $service = new Google_Service_Drive($client);
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
    clearOrders($ordersDir);
}

function clearOrders($ordersDir) {
    if (file_exists($ordersDir)) {
        foreach (glob($ordersDir.'*') as $file) {
            unlink($file);
        }
    }
}
