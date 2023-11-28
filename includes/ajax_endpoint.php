<?php
require_once( WP_PLUGIN_DIR . '/SuncodeIT-Custom-Form'.'/stripe-php-library/init.php' );
 \Stripe\Stripe::setApiKey(ENCODER_IT_STRIPE_SK);

class encoderit_ajax_endpoints
{
    public static function enoderit_custom_form_submit()
    {
         
        //var_dump($_POST);
        //exit;
        $erros=self::check_validation_data_request();

        if (!wp_verify_nonce($_POST['nonce'], 'admin_ajax_nonce_encoderit_custom_form')) {
            echo json_encode([
                'success' => 'error',
                'message'=>'Invalid Nonce field'
            ]);
        }elseif(!empty($erros))
        {
            echo $erros;
        }
        else
        {
            if($_POST['payment_method']=="Paypal")
            {
                $is_payment_success=self::encode_it_paypal_payment();
                if(!empty($is_payment_success))
                {
                    echo $is_payment_success;
                } 
            }else
            {
                $encode_it_stripe_payment_return=self::encode_it_stripe_payment();
                $decoded_stripe=json_decode($encode_it_stripe_payment_return,true);
                if($decoded_stripe['status'] == "success")
                {
                    $json_files_by_user=self::save_files_by_user();
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'encoderit_custom_form';
                
                    $data = array(
                        'user_id' => wp_get_current_user()->id,
                        'person_number' => $_POST['person_number'],
                        'description' => $_POST['description'],
                        'services' => $_POST['sumbit_service'],
                        'files_by_user' => $json_files_by_user,
                        'payment_method' => $_POST['payment_method'],
                        'transaction_number' => $_POST['paymentMethodId'],
                        'total_price' => $_POST['total_price'],
                        'created_at' => date('Y-m-d H:i:s'),
                    );
                    
                    $inserted=$wpdb->insert($table_name, $data);
                    if($inserted)
                    {
                        self::send_mail();
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
                }else
                {
                    echo  json_encode([
                        'success' => 'error',
                        'message'=>$decoded_stripe['message'].';'
                    ]);
                }
            }

        } 
        wp_die();
    }
    public static function check_validation_data_request()
    {
        $message='';
         if(empty($_FILES))
         {
            $message .='Please Input files.;';
         }
         if(empty($_POST['total_price']))
         {
            $message .='No Service Selected.;';
         }
         if(empty($_POST['payment_method']))
         {
            $message .='Please Select Payment Method.;';
         }
         if(!empty($message))
         {
            return json_encode([
                'success' => 'error',
                'message'=>$message
            ]);
         }else
         {
            return;
         }
    }
    public static function save_files_by_user()
    {
        
        $file_paths=[];
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
    public static function encode_it_stripe_payment()
    {
        try {
            // Create a PaymentIntent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $_POST['total_price']* 100, // Replace with your actual amount
                'currency' => 'usd',
                'payment_method' => $_POST['paymentMethodId'],
                //'confirmation_method' => 'manual',
                'confirm' => true,
                'metadata' => [
                    'email' => wp_get_current_user()->user_email,
                    'name'=>wp_get_current_user()->display_name,
                ],
                'automatic_payment_methods'=>[
                    'enabled'=>true,
                    'allow_redirects'=>'never'
                ]
            ]);
           
            // Confirm the PaymentIntent
            // if ($paymentIntent->status === 'requires_action' ||
            //     $paymentIntent->status === 'requires_source_action') {
            //     // Card action required
            //     $confirmation = \Stripe\PaymentIntent::confirm($paymentIntent->id);
            // }
    
            // Handle the success or failure of the payment
            if ($paymentIntent->status === 'succeeded') {
                // Payment succeeded
                return json_encode(['status' => 'success', 'message' => 'Payment succeeded!']);
            } else {
                // Payment failed
                return json_encode(['status' => 'failure', 'message' => 'Payment failed.']);
            }
        } catch (\Exception $e) {
            // Handle errors
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

          
    }
    public static function encode_it_paypal_payment()
    {
        $json_files_by_user=self::save_files_by_user();
        global $wpdb;
        $table_name = $wpdb->prefix . 'encoderit_custom_form';
    
        $data = array(
            'user_id' => wp_get_current_user()->id,
            'person_number' => $_POST['person_number'],
            'description' => $_POST['description'],
            'services' => $_POST['sumbit_service'],
            'files_by_user' => $json_files_by_user,
            'payment_method' => $_POST['payment_method'],
            'transaction_number' => $_POST['paymentMethodId'],
            'total_price' => $_POST['total_price'],
            'created_at' => date('Y-m-d H:i:s'),
        );
        
        $inserted=$wpdb->insert($table_name, $data);
        if($inserted)
        {
            self::send_mail();
            return  json_encode([
                'success' => 'success',
                'message'=>'Form Submmited Successfully'
            ]);
        }else
        {
            return  json_encode([
                'success' => 'error',
                'message'=>'Something worng.;'
            ]);
        }
       
    }
    public static function send_mail()
    {
        $to = get_option('admin_email');

		$subject = 'New Case Form Submission ' . ' (' . wp_get_current_user()->display_name . ')';

		$message = '<p> Contact Name: ' . wp_get_current_user()->display_name . '</p>';

		$message .= '<p> Contact Email: ' . wp_get_current_user()->user_email . '</p>';
		$message .= '<p> Payment_method: ' . $_POST['payment_method'] . '</p>';
		$message .= '<p> Transaction Number: ' . $_POST['paymentMethodId'] . '</p>';
        $message .= '<p> Total Price: ' . $_POST['total_price'] . '</p>';

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		wp_mail($to, $subject, $message, $headers);
    }    
}