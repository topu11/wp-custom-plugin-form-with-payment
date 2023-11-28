<?php

require_once WP_PLUGIN_DIR . '/SuncodeIT-Custom-Form' . '/assets/css/main.php';
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
  //var_dump(json_decode($result->files_by_user,true));
  $files_by_user=json_decode($result->files_by_user,true);
  $files_by_admin=json_decode($result->files_by_admin,true);
  //var_dump($files_by_admin);
  $service_ids='('.$result->services.')';
  if(empty($result))
  {
    ?>
      <script>
        alert();
      window.location.replace("<?=admin_url().'/index.php'?>")
      
      </script>
    <?php
  }     
?>

<div style="padding: 30px">
    <h1>Case ID #<?=$id?></h1>
    <form action="" method="POST" enctype="multipart/form-data" id="fileUploadForm">
        <div class="row_d">
            <div class="titel_col">
                <label for="">Person number:</label>
            </div>
            <div class="right_col person_number_col">
                <span style="font-size: 24px; font-weight:900"><?=$result->person_number?></span>
            </div>
        </div>

        <div class="row_d">
            <div class="titel_col">
                <label for="">Description:</label>
            </div>
            <div class="right_col">
                <p style="font-size: 15px; font-weight:900"><?=$result->description?></p>
            </div>
        </div>

        <div class="row_d">
            <div class="titel_col">
                <label for="">Add File:</label>
            </div>
            <div class="right_col add__file__container">
                
                <?php
                foreach($files_by_user as $key=>$value)
                {
                    ?>
                     <a href="<?=wp_upload_dir()['baseurl'].$value['paths']?>" target="_blank"><?=$value['name']?></a>
                     <br>
                     <br>
                    <?php

                }
                
                ?>
            </div>
        </div>

        <div class="row_d services_row">
            <div class="titel_col">
                <label for="">Services:</label>
            </div>
            <div class="right_col product__container">
                <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'encoderit_custom_form_services';
        $get_all_services = $wpdb->get_results("SELECT * FROM " . $table_name . "
                where id in $service_ids");
                foreach ($get_all_services as $key => $value) 
                {
                    ?>
                <div class="product__item d-flex-center">
                    <input type="checkbox" class="encoder_it_custom_services" checked disabled/>
                    <label class="d-flex-center">
                        <span><?= $value->service_name ?>...................</span>
                        <span>$<?= $value->service_price ?></span>
                    </label>
                </div>
                <?php
         }

       ?>
            </div>
        </div>

        <div class="row_d">
            <div class="titel_col">
                <label for="">Payment Method:</label>
            </div>
            <div class="right_col right_total_price">
                <div class="payment_method_container">
                    <div class="item d-flex-center">
                        <input type="radio" checked disabled/>
                        <span><?=$result->payment_method?></span>
                    </div>
                </div>
               
                <div class="total__price">
                    <span>Total Price</span><span id="price">$ <?=$result->total_price?></span>
                </div>
                
            </div>
        </div>
        <div class="row_d">
            <div class="titel_col">
            <label for="">Add File:</label>
            </div>
          <div class="right_col add__file__container">
          <?php
              if(!empty($files_by_admin))
              {
                foreach($files_by_admin as $key=>$value)
                {
                    ?>
                     <a href="<?=wp_upload_dir()['baseurl'].$value['paths']?>" target="_blank"><?=$value['name']?></a>
                     <br>
                     <br>
                    <?php

                }
              }
                
            ?>
            <button id="addFile">Add File</button>
            <div id="files"></div>
            <div class="submit_btn">
                    <input class="buttons" id="enoderit_cuctom_form_admin_file_submit" type="submit" name="btn" />
            </div>
        </div>
    </div>
    </form>
</div>

<?php require_once( dirname( __FILE__ ).'/js/add_case_js_file.php' ); ?>
