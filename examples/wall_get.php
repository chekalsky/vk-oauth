<?php
    include('../che/VK.php');
    include('../che/VKException.php');

    $vk = new \che\VK('{APP_ID}', '{SECRET}');

    try {
        $vk->setAccessToken($_GET['token']);

        // Using POST request by default
        $wall = $vk->wall_get(array(
            'count' => 10,
            'filter' => 'owner'
        ));

        echo "<pre>" . print_r($wall, 1) . "</pre>";

        // If you want to get all urls in https-compatible form
        $vk->setForceHttps(true);

        // If you want to use GET
        $users = $vk->get('users.get', array(
            'user_ids' => "1,3990053",
            'fields' => 'photo_50,city'
        ));

        echo "<pre>" . print_r($users, 1) . "</pre>";
    } catch (\che\VKException $e) {
        echo 'Error: ' . $e->getMessage();
    }