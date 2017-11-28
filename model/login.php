<?php
define('APPLICATION_NAME', 'Google Sheets API PHP Quickstart');
define('CREDENTIALS_PATH', './.credentials/sheets.googleapis.com-php-quickstart.json');
define('CLIENT_SECRET_PATH', './../client_secret.json');
define('SCOPES', implode(' ', array(Google_Service_Sheets::SPREADSHEETS_READONLY)));

function getClient() {
    global $accessToken;
    $client = new Google_Client();
    $client->setApplicationName(APPLICATION_NAME);
    $client->setScopes(SCOPES);
    $client->setAuthConfig(CLIENT_SECRET_PATH);
    $client->setAccessType('offline');
    if(isset($_SESSION['token'])) {
        $accessToken = $_SESSION['token'];
    } else {
        $authUrl = $client->createAuthUrl();
        printf('<div class="login"><a href="%s">Log In with <img src="/assets/images/google.png" alt="G">oogle</a><div>', $authUrl);
        if(isset($_GET['code'])) {
            $authCode = $_GET['code'];
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $_SESSION['token'] = $accessToken;
            header("Location: http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']);
        }
    }
    
    $client->setAccessToken($accessToken);
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getAccessToken());
        $_SESSION['token'] = $client->getAccessToken();
    }
    return $client;
}

function dropClient($status) {
    if ($status) {
        unset($_SESSION['token']);
        echo "<meta http-equiv=\"refresh\" content=\"0;URL=/\">";
    }
}

function getMonday($next=0, $readable=false) {
    $format = ($readable) ? 'j/n/Y' : 'n/j/Y';
    return date($format, strtotime("next monday")+$next*604800); // 604800 = 1 week in seconds 
}

function getName($fullname) {
    preg_match('/.*\s(.*)/', $fullname, $name);
    return $name[1];
}

function defineWeek() {
    return $week = isset($_GET['w']) ? $_GET['w'] : 0;
}

function getTalks($sheet, $week) {
    $date = getMonday($week);
    $n = array_search($date, $sheet[0]);
    $pncolumn = array_search('PhoneNumbers', $sheet[0]);
    foreach ($sheet as $row) {
        $name = !empty($row[1]) ? $row[1] : null;
        $assignment = !empty($row[$n]) ? $row[$n] : null;
        $phoneNumber = !empty($row[$pncolumn]) ? $row[$pncolumn] : null;
        if(isset($name) && isset($assignment)) {
            switch ($assignment) {
                case "Ч":
                    $reader["name"] = $name;
                    $reader["number"] = $phoneNumber;
                    break;
                case "1":
                    $first_call["first"] = $name;
                    $first_call["first_number"] = $phoneNumber;
                    break;
                case "1А":
                    $first_call["assistant"] = $name;
                    $first_call["assistant_number"] = $phoneNumber;
                    break;
                case "2":
                    $second_call["first"] = $name;
                    $second_call["first_number"] = $phoneNumber;
                    break;
                case "2А":
                    $second_call["assistant"] = $name;
                    $second_call["assistant_number"] = $phoneNumber;
                    break;
                case "3":
                    $third_talk["first"] = $name;
                    $third_talk["first_number"] = $phoneNumber;
                    break;
                case "3А":
                    $third_talk["assistant"] = $name;
                    $third_talk["assistant_number"] = $phoneNumber;
                    break;
            }
        }
    }
    return $talks = [
        0 => [
            'first' => $reader["name"],
            'first_number' => $reader["number"]
        ],
        1 => [
            'first' => $first_call["first"],
            'first_number' => $first_call["first_number"],
            'assistant' => $first_call["assistant"],
            'assistant_number' => $first_call["assistant_number"]
        ],
        2 => [
            'first' => $second_call["first"],
            'first_number' => $second_call["first_number"],
            'assistant' => $second_call["assistant"],
            'assistant_number' => $second_call["assistant_number"]
        ],
        3 => [
            'first' => $third_talk["first"],
            'first_number' => $third_talk["first_number"],
            'assistant' => $third_talk["assistant"],
            'assistant_number' => $third_talk["assistant_number"]
        ]
    ];
}

function slideWeeks($week) {
    $dateR = getMonday($week, 1);
    printf('<h1><a href="/?w=%s">&larr;</a> Неделя от %s <a href="/?w=%s">&rarr;</a></h1>', $week-1, $dateR, $week+1);
}

function printOut($talks, $week) {
    foreach ($talks as $number => $data) {
        $kinds = ['Чтение', 'Первое задание', 'Второе задание', 'Третье задание'];
        $kinds_en = ['reader', 'first_call', 'second_call', 'third_talk'];
        $and_assistant = !empty($data['assistant']) ? ' и ' . $data['assistant'] : null;
        $with_assistant = !empty($data['assistant']) ? ' с ' . $data['assistant'] : null;
        $line = $kinds[$number] . ': <a href="viber://chat?number=&#37;2B'.$data['first_number'].'">'.$data['first'].'</a>'.$and_assistant.'.<br />';
        $header[] = $line;
        $message = '<textarea id="'.$kinds_en[$number].'_message" class="form">Здравствуй, '.getName($data['first']).'!&#13;&#10;Самое время сообщить тебе, что у тебя задание на хинди-школе &mdash; '.mb_strtolower($kinds[$number]).' на неделе от '.getMonday($week, 1).$with_assistant.'.</textarea><br />
                    <button class="btn_'.$kinds_en[$number].' form" data-clipboard-target="#'.$kinds_en[$number].'_message">Copy</button><br />';
        $messages[] = $message;
        echo $line.$message;
    }
    // printf('<textarea id="reader_message" class="form">Здравствуй, %s!&#13;&#10;Самое время сообщить тебе, что у тебя задание на хинди-школе &mdash; чтение на неделе от %s.</textarea><br /><button class="btn_r form" data-clipboard-target="#reader_message">Copy</button><br />', getName($reader['name']), $dateR);

    // printf('<textarea id="first_call_message" class="form">Здравствуй, %s!&#13;&#10;Самое время сообщить тебе, что у тебя задание с %s на хинди-школе &mdash; первое посещение, на неделе от %s.</textarea><br /><button class="btn_fc form" data-clipboard-target="#first_call_message">Copy</button><br />', getName($first_call['first']), $first_call['assistant'], $dateR);

    // printf('<textarea id="second_call_message" class="form">Здравствуй, %s!&#13;&#10;Самое время сообщить тебе, что у тебя задание с %s на хинди-школе &mdash; повторное посещение, на неделе от %s.</textarea><br /><button class="btn_sc form" data-clipboard-target="#second_call_message">Copy</button><br />', getName($second_call['first']), $second_call['assistant'], $dateR);

    // if(isset($third_talk['assistant'])) {$sentence = "Третье задание: "; $print = " c ".$third_talk['assistant'];$kind = "изучение Библии";$and_name = " и ".$third_talk['assistant'];} else {$sentence = "Речь: ";$kind = "речь";}
    //     printf('%s <a href="viber://chat?number=&#37;2B%s">%s</a>%s.<br />', $sentence, $third_talk['first_number'], $third_talk['first'], $and_name);
    //     printf('<textarea id="third_talk_message" class="form">Здравствуй, %s!&#13;&#10;Самое время сообщить тебе, что у тебя задание%s на хинди-школе &mdash; %s, на неделе от %s.</textarea><br /><button class="btn_bs form" data-clipboard-target="#third_talk_message">Copy</button><br />', getName($third_talk['first']), $print, $kind, $dateR);
}
