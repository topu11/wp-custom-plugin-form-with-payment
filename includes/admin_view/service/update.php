<?php
$id=$_GET['id'];
if(empty($id) || !isset($_GET['id']))
{
  echo "Sorry This is Wrong Page ID Missing";
  exit;
}
global $wpdb;

$table_name = $wpdb->prefix . 'encoderit_custom_form_services'; 
$row_service = $wpdb->get_row("SELECT * FROM " . $table_name . " where id =$id");

if(isset($_POST['btn'])){
 
  if(!is_numeric($_POST["service_price"]))
  {
     echo "<h1>Can Not Insert Non Numeric data error</h1>";
  }else
  {
    
    $table_name = $wpdb->prefix . 'encoderit_custom_form_services';

      $data = array(
        "service_name" => $_POST["service_name"],
        "service_price" => $_POST["service_price"],
        "active_status" => $_POST["active_status"],
        "updated_at" => date('Y-m-d H:i:s')
      );

        $where_condition=array(
            'id' => $id
        );
     
       $inserted=$wpdb->update($table_name, $data, $where_condition);
      
      if($inserted)
      {
        ?>
        <script>
          window.location.href='<?=admin_url() . '?page=encoderit-custom-services'?>'
        </script>
        <?php
      }
    
  }


}
  
  
  

?>
<style type='text/css'>
/* form elements */
form {
  margin:10px; 
  padding: 0 5px;
  background: #F5F5F5;  
}
label {
  display:block;
  font-weight:bold;
  margin:15px 0;
}
input {
  padding:2px;
  border:1px solid #eee;
  font: normal 1em Verdana, sans-serif;
  color:#777;
}
select
{
 padding:2px;
  border:1px solid #eee;
  font: normal 1em Verdana, sans-serif;
  color:#777;
  width:100%;
}
input.buttons { 
  font: bold 12px Arial, Sans-serif; 
  height: 50px;
  width: 150px;
  margin-top:20px;
  margin-left:200px;
  margin-bottom: 50px;
  cursor: pointer;  
  color: #333;
  background: #e7e6e6 url(MarketPlace-images/button.jpg) repeat-x;
  border: 1px solid #dadada;
}

</style>
<div style="padding: 30px;">
<h1>Add New Service</h1>
  <form action="" method='POST' enctype="multipart/form-data">
    <label for="">Service Name:</label>
    <input type="text" name="service_name" value="<?=$row_service->service_name?>" style="width:100%;" required>
    
    <label for="">Service Price:</label>

    <input type="text" name="service_price" value="<?=$row_service->service_price?>" style="width:100%;" required>
    <label for="">Service Status:</label>
    <select id="active_status" name="active_status" required >
       
    <option value="2" <?php if($row_service->active_status == 2) echo 'selected'; ?>>Active</option>
    <option value="1" <?php if ($row_service->active_status == 1) echo 'selected'; ?>>InActive</option>

    </select>
    <br>
    <br>
    <input class="buttons" type="submit" name="btn">    
  </form>
</div>