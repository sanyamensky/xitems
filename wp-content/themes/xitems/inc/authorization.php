<?php

function ajax_login_init(){

    wp_register_script('ajax-login-script', get_template_directory_uri() . '/js/authorization-ajax.js', array('jquery') );
    wp_enqueue_script('ajax-login-script');

    wp_localize_script( 'ajax-login-script', 'ajax_login_object', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => home_url(),
        'loadingmessage' => __('Проверка данных...')
    ));

    // Позволяем пользователю без каких-либо привилегий запуск функции ajax_login () в AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
}

// Выполняем действие ajax_login_init, только если пользователь не вошел в систему
if (!is_user_logged_in()) {
    add_action('init', 'ajax_login_init');
}

function ajax_login(){

    // Сначала проверяем одноразовое значение скрытого поля безопасности и если оно выходит из строя - выполнение функции будет прервано
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Получаем данные POST и выводим результат
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Неверное имя или пароль.')));
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Вход выполнен.')));
    }

    die();
}