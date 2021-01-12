<?php
function editEvent($action, $datetime, $usernames, $info, $zoom, $eventid)
{
    $client = getClient();
    $service = new Google_Service_Calendar($client);
    $temp = uniqid();
    if ($action == 'claim') {
        $event = new Google_Service_Calendar_Event(array(
            'summary' => "Tutoring session with " . $usernames[0] . " and " . $usernames[1],
            'description' => "zoom:\n$zoom\n\nsession topic needs/help:\n$info",
            'start' => array(
                'dateTime' => date("Y-m-d\\TH:i:s", strtotime($datetime)),
                'timeZone' => 'UTC',
            ),
            'end' => array(
                // 2015-05-28T17:00:00
                'dateTime' => date("Y-m-d\\TH:i:s", strtotime($datetime . "+1 hours")),
                'timeZone' => 'UTC',
            ),
            'attendees' => array(
                array('email' => getEmail($usernames[0])),
                array('email' => getEmail($usernames[1])),
            ),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 30),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
            'id' => $temp
        ));
        $calendarId = 'primary';
        $event = $service->events->insert($calendarId, $event);
        require "../config.php";
        $sql = "UPDATE slots SET eventid = ? WHERE start = ? AND tutor = ?";
        if ($stmt = $link->prepare($sql)) {
            $stmt->execute([$temp, $datetime, $usernames[0]]);
        }
    } else {
        $service->events->delete('primary', $eventid);
    }
}

function getClient()
{
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    $client = new Google\Client();
    $client->setAuthConfig("credentials.json");
    $client->setRedirectUri($redirect_uri);
    $client->addScope("https://www.googleapis.com/auth/calendar");
    $client->setSubject("scheduler@tutoringforall.org");
    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

