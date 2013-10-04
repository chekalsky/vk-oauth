<?php
    include('../che/VK.php');
    include('../che/VKException.php');

    $vk = new \che\VK('{APP_ID}', '{SECRET}');

    $currentUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

    try {
        if (!isset($_GET['code'])) {
            $url = $vk->getAuthenticationUrl($currentUrl, 'friends,wall');
            echo '<a href="'.$url.'">Enter using VK account</a>';
        } else {
            $token = $vk->getAccessToken($_GET['code'], $currentUrl);
            $timeToLive = $token['expires_in'];
            $userId = $token['user_id'];

            echo 'User ID is ' . $userId . '. Token is: ' . $token['access_token'];
            echo ' and it is valid until ' . date('Y-m-d H:i:s', time() + $token['expires_in']);
            echo '<br>Now you can <a href="wall_get.php?token='.$token['access_token'].'">move to the next example</a>.';
        }
    } catch (\che\VKException $e) {
        echo 'Error: ' . $e->getMessage();
    }