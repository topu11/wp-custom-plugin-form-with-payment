<?php

class encoderit_create_custom_table
{
    public static function create_custom_tables()
    {
        global $wpdb;
       
        $table_name = $wpdb->prefix . 'encoderit_custom_form_services';

       $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS  $table_name (
            `id` BIGINT NOT NULL AUTO_INCREMENT,
            `service_name` VARCHAR(100) NOT NULL,
            `service_price` FLOAT NOT NULL,
            `active_status` TINYINT NOT NULL DEFAULT '1' COMMENT '1 Deactive , 2 Active',
            `created_at` DATETIME NULL DEFAULT NULL,
            `updated_at` DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
        $table_name = $wpdb->prefix . 'encoderit_custom_form';

       $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS  $table_name (
            `id` BIGINT NOT NULL AUTO_INCREMENT ,
             `user_id` BIGINT NOT NULL ,
            `person_number` MEDIUMINT NOT NULL ,
            `description` TEXT NULL DEFAULT NULL ,
            `services` TEXT NULL DEFAULT NULL ,    
            `files_by_user` JSON NULL DEFAULT NULL ,
            `files_by_admin` JSON NULL DEFAULT NULL , 
            `payment_method` VARCHAR(100) NULL , 
            `transaction_number` VARCHAR(100) NULL,
            `total_price` FLOAT NOT NULL , 
            `created_at` TIMESTAMP NULL DEFAULT NULL , 
            `updated_at` TIMESTAMP NULL DEFAULT NULL , PRIMARY KEY (`id`)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
