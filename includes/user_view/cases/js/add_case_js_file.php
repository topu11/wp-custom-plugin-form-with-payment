<script src="https://www.paypal.com/sdk/js?client-id=<?=ENCODER_IT_PAYPAL_CLIENT?>&currency=USD&disable-funding=paylater"></script>
<script>
  let total_price = 0;
  let person_number=0;
  let temp_price_on_service_check=0;
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
     
    var check_payment_option_radio=false;
     if(document.getElementById('encoderit_paypal').checked || document.getElementById('encoderit_stripe').checked)
     {
         check_payment_option_radio=true;
     }
    if(document.getElementsByClassName("file_add").length == 0 && check_payment_option_radio)
    {
        location.reload();
    }
  });

  function add_total_price(id) {
     person_number=document.getElementById('person_number').value;
    //console.log(person_number);
    if(!person_number)
    {
      document.getElementById(id).checked = false;   
      swal.fire({text: 'Please select Numer of persons', });
      return ;
    }
    if (document.getElementById(id).checked) {
      temp_price_on_service_check =
      temp_price_on_service_check +
        parseFloat(document.getElementById(id).getAttribute("data-price"));
    } else {
      temp_price_on_service_check =
      temp_price_on_service_check -
        parseFloat(document.getElementById(id).getAttribute("data-price"));
    }
    total_price=person_number*temp_price_on_service_check;
    if(total_price <= 0)
    {
      total_price=0;
      temp_price_on_service_check=0;
    }
    document.getElementById("price").innerText = total_price;
  }

  document.getElementById('person_number').addEventListener('change', function(event) {
             var person_number_on_event = event.target.value;
             if(person_number_on_event <= 0)
             {
                  return ;
             }
            add_total_price_by_persons(person_number_on_event)
        });

  document.getElementById('person_number').addEventListener('input', function(event) {
         var person_number_on_event = event.target.value;
         if(person_number_on_event <= 0)
             {
                  return ;
             }
            add_total_price_by_persons(person_number_on_event)
        });

  function add_total_price_by_persons(number_of_persons)
  {
   
    // var temp_price=0;
    // var custom_services_checked_or_not=document.getElementsByClassName("encoder_it_custom_services");
    //   for(var i=0;i<custom_services_checked_or_not.length;i++)
    // {
    //     if(custom_services_checked_or_not[i].checked)
    //     {
    //       temp_price =
    //       temp_price +
    //          parseFloat(custom_services_checked_or_not[i].getAttribute("data-price"));
    //     }
              
    // }
    total_price=number_of_persons*temp_price_on_service_check;
    if(total_price <= 0)
    {
      total_price=0;
      temp_price_on_service_check=0;
      
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
    var custom_file=document.getElementsByClassName("file_add");
    var file_bug=false;
      for(var i=0;i<custom_file.length;i++)
    {
           if(!custom_file[i].files[0])
           {
             file_bug=true;
             break;
           }
              
    }
    if(total_price == 0 || document.getElementsByClassName("file_add").length == 0 || sumbit_service.length == 0 || person_number == 0 || !description || file_bug)
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
       /*** Show the Submit Button */
       document.getElementById('encoder_it_submit_btn_user_form').removeAttribute("disabled");
    }else if(id=="encoderit_paypal")
    {
      document.getElementById('stripe_payment_div').style.display='none';
      document.getElementById('paypal-button-container').style.display='block';
      /*** hide the Submit Button */
      document.getElementById('encoder_it_submit_btn_user_form').setAttribute("disabled");
    }else
    {
      document.getElementById('stripe_payment_div').style.display='none';
      document.getElementById('paypal-button-container').style.display='none';
       /*** hide the Submit Button */
       document.getElementById('encoder_it_submit_btn_user_form').setAttribute("disabled");
    }
  }

/******* Stripe Sections */
var stripe = Stripe("<?=ENCODER_IT_STRIPE_PK?>");
  var elements = stripe.elements();
  var cardElement = elements.create('card', {
  style: {
    base: {
      iconColor: '#000',
      color: '#3c434a',
      fontWeight: '500',
      fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
      fontSize: '16px',
      fontSmoothing: 'antialiased',
      ':-webkit-autofill': {
        color: '#fce883',
      },
      '::placeholder': {
        color: '#3c434a',
      },
    },
    invalid: {
      iconColor: '#ff000c',
      color: '#ff000c',
    },
      g: {
    fill: '#000',
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
            
            swal.showLoading();

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
      swal.fire({text: 'Please Select Transaction methods', });
     }
     
     
});

</script>