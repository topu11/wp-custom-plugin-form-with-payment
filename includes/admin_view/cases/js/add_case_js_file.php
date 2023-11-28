<script>
  
  jQuery(document).ready(function () {
    jQuery("#addFile").on("click", function (e) {
      e.preventDefault();
      var newInput =
        '<div class="file_item"><input type="file" class="file_add" name="files[]" multiple><button class="removefile">X</button><div>';
      jQuery("#files").append(newInput);
    });
  });
  jQuery(document).on("click", ".removefile", function (e) {
    e.preventDefault();

    jQuery(this).closest("div").remove(); // to get clicked element
  });

 jQuery('#enoderit_cuctom_form_admin_file_submit').on('click',function(e)
    {
      e.preventDefault();
      var formdata = new FormData();
      var custom_file=document.getElementsByClassName("file_add");
      if(custom_file.length == 0)
      {
        swal.fire({
                text: "Please Add files",
              });
               
        return false; 
      }
      for(var i=0;i<custom_file.length;i++)
      {

              formdata.append('file_array[]', custom_file[i].files[0]);

      }
      formdata.append('form_id',<?=$id?>);
      formdata.append('action','enoderit_custom_form_admin_submit');
      formdata.append('nonce','<?php echo wp_create_nonce('admin_ajax_nonce_encoderit_custom_form') ?>')
      jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'post',
        processData: false,
        contentType: false,
        processData: false,
        data: formdata,
        success: function(data) {
                      swal.hideLoading()
                      const obj = JSON.parse(data);
                      console.log(obj);

                        if (obj.success == "success") {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'success',
                                text: 'Save Successfully',
                                showConfirmButton: false,
                                timer: 2500
                            })
                            window.location.href='<?=admin_url() .'/admin.php.?page=encoderit-custom-cases-user'?>'
                        }
                        if(obj.success == "error")
                        {
                          let message_arr=obj.message.split(';')
                          let html='';
                          for(let index=0;index<message_arr.length;index++)
                          {
                               var temp=message_arr[index]+"\n";
                               html = html+temp;
                          }
                          swal.fire({
                            

                            html: html,
                        
                           });
                        }
                    }
             })

    })
</script>