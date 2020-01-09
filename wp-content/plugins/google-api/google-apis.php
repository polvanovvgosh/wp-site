<?php
/**
 * Plugin Name: Google api's plugin
 * Description: Allow to use google api's to write backend in wordpress
 * Version: 1.0
 */

require __DIR__.'/vendor/autoload.php';

/**
 * Returns an authorized API client.
 *
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('google-web-client');
    $client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
    $client->setAuthConfig(__DIR__. '/client_secret_922551308385-m8bfrgcnqk16vtvjun85ue0db14v5oc2.apps.googleusercontent.com.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $tokenPath = __DIR__ . '/token.json';
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


$client = getClient();
$service = new Google_Service_Drive($client);

$optParams = array(
    'pageSize' => 5,
    'fields' => 'nextPageToken, files(id, name)'
);
$results = $service->files->listFiles($optParams);

if (count($results->getFiles()) == 0) {
    print "No files found.\n";
} else {
    echo '<ul>';
    print "Files:";
    foreach ($results->getFiles() as $file) {
    printf("<li> %s (%s) </li>", $file->getName(), $file->getId());
    }
    echo '</ul>';
}

function clear() {
    if (file_exists(wp_get_upload_dir()['basedir']. '/orders/')) {
        foreach (glob('/orders/*') as $file) {
            unlink($file);
        }
    }
}
