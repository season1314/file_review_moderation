<?php
/**
 * Utilities methods
 */

if (!defined('ABSPATH')) exit;

// verify role
function utilities_verify_role($roles,$select_role) {
     //solve um_xxxx
     foreach ($roles as $role) {
        if (strpos($role, '_') !== false) {
            $parts = explode('_', $role);
            $clean_roles[] = array_pop($parts); 
        } else {
            $clean_roles[] = $role;
        }
    }
    
    //verify user as role
    if(!empty($select_role)){
        $select_role_array = explode(',', $select_role);
        $result = array_intersect($clean_roles, $select_role_array );
        if(empty($result)){
            return  false;
        }
    }
    return true;
}


//get application list
function utilities_application_list($page = 1, $limit = 20, $email = '', $status = ''){
        global $wpdb;
        $table = $wpdb->prefix . 'file_reviews_10min';
        $where = [];
        $params = [];
        if (!empty($email)) {
            $where[] = "email LIKE %s";
            $params[] = '%' . $wpdb->esc_like($email) . '%';
        }
        if (!empty($status)) {
            $where[] = "status = %s";
            $params[] = $status;
        }
        $where_sql = '';
        if (!empty($where)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where);
        }
    
        $count_sql = "SELECT COUNT(*) FROM $table $where_sql";
        $count_prepared = !empty($params)
            ? $wpdb->prepare($count_sql, $params)
            : $count_sql;
    
        $total = (int) $wpdb->get_var($count_prepared);
    
        $offset = ($page - 1) * $limit;
    
        $list_sql = "SELECT * FROM $table $where_sql ORDER BY id DESC LIMIT %d OFFSET %d";
    
        $list_params = $params;
        $list_params[] = $limit;
        $list_params[] = $offset;
        $list_prepared = $wpdb->prepare($list_sql, $list_params);
        $rows = $wpdb->get_results($list_prepared);
        $total_pages = ceil($total / $limit);
    
        return [
            'rows' => $rows,
            'total' => $total,
            'total_pages' => $total_pages,
            'current_page' => (int) $page,
            'limit' => (int) $limit
        ];
    }