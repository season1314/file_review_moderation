<?php

//Render frm desk
function display_frm_desk($atts) {
    wp_enqueue_script(
        'frm-desk-js', 
        plugins_url( 'assets/js/desk.js', __FILE__ ), 
        array('jquery'),
        '1.0', 
        true 
    );

    wp_localize_script('frm-desk-js', 'frm_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('file_upload_nonce')
    ));

    $atts = shortcode_atts( 
        array(
            'role' => '',
            'download' =>''
        ), 
        $atts, 'frm_desk' 
    );

    $select_role = $atts['role'];
    $file_url = $atts['download'];

    //Verify user login status and role
    $current_user = wp_get_current_user();
    $template_path = __DIR__ . '/templates/frm-desk-form.php';
    if ( 0 == $current_user->ID ) {
        $error = 'Please log in first to access.';
        return $error;
    }

    $wp_user_id = $current_user ->ID;
    $email = $current_user -> email;
    $roles = $current_user->roles;

    $verify_role = utilities_verify_role($roles,$select_role);

    if(!$verify_role){
        $error = 'You do not have permission to access';
        return $error;
    }


    //Verify user submit record
    global $wpdb;
    $table_name = $wpdb->prefix . 'file_reviews_10min';
    $table_staff = $wpdb->prefix . 'bookly_staff';
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT t.*, bs.id AS staff_id
             FROM $table_name t
             LEFT JOIN $table_staff bs
             ON bs.wp_user_id = t.wp_user_id
             WHERE t.wp_user_id = %d
             LIMIT 1",
            $wp_user_id
        )
    );

    $title ="";
    $content = "";
    $status = $row->status ?? 'none';

    if(!empty($row)&& $status === 'approved' && empty($row->staff_id)){
        $title ="Application Approved";
        $content = "Please wait while the administrator sets up your Buddy feature. It will be available once ready. If you have any questions, please send a ticket for assistance.";

    }

    if(!empty($row)&& $status === 'approved' && !empty($row->staff_id)){
        $status = 'completed';
        $title ="";
        $content = "";

    }

    if(!empty($row)&& $status === 'rejected'){
        $title ="Application Rejected";
        $content = "Your application has not been approved, due to'" . $row->last_notes  ."'. Please complete the form again and resubmit";

    }
    if(!empty($row) &&  $status  === 'pending'){
        $title ="Awaiting Verification";
        $content = "You have submitted your application documents. Please wait patiently for admin review. If there are any errors in the submitted files, you can resubmit them.";
    }

    if(empty($row)){
        $title = "Waiting UpLoad File";
        $content = "Please download the document. After reading it carefully, please sign and re-upload it for verification.";
    }

    ob_start();
    include $template_path;
    $output = ob_get_clean();
    return $output;
}
add_shortcode( 'frm_desk', 'display_frm_desk' );


//AjAX handle upload file
add_action('wp_ajax_frm_upload_action', 'handle_ajax_file_upload');
function handle_ajax_file_upload() {
    // check_ajax_referer('file_upload_nonce');
    if (!empty($_FILES['file'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $movefile = wp_handle_upload($_FILES['file'], array('test_form' => false));
        if ($movefile && !isset($movefile['error'])) {
            wp_send_json_success(['url' => $movefile['url']]);
        } else {
            wp_send_json_error($movefile['error']);
        }
    }
    wp_die();
}

//AjAX handle submit
add_action('wp_ajax_frm_from_submit', 'handle_ajax_frm_form_submit_callback');
function handle_ajax_frm_form_submit_callback() {
    global $wpdb;
    $url = $_POST['file_url'];
    if(empty($url)) wp_send_json_error('Error : Please upload a file before submitting.');
    //verify user
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) wp_send_json_error('Error : Unable to locate user. Please refresh the page and retry.');
    $wp_user_id = $current_user ->ID;
    $email = $current_user->user_email;
    $table_name = $wpdb->prefix . 'file_reviews_10min';
    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $table_name WHERE wp_user_id = %d LIMIT 1",
        $wp_user_id
    ));

    if(empty($row)){
        $data = array(
            'email' => $email,
            'wp_user_id' => $wp_user_id,
            'status' => 'pending',
            'file_url' => $url,
            'created_at' => current_time('mysql'),
        );
        
        $format = array(
            '%s', // email
            '%d', // wp_user_id
            '%s', // status
            '%s',//file_url
            '%s', // created_at
        );
        $result = $wpdb->insert($table_name, $data, $format);
        if ($result === false) {
            wp_send_json_error('Error :database error');
        } else {
            wp_send_json_success( 'Successful submitted! Please wait for admin approval');
        }
    }else{
        if( $row->status == 'approved'){
            wp_send_json_error('The application status has changed. Please refresh the page and try again.');
        }else{
            $data = array('status' => 'pending','file_url' => $url,);
            $format = array('%s','%s',);
            $where = array('wp_user_id' => $wp_user_id);
            $where_format = array('%d');
            $result = $wpdb->update($table_name, $data, $where, $format, $where_format);
            if ($result === false) {
                wp_send_json_error('Error :database error');
            } else {
                wp_send_json_success( 'Successful submitted! Please wait for admin approval');
            }
        }
    }
}




