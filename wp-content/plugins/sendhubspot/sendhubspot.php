<?php
/*
Plugin Name: Отправка сообщений и создание аккаунта на HubSpot.com
Description: Плагин предоставляет виджет, позволяющий отправлять сообщения и создавать аккаунт на hubspot.com
Author: Дмитрий затуленко
Plugin URI:
Author URI:
*/

include dirname(__FILE__) . '/CHubspotWidget.php';
include dirname(__FILE__) . '/ajaxfuctions.php';
include dirname(__FILE__) . '/helper.php';
include dirname(__FILE__) . '/messages-subpage.php';


register_activation_hook( __FILE__, 'hs_create_table_messages' );
add_action( 'widgets_init', 'hubspot_widget' );
add_action( 'admin_init', 'hs_messages_admin_settings' );
add_action( 'admin_menu', 'hs_messages_admin_menu' );
add_action( 'wp_ajax_send_message', 'hs_ajax_send_message' );
add_action( 'wp_ajax_nopriv_send_message', 'hs_ajax_send_message' ); // Для неавторизованных пользователей

function hs_add_scripts(){
    wp_register_script( 'send-message', plugins_url( 'js/send-message.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'send-message' );
    wp_localize_script( 'send-message', 'objajax', array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'objajax' ) ) );
}

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
    update_option( 'hs_messages_options', array(
        'perpage' => 5,
    ) );
}

function hs_messages_admin_menu(){
    // $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position
    add_menu_page( 'Настройки плагина Сообщения', 'Сообщения', 'manage_options', 'hs-messages-options', 'hs_messages_options_menu', 'dashicons-email-alt' );

    // $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function
    add_submenu_page( 'hs-messages-options', 'Параметры', 'Параметры', 'manage_options', 'hs-messages-options', 'hs_messages_options_menu');
    add_submenu_page( 'hs-messages-options', 'Сообщения', 'Сообщения', 'manage_options', 'hs-messages-subpage', 'hs_messages_subpage' );

    add_action( 'admin_enqueue_scripts', 'hs_admin_scripts' );
}

function hs_messages_options_menu(){
    ?>
    <div class="wrap">
<!--        <h2>Настройки плагина</h2>-->
        <form action="options.php" method="post">
            <?php settings_fields( 'hs_group' ); ?>
            <?php do_settings_sections( 'hs-messages-options' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function hs_messages_perpage_cb(){
    $options = get_option( 'hs_messages_options' );
    ?>
    <p>
        <input type="text" name="hs_messages_options[perpage]" id="hs_messages_perpage_id" value="<?php echo $options['perpage'] ?>" class="regular-text">
    </p>
    <?php
}

function hs_messages_admin_settings(){
    // $option_group, $option_name, $sanitize_callback
//    register_setting( 'hs_group', 'hs_messages_options', 'wfm_subscriber_sanitize' );
    register_setting( 'hs_group', 'hs_messages_options','hs_options_sanitize' );

    // $id, $title, $callback, $page
    add_settings_section( 'hs_messages_section_id', 'Настройки пагинации для списка сообщений', '', 'hs-messages-options' );

    // $id, $title, $callback, $page, $section, $args
    add_settings_field( 'hs_setting_perpage_id', 'Кол-во сообщений на страницу', 'hs_messages_perpage_cb', 'hs-messages-options', 'hs_messages_section_id', array( 'label_for' => 'hs_setting_perpage_id' ) );
}

function hs_options_sanitize($options){
    $clean_options = array();
    $clean_options['perpage'] = ( (int)$options['perpage'] > 0 ) ? (int)$options['perpage'] : 5;
    return $clean_options;
}


