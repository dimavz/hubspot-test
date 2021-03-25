<?php
/*
Plugin Name: Отправка сообщений и создание аккаунта на HubSpot.com
Description: Плагин предоставляет виджет, позволяющий отправлять сообщения и создавать аккаунт на hubspot.com
Author: Дмитрий затуленко
Plugin URI:
Author URI:
*/

include dirname(__FILE__) . '/CHubspotWidget.php';

register_activation_hook( __FILE__, 'hs_create_table_messages' );
add_action( 'widgets_init', 'hubspot_widget' );


function hubspot_widget(){
    register_widget( 'HubspotWidget' );
}

function hs_create_table_messages(){
    global $wpdb;
    $query = "CREATE TABLE IF NOT EXISTS `wp_hubspot_messages` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`firstname` varchar(100) NOT NULL,
		`lastname` varchar(150) NOT NULL,
		`subject` varchar(250) NOT NULL,
		`message` text NOT NULL,
		`email` varchar(150) NOT NULL,
		`send` enum('0','1') NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $wpdb->query($query);
}


