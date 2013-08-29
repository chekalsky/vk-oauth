<?php
    include('../che/VK.php');
    include('../che/VKException.php');

    $vk = new \che\VK('{APP_ID}', '{SECRET}');

    try {
        $vk->setAccessToken($_GET['token']);
        $wall = $vk->wall_get(array(
            'count' => 10,
            'filter' => 'owner'
        ));

        echo "<pre>" . print_r($wall, 1) . "</pre>";


        $users = $vk->post('users.get', array(
            'user_ids' => "1,3990053",
            'fields' => 'photo_50,city'
        ));

        echo "<pre>" . print_r($users, 1) . "</pre>";
    } catch (\che\VKException $e) {
        echo 'Error: ' . $e->getMessage();
    }