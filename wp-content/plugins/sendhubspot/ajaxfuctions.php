<?php

function hs_ajax_send_message()
{
    if (!wp_verify_nonce($_POST['security'], 'objajax')) {
        die('Ошибка безопасности!');
    }
    parse_str($_POST['formData'], $arr);

    if (!checkFields($arr)) {
        exit('{"success":false,"mess":"Заполните все поля!"}');
    }

    if (!is_email($arr['hs_email'])) {
        exit('{"success":false,"mess":"Email не соответствует формату!"}');
    }

    $admin_email = get_option( 'admin_email');

    wp_mail( $admin_email, 'Отправлена форма с сайта','Отправлена форма с сайта' );

    global $wpdb;

    if ($wpdb->query($wpdb->prepare(
        "INSERT INTO wp_hubspot_messages (firstname,lastname,subject,message,email,send) VALUES (%s,%s,%s,%s,%s,%s)",
        $arr['hs_first_name'],
        $arr['hs_last_name'],
        $arr['hs_subject'],
        $arr['hs_message'],
        $arr['hs_email'],
        '1'
    ))) {
        echo '{"success":true,"mess":"Сообщение отправлено!"}';
    } else {
        echo '{"success":false,"mess":"Ошибка записи!"}';
    }
    die;
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