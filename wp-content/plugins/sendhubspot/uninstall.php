<?php
if( !defined('WP_UNINSTALL_PLUGIN') ) exit;

global $wpdb;
$query = "DROP TABLE IF EXISTS `wp_hubspot_messages`";
$wpdb->query($query);