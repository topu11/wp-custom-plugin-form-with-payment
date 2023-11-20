<?php

class encoderit_admin_functionalities
{
    public static function get_service_list()
    {
        require_once( dirname( __FILE__ ).'/admin_view/service/index.php' );
        
    }
    public static function add_new_service()
    {
        require_once( dirname( __FILE__ ).'/admin_view/service/add.php' );
    }
    public static function enoderit_custom_form_admin_submit()
    {
     
        if (!wp_verify_nonce($_POST['nonce'], 'admin_ajax_nonce_encoderit_custom_form')) {
            echo json_encode([
                'success' => 'error',
                'message'=>'Invalid Nonce field'
            ]);
        }else
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'encoderit_custom_form';
        
            $data = array(
                'files_by_admin' => self::save_files_by_admin(),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $where_condition=array(
                'id' => $_POST['form_id']
            );
             
            $inserted=$wpdb->update($table_name, $data, $where_condition);
                if($inserted)
                {
                    echo  json_encode([
                        'success' => 'success',
                        'message'=>'Form Submmited Successfully'
                    ]);
                }else
                {
                    echo  json_encode([
                        'success' => 'error',
                        'message'=>'Something worng.;'
                    ]);
                }
        }
        wp_die(); 
    }
    public static function update_service()
    {
        require_once( dirname( __FILE__ ).'/admin_view/service/update.php' );
    }
    public static function save_files_by_admin()
    {
        global $wpdb;
        $id=$_POST['form_id'];
        $table_name = $wpdb->prefix . 'encoderit_custom_form';
        $file_paths=[];
        $result = $wpdb->get_row("SELECT * FROM " . $table_name . " where id =$id");
        if(!empty($result->files_by_admin))
        {

            $file_paths=json_decode($result->files_by_admin,true);
        }
        $wordpress_upload_dir = wp_upload_dir();
       foreach($_FILES['file_array']['name'] as $key=>$value)
       {

         $tail='';
         $file_name_with_addition='';
         $new_file_path = $wordpress_upload_dir['path'] . '/' . $value;
         $i=1;
         while (file_exists($new_file_path)) {
            $i++;
            $new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' .$value;
        }
        if (move_uploaded_file($_FILES['file_array']['tmp_name'][$key], $new_file_path)) {
            if($i>1)
            {
                //$string_json_path_Single=$wordpress_upload_dir['url'].'/' . $i . '_' .$value;
                $tail=$wordpress_upload_dir['subdir'].'/' . $i . '_' .$value;
                $file_name_with_addition= $i . '_' .$value;
            }else
            {
                //$string_json_path_Single=$wordpress_upload_dir['url'].'/'.$value; 
                $tail=$wordpress_upload_dir['subdir'].'/'.$value;
                $file_name_with_addition= $value; 
            }
            
           $single_file=[
            'name'=>$file_name_with_addition,
            'paths'=>$tail
           ];
           array_push($file_paths,$single_file);
        }   
       }
       return json_encode($file_paths);
    }
}
