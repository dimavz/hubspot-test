<?php

class HubspotWidget extends WP_Widget
{

    function __construct()
    {

        $args = array(
            'name' => 'Виджет формы отправки сообщения',
            'description' => 'Виджет выводит форму для отправки сообщения',
            'classname' => 'hubspot'
        );
        parent::__construct('hs_widg', '', $args);
    }

    function widget($args, $instance)
    {
        add_action( 'wp_footer', 'hs_add_scripts' );
        extract($args);
        extract($instance);

        $title = apply_filters('widget_title', $title);
        $text = apply_filters('widget_text', $text);

        echo $before_widget;
        echo $before_title . $title . $after_title;
        echo "<div>$text</div>";
        ?>
        <form action="" method="post" id="hs_form">
            <p>
                <label for="hs_first_name">Имя:</label>
                <input type="text" name="hs_first_name" id="hs_first_name">
            </p>
            <p>
                <label for="hs_last_name">Фамилия:</label>
                <input type="text" name="hs_last_name" id="hs_last_name">
            </p>
            <p>
                <label for="hs_subject">Тема сообщения:</label>
                <input type="text" name="hs_subject" id="hs_subject">
            </p>
            <p>
                <label for="hs_message">Сообщение:</label>
                <textarea name="hs_message" id="hs_message"></textarea>
            </p>
            <p>
                <label for="hs_email">Email:</label>
                <input type="text" name="hs_email" id="hs_email">
            </p>
            <p>
                <input type="submit" id="hs_submit" name="hs_submit" value="Отправить">
                <span id="loader" style="display: none;">
                    <img src="<?php echo plugins_url( 'img/loader.gif', __FILE__ ); ?>" alt="">
                </span>
            </p>
            <div id="res"></div>
        </form>
        <?php
        echo $after_widget;
    }

    function form($instance)
    {
        extract($instance);
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title') ?>">Заголовок:</label>
            <input type="text" name="<?php echo $this->get_field_name('title') ?>"
                   id="<?php echo $this->get_field_id('title') ?>"
                   value="<?php if (isset($title)) echo esc_attr($title); ?>" class="widefat">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('text') ?>">Текст:</label>
            <textarea class="widefat" name="<?php echo $this->get_field_name('text') ?>"
                      id="<?php echo $this->get_field_id('text') ?>" cols="20"
                      rows="5"><?php if (isset($text)) echo esc_attr($text); ?></textarea>
        </p>

        <?php
    }

    function update($new_instance, $old_instance)
    {
        $new_instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $new_instance['text'] = str_replace('<p>', '', $new_instance['text']);
        $new_instance['text'] = str_replace('</p>', '<br>', $new_instance['text']);
        return $new_instance;
    }

}