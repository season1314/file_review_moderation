<?php
add_action('admin_menu', function () {
    add_menu_page(
        'Application Review',
        'FRM',
        'manage_options',
        'app-review',   
        'app_review_page',
        'dashicons-clipboard',
        26
    );
});



//Render page
function app_review_page() {
    $result = utilities_application_list(1,20,'','');
    $rows = $result['rows'];
    $total_pages = $result['total_pages'];
    $current_page = $result['current_page'];
    wp_enqueue_script(
        'frm-list-js', 
        plugins_url( 'assets/js/list.js', __FILE__ ), 
        array('jquery'),
        '1.0', 
        true 
    );

    wp_localize_script('frm-list-js', 'frm_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('frm_list_nonce')
    ));

    include plugin_dir_path(__FILE__) . 'templates/app-review.php';
}


//Ajax handle list operation
add_action('wp_ajax_frm_list', 'handle_ajax_list');
function handle_ajax_list(){
    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 'pending';
    $result = utilities_application_list($page,20,$search,$status);
    wp_send_json([
        'success' => true,
        'data' => $result
    ]);
}

//Ajax handle app operation
add_action('wp_ajax_frm_app_opt','handle_ajax_opt');
function handle_ajax_opt(){
    $id = $_POST['id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    if(empty($id)){
        wp_send_json_error('Error : Id can not be null');
    }
    if(empty($status)){
        wp_send_json_error('Error : Status can not be null');
    }
    global $wpdb;
    $table = $wpdb->prefix . 'file_reviews_10min';
    $data = ['status' => $status,'reviewer_id' => get_current_user_id()];
    if (!empty($notes)) {$data['last_notes'] = $notes;}
    $result = $wpdb->update($table,$data,['id' => $id]);
    if ($result === false) {
        wp_send_json_error('Update failed');
    } else {
        wp_send_json_success('Update success');
    }
}