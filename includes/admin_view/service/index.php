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
            "SL No." => "SL No.",
            "Service Name" => "Service Name",
            "Service Price" => "Service Price",
            "Active Status"  =>"Active Status",
            'Action'  => 'Action'
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
                    'Service Name' => array('Service Name', true),
                    'SL No.'=>array('SL No.',true)
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
        $active_status=[
           '2'=>'Active',
           '1'=>'Deactive'
        ];
        $table_name = $wpdb->prefix . 'encoderit_custom_form_services';
        if (isset($_POST['s']) && !empty($_POST['s'])) {
            
            $search_data = $_POST['s'];
            $result = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE service_name LIKE '%" . $search_data . "%' OR service_price LIKE '%" . $search_data . "%'");
        } else {
            $result = $wpdb->get_results("SELECT * FROM " . $table_name . " ORDER BY id DESC");
        }
        if (count($result) != 0) {
            $sl = 1;
            foreach ($result as $singledata) {
                


                $data[] = array(
                    'SL No.'                    => $sl,
                    'Service Name'              => $singledata->service_name,
                    'Service Price'             =>'$ '. $singledata->service_price,
                    'Active Status'             =>$active_status[$singledata->active_status],
                    'Created At'                => $singledata->created_at,
                    'Action'                    => '<a  href="' .admin_url() .'admin.php'. '?page=encoderit-custom-service-update&id=' . $singledata->id . '" class="button" target="_blank" style="background-color: #009B00;color: black">Update</a>',
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
            case "SL No.":
            case "Service Name":
            case "Service Price":
            case "Active Status":
            case "Created At":
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
        $orderby = 'SL No.';
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
        <h1 class="pbwp-headingtag pbwp-mb-4 pbwp-p-1">Service Lists</h1>
        <button onclick="location.href='<?=admin_url() .'admin.php'. '?page=encoderit-custom-service-create'?>'">Add New Service</button>
    </div>
    <div class="pbwp-mt-3">
        <form method="post" class="pbwp-d-inline" style="display: none;">
            <input type="hidden" name="page" value="pbwp_product_table" />
            <?php $pbwp_products->search_box('search', 'search_id'); ?>
        </form>
    </div>
    <?php $pbwp_products->display(); ?>
</div>
