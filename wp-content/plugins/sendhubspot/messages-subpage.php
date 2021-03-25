<?php

add_action( 'wp_ajax_wfm_subscriber', 'wfm_ajax_subscriber' );
add_action( 'wp_ajax_nopriv_wfm_subscriber', 'wfm_ajax_subscriber' );
add_action( 'wp_ajax_wfm_subscriber_admin', 'wfm_ajax_subscriber_admin' );

function wfm_subscriber_scripts(){
    wp_register_script( 'wfm-subscriber', plugins_url( 'js/wfm-subscriber.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'wfm-subscriber' );
    wp_localize_script( 'wfm-subscriber', 'wfmajax', array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'wfmajax' ) ) );
}

function hs_admin_scripts($hook){
    if( urldecode($hook) != 'сообщения_page_hs-messages-subpage' ) return;
    wp_enqueue_style( 'hs-style', plugins_url( 'css/hs-style.css', __FILE__ ) );
}

function hs_messages_subpage(){
    ?>
    <div class="wrap">
        <h2>Сообщения:</h2>
        <?php
        $pagination_params = pagination_params();
        $messages = get_messages();
        ?>

        <?php if(!empty($messages)): ?>
            <p><b>Кол-во сообщений: <?php echo $pagination_params['count'] ?></b></p>

            <table class="wp-list-table widefat fixed posts" id="wfm-table">
                <thead>
                <tr>
                    <td>ID</td>
                    <td>Имя</td>
                    <td>Фамилия</td>
                    <td>Тема</td>
                    <td>Сообщение</td>
                    <td>Email</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach($messages as $mess): ?>
                    <tr>
                        <td><?php echo $mess['id']; ?></td>
                        <td><?php echo $mess['firstname']; ?></td>
                        <td><?php echo $mess['lastname']; ?></td>
                        <td><?php echo $mess['subject']; ?></td>
                        <td><?php echo mb_strimwidth($mess['message'],0,30,'...'); ?></td>
                        <td><?php echo $mess['email']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Пагинация -->
            <?php if( $pagination_params['count_pages'] > 1 ): ?>
                <div class="pagination">
                    <?php echo pagination($pagination_params['page'], $pagination_params['count_pages']); ?>
                </div>
            <?php endif; ?>
            <!-- Пагинация -->
        <?php else: ?>
            <p>Список сообщений пуст</p>
        <?php endif; ?>
    </div>
    <?php
}