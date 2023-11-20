<?php

class encoderit_user_functionalities
{
    public static function get_all_case_by_user()
    {
        if (is_super_admin()) {
            
            require_once( dirname( __FILE__ ).'/admin_view/cases/index.php' );
        } else {
            // The current user is not a super admin
            require_once( dirname( __FILE__ ).'/user_view/cases/index.php' );
        }
        
        
    }
    public static function add_new_case_by_user()
    {
        require_once( dirname( __FILE__ ).'/user_view/cases/add.php' );
    }
    public static function view_single_case()
    {
        if (is_super_admin()) {
            
            require_once( dirname( __FILE__ ).'/admin_view/cases/view.php' );
        } else {
            // The current user is not a super admin
            require_once( dirname( __FILE__ ).'/user_view/cases/view.php' );
        }
    }
    public static function download_pdf_files()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'encoderit_custom_form';
       $id=$_GET['id'];
        if(empty($id) || !isset($_GET['id']))
        {
          echo "Sorry This is Wrong Page ID Missing";
          exit;
       }      
        $result = $wpdb->get_row("SELECT * FROM " . $table_name . " where id =$id");
        //var_dump($result);
       

        $wp_content_path = wp_upload_dir()['path'];

        // Specify the empty zip file path
        $empty_zip_file_path = $wp_content_path . '/empty_file.zip';
    
        // Create a zip file
        $zip = new ZipArchive();
        if ($zip->open($empty_zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->close();
           // echo 'Empty zip file created successfully at ' . $empty_zip_file_path;
        } else {
            echo 'Failed to create the empty zip file.';
        }
        $files_by_admin=json_decode($result->files_by_admin,true);

        if ($zip->open($empty_zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files_by_admin as $file) {
                $file_path = wp_upload_dir()['baseurl'].$file['paths'];
                if (file_exists($file_path)) {
                    $zip->addFile($file_path, $file['name']);
                    echo 'Empty zip file created successfully at ' . $empty_zip_file_path;
                }
            }
            $zip->close();
    
            
        }
        
    }
}
