<?php

function hs_ajax_send_message()
{
    if (!wp_verify_nonce($_POST['security'], 'objajax')) {
        exit('{"success":false,"mess":"Ошибка безопасности!"}');
    }
    parse_str($_POST['formData'], $arr);

    if (!checkFields($arr)) {
        exit('{"success":false,"mess":"Заполните все поля!"}');
    }

    if (!is_email($arr['hs_email'])) {
        exit('{"success":false,"mess":"Email не соответствует формату!"}');
    }

    /* https://packagist.org/packages/hubspot/api-client */

//    $api_key = 'b1014e95-add5-422c-a15f-75054137a8a8';
    $options = get_option('hs_messages_options');
    $api_key = $options['hubspot_apikey'];
    $notice_admin = $options['notice_admin'];

    $isSaveHubSpot = false;

    if (!empty($api_key)) {
        $postfields = "{\"properties\":{\"company\":\"\",\"email\":\"{$arr['hs_email']}\",\"firstname\":\"{$arr['hs_first_name']}\",\"lastname\":\"{$arr['hs_last_name']}\",\"phone\":\"\",\"website\":\"\"}}";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.hubapi.com/crm/v3/objects/contacts?hapikey={$api_key}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS => "{\"properties\":{\"company\":\"Biglytics\",\"email\":\"bcooper2@biglytics.net\",\"firstname\":\"Bryan2\",\"lastname\":\"Cooper2\",\"phone\":\"(877) 929-0685\",\"website\":\"biglytics.net\"}}",
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            exit("{\"success\":false,\"mess\":\"cURL Error #: {$err}\"}");
//            echo "cURL Error #:" . $err;
        } else {
            $isSaveHubSpot = true;
//            echo $response;
        }
    }

    if ($notice_admin) {
        $admin_email = get_option('admin_email');
        wp_mail($admin_email, 'Отправлен контакт с сайта', 'Отправлен контакт с сайта в HubSpot.com');
    }

    global $wpdb;

    if ($wpdb->query($wpdb->prepare(
        "INSERT INTO wp_hubspot_messages (firstname,lastname,subject,message,email,is_save_hubspot) VALUES (%s,%s,%s,%s,%s,%s)",
        $arr['hs_first_name'],
        $arr['hs_last_name'],
        $arr['hs_subject'],
        $arr['hs_message'],
        $arr['hs_email'],
        $isSaveHubSpot ? '1' : '0'
    ))) {
        exit('{"success":true,"mess":"Сообщение отправлено!"}');
    } else {
        exit('{"success":false,"mess":"Ошибка записи!"}');
    }
    wp_die();
}

function checkFields($arr)
{
    if (empty($arr['hs_first_name']) ||
        empty($arr['hs_last_name']) ||
        empty($arr['hs_subject']) ||
        empty($arr['hs_message']) ||
        empty($arr['hs_email'])
    ) {
        return false;
    } else {
        return true;
    }
}