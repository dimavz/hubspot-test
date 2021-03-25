<?php

/**
 * получение подписчиков
 **/
function get_messages($all = false){
    global $wpdb;
    $pagination_params = pagination_params();

    if($all){
        return $wpdb->get_results("SELECT * FROM wp_hubspot_messages", ARRAY_A);
    }

    return $wpdb->get_results("SELECT * FROM wp_hubspot_messages LIMIT {$pagination_params['start_pos']}, {$pagination_params['perpage']}", ARRAY_A);
}

/**
 * параметры пагинации
 **/
function pagination_params(){
    global $wpdb;

    // кол-во сообщений на страницу
    $options = get_option( 'hs_messages_options' );
    $perpage = $options['perpage'];
    // кол-во записей
    $count = $wpdb->get_var("SELECT COUNT(*) FROM wp_hubspot_messages");
    // необходимое кол-во страниц
    $count_pages = 1;
    if($count)
    {
        $count_pages = ceil($count / $perpage);
    }
    // получение текущей страницы
    if( isset($_GET['paged']) ){
        $page = (int)$_GET['paged'];
        if( $page < 1 ) $page = 1;
    }else{
        $page = 1;
    }
    // если запрошенная страница больше максимума
    if( $page > $count_pages ) $page = $count_pages;
    // начальная позиция для запроса
    $start_pos = ($page - 1) * $perpage;

    $pagination_params = array(
        'page' => $page,
        'count' => $count,
        'count_pages' => $count_pages,
        'perpage' => $perpage,
        'start_pos' => $start_pos
    );
    return $pagination_params;
}

/**
 * Постраничная навигация
 **/
function pagination($page, $count_pages){
    // << < 3 4 5 6 7 > >>
    $back = null; // ссылка НАЗАД
    $forward = null; // ссылка ВПЕРЕД
    $startpage = null; // ссылка В НАЧАЛО
    $endpage = null; // ссылка В КОНЕЦ
    $page2left = null; // вторая страница слева
    $page1left = null; // первая страница слева
    $page2right = null; // вторая страница справа
    $page1right = null; // первая страница справа

    $uri = "?";
    if( $_SERVER['QUERY_STRING'] ){
        foreach ($_GET as $key => $value) {
            if( $key != 'paged' ) $uri .= "{$key}=$value&amp;";
        }
    }

    if( $page > 1 ){
        $back = "<a class='nav-link' href='{$uri}paged=" .($page-1). "'>Назад</a>";
    }
    if( $page < $count_pages ){
        $forward = "<a class='nav-link' href='{$uri}paged=" .($page+1). "'>Вперед</a>";
    }
    if( $page > 3 ){
        $startpage = "<a class='nav-link' href='{$uri}paged=1'>В начало</a>";
    }
    if( $page < ($count_pages - 2) ){
        $endpage = "<a class='nav-link' href='{$uri}paged={$count_pages}'>В конец</a>";
    }
    if( $page - 2 > 0 ){
        $page2left = "<a class='nav-link' href='{$uri}paged=" .($page-2). "'>" .($page-2). "</a>";
    }
    if( $page - 1 > 0 ){
        $page1left = "<a class='nav-link' href='{$uri}paged=" .($page-1). "'>" .($page-1). "</a>";
    }
    if( $page + 1 <= $count_pages ){
        $page1right = "<a class='nav-link' href='{$uri}paged=" .($page+1). "'>" .($page+1). "</a>";
    }
    if( $page + 2 <= $count_pages ){
        $page2right = "<a class='nav-link' href='{$uri}paged=" .($page+2). "'>" .($page+2). "</a>";
    }

    return $startpage.$back.$page2left.$page1left.'<a class="active-page">'.$page.'</a>'.$page1right.$page2right.$forward.$endpage;
}