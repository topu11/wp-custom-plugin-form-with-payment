<?php
ob_start();
// WP_List_Table is not loaded automatically so we need to load it in our application
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


/**
 * Create a new table class that will extend the WP_List_Table
 */
class EncoderITCustomForm extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->process_bulk_action();
       

        $data = $this->table_data();
        usort($data, array(&$this, 'sort_data'));

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
    // public function get_bulk_actions() {
	// 	return array(
	// 		'trash' => __( 'Move to Trash', 'admin-table-tut' ),
	// 	);
	// }

	/**
	 * Get bulk actions.
	 *
	 * @return void
	 */
	// public function process_bulk_action() {
	// 	if ( 'trash' === $this->current_action() ) {
	// 		$post_ids = filter_input( INPUT_GET, 'draft_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	// 		var_dump($post_ids );
	// 		global $wpdb;
	// 		$table_name = $wpdb->prefix . 'contacts';
	// 		$wpdb->query("DELETE from $table_name  WHERE id > 0");

	// 	}
	// }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            "Case No." => "Case No.",
            "Amount" => "Amount",
            "Date"        =>      "Date",
            "Admin File Upload"  =>"Download Submitted File By Admin",
        );

        return $columns;
    }


  

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
                    'Amount' => array('Amount', true),
                    'Case No.'=>array('Case No.',true)
                    );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
   
    private function table_data()
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'encoderit_custom_form';
        if (isset($_POST['s']) && !empty($_POST['s'])) {
            
            $search_data = $_POST['s'];
            $result = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE id = '%" . $search_data . "%' OR total_price LIKE '%" . $search_data . "%' OR  user_id LIKE '%" . $search_data . "%' OR  person_number LIKE '%" . $search_data . "%' OR  transaction_number LIKE '%" . $search_data . "%' OR  payment_method LIKE '%" . $search_data . "%' OR  created_at LIKE '%" . $search_data . "%' AND user_id = ".get_current_user_id()." ");
        } else {
            $result = $wpdb->get_results("SELECT * FROM " . $table_name . " where user_id = ".get_current_user_id()."  ORDER BY id DESC");
        }
        if (count($result) != 0) {
            $sl = 1;
               $is_ssl=false;
               if(str_contains(site_url(), 'https'))
                {
                   $is_ssl=true;
                }
            foreach ($result as $singledata) {
                
                
                $files=[];
                $download_link='';
                if(!empty($singledata->files_by_admin))
                {
                        $files_by_admin=json_decode($singledata->files_by_admin,true);
                        foreach ($files_by_admin as $file) {
                            if($is_ssl)
                            {

                                $file_path = str_replace("http://","https://",wp_upload_dir()['baseurl'].$file['paths']);
                            }else
                            {
                                $file_path = wp_upload_dir()['baseurl'].$file['paths'];
                            }
                            array_push($files,$file_path);
                        }
                        $a=implode(',',$files);

                        $download_link='<a class="button" style="background-color:#007bff;color:#fff" href="javascript:void(0)" 
                        data-name="'.wp_get_current_user()->user_email.'"  id="user_id_enc_don_'.$sl.'" data-file="'.$a.'" onclick="enc_download(this.id)">Download</a>';
                        
             
                   
                }
                $date=explode(' ',$singledata->created_at)[0];
                //$date=str_replace('-','/',$date);
                

                $data[] = array(
                    'Case No.'                    => '#'.$singledata->id,
                    'Amount'              =>'$ '. $singledata->total_price,
                    'Date'                => implode('/',array_reverse(explode('-',$date))),
                    'Admin File Upload'        =>$download_link,
                    'Action'                    => '',
                );
                $sl++;
            }
        } else {
            $data = [];
        }

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case "Case No.":
            case "Amount":
            case "Date":
            case "Admin File Upload":
            case 'Action':
                return $item[$column_name];

            default:
                return print_r($item, true);
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a, $b)
    {
        // Set defaults
        $orderby = 'Case No.';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }


        $result = strnatcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }
 
   

}
$pbwp_products = new EncoderITCustomForm();
$pbwp_products->prepare_items();

?>
<div class="wrap pbwp">
    <div>
        <h1 class="pbwp-headingtag pbwp-mb-4 pbwp-p-1">All Cases</h1>
    </div>
    <div class="pbwp-mt-3">
        <form method="post" class="pbwp-d-inline" style="display: none;">
            <input type="hidden" name="page" value="pbwp_product_table" />
            <?php $pbwp_products->search_box('search', 'search_id'); ?>
        </form>
    </div>
    <?php $pbwp_products->display(); ?>
</div>
