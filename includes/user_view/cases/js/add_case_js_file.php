<?php
?>
 <script src="https://www.paypal.com/sdk/js?client-id=<?=ENCODER_IT_PAYPAL_CLIENT?>"></script>
<script>
  let total_price = 0;
  let payment_method='';
  let paypal_tansaction_id='';
  let paypal_transaction_status='';
  let paypal_transaction_name='';
  let paypal_transaction_details='';

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

  function add_total_price(id) {
    if (document.getElementById(id).checked) {
      total_price =
        total_price +
        parseFloat(document.getElementById(id).getAttribute("data-price"));
    } else {
      total_price =
        total_price -
        parseFloat(document.getElementById(id).getAttribute("data-price"));
    }
    document.getElementById("price").innerText = total_price;
  }
  function check_radio_payment_method(id)
  {
    document.getElementById('stripe_payment_div').style.display='none';
    document.getElementById('paypal-button-container').style.display='none'; 

    var description=document.getElementById('description').value;
    var person_number=document.getElementById('person_number').value;
    var service=document.getElementsByClassName("encoder_it_custom_services");
    var sumbit_service=[];
    for(var i=0;i<service.length;i++)
    {
            if(service[i].checked)
            {
                sumbit_service.push(service[i].value)
            }
    }

    if(total_price == 0 || document.getElementsByClassName("file_add").length == 0 || sumbit_service.length == 0 || person_number == 0 || !description)
    {
       swal.fire({
                text: "please provide all information",
              });
              document.getElementById(id).checked = false;      
        return false;
         
    }
    // if(document.getElementsByClassName("file_add").length < sumbit_service.length )
    // {
    //   swal.fire({
    //             text: "You provide lower number of file than service ",
    //           });
    //           document.getElementById(id).checked = false;      
    //     return false;
    // }
    payment_method=document.getElementById(id).value;
    if(id == "encoderit_stripe")
    {
       document.getElementById('stripe_payment_div').style.display='block';
       document.getElementById('paypal-button-container').style.display='none';
    }else if(id=="encoderit_paypal")
    {
      document.getElementById('stripe_payment_div').style.display='none';
      document.getElementById('paypal-button-container').style.display='block';
    }else
    {
      document.getElementById('stripe_payment_div').style.display='none';
      document.getElementById('paypal-button-container').style.display='none';
    }
  }

/******* Stripe Sections */
var stripe = Stripe("<?=ENCODER_IT_STRIPE_PK?>");
  var elements = stripe.elements();
  var cardElement = elements.create('card', {
  style: {
    base: {
      iconColor: '#c4f0ff',
      color: '#00000',
      fontWeight: '500',
      fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
      fontSize: '16px',
      fontSmoothing: 'antialiased',
      ':-webkit-autofill': {
        color: '#fce883',
      },
      '::placeholder': {
        color: '#87BBFD',
      },
    },
    invalid: {
      iconColor: '#FFC7EE',
      color: '#FFC7EE',
    },
  },
});
  
  // Mount the Card Element to the DOM
  cardElement.mount('#card-element');
  
  /******* Stripe Sections end */

/********** Pay Pal Start Here ******* */
paypal.Buttons({
          createOrder: function(data, actions) {
              return actions.order.create({
                  purchase_units: [{
                      amount: {
                          value: total_price,
                          currency_code: 'USD',
                      }
                  }]
              });
          },
          onApprove: function(data, actions) {
              return actions.order.capture().then(function(details) {
                  //const result=JSON.stringify(details,null,2);
                 // console.log(details.purchase_units[0].payments.captures[0].id , details.purchase_units[0].payments.captures[0].status);
                  let paypal_tansaction_id=details.purchase_units[0].payments.captures[0].id;
                  let paypal_transaction_status=details.purchase_units[0].payments.captures[0].status;
                  let paypal_transaction_name=details.payer.name.given_name;
                  if(paypal_transaction_status == "COMPLETED")
                  {
                    swal.showLoading();
                    var service=document.getElementsByClassName("encoder_it_custom_services");
                    var sumbit_service=[];
                    var sumbit_file=[];
                    
                    
                    for(var i=0;i<service.length;i++)
                    {
                      if(service[i].checked)
                      {
                        sumbit_service.push(service[i].value)
                        }
                    }
                    var description=document.getElementById('description').value;
                    var person_number=document.getElementById('person_number').value;
            
            var formdata = new FormData();
            formdata.append('paymentMethodId',paypal_tansaction_id);
            formdata.append('sumbit_service',sumbit_service);
            formdata.append('description',description);
            formdata.append('person_number',person_number);
            var custom_file=document.getElementsByClassName("file_add");
            for(var i=0;i<custom_file.length;i++)
            {

              formdata.append('file_array[]', custom_file[i].files[0]);

            }
            formdata.append('total_price',total_price);
            formdata.append('payment_method',payment_method);
            formdata.append('paymentMethodId',paypal_tansaction_id);
            formdata.append('paypal_transaction_name',paypal_transaction_name);
            formdata.append('action','enoderit_custom_form_submit');
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
                            location.reload();
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
                      });
                  }
              });
          },
          onError: function(err) {
              console.error('Error:', err);
              alert('Can Not Pay Zero')
          }
      }).render('#paypal-button-container');





/********** Pay Pal END Here ******* */




var form = document.getElementById('fileUploadForm');
  form.addEventListener('submit', function(event) {
    event.preventDefault();
   
    var service=document.getElementsByClassName("encoder_it_custom_services");
    var sumbit_service=[];
    var sumbit_file=[];
    
    
    for(var i=0;i<service.length;i++)
    {
      if(service[i].checked)
       {
        sumbit_service.push(service[i].value)
        }
    }
    var description=document.getElementById('description').value;
    var person_number=document.getElementById('person_number').value;


     if(payment_method == "Credit Card"){
      stripe.createPaymentMethod({
      type: 'card',
      card: cardElement,
      billing_details: {
           name: '<?=wp_get_current_user()->display_name?>',
           email:'<?=wp_get_current_user()->user_email?>',
          },
        }).then(function(result) {
          if (result.error) {
            // Display error to your user
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
          } else {
            var formdata = new FormData();
            formdata.append('paymentMethodId',result.paymentMethod.id);
            formdata.append('sumbit_service',sumbit_service);
            formdata.append('description',description);
            formdata.append('person_number',person_number);
            var custom_file=document.getElementsByClassName("file_add");
            for(var i=0;i<custom_file.length;i++)
            {

              formdata.append('file_array[]', custom_file[i].files[0]);

            }
            formdata.append('total_price',total_price);
            formdata.append('payment_method',payment_method);
            formdata.append('action','enoderit_custom_form_submit');
            formdata.append('nonce','<?php echo wp_create_nonce('admin_ajax_nonce_encoderit_custom_form') ?>')
            jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    processData: false,
                    data: formdata,
                    success: function(data) {
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
                            location.reload();
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
                            

                            text: html,
                        
                           });
                        }
                    }
            });
          }
        });
     }
     else
     {
      swal.fire({text: 'Transaction Error', });
     }
     
     
});

</script>