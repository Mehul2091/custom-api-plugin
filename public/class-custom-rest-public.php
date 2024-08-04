<?php

class CustomRestApi_Public
{
    
    private $plugin_name;
    private $version;
    private $namespace;
    private $wpdb_prefix;
    private $result;
    public function __construct($plugin_name, $version)
    {
        
        global $wpdb;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->namespace = $this->plugin_name.'/v'.intval($this->version);
        $this->wpdb = $wpdb; 
        $this->wpdb_prefix = $wpdb->prefix; 
        $this->result = [
            "success"=>false,
            "statusCode"=>400,
            "message"=>"",
            "data"=>(object)[]
        ];

    }

    /**
     * Add the endpoints to the API
     */
    public function add_api_routes()
    {

        register_rest_route($this->namespace, 'login', [
            'methods' => 'POST',
            'callback' => array($this, 'login_call_back'),            
            'args' => array(
                'username' => array(
                    'required' => true
                ),
                'password' => array(
                    'required' => true
                ),
            )
        ]); 


        register_rest_route($this->namespace, 'submit_data', [
            'methods' => 'POST',
            'callback' => array($this, 'submit_data_back'),            
            'args' => array(
                'first_name' => array(
                    'required' => true
                ),
                'last_name' => array(
                    'required' => true
                ),
                'email' => array(
                    'required' => true
                ),
                'mobile' => array(
                    'required' => true
                )
            )
        ]); 

       
        register_rest_route($this->namespace, 'get_data', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_data_call_back'),
        ));

        
    }

    public function login_call_back($request){
        $data=array('username'=> $request->get_param( 'username' ),'password'=>$request->get_param( 'password' ));
        $response = wp_remote_post( get_site_url().'/wp-json/jwt-auth/v1/token', array(
            'body'    => $data
        ) );
        $data=json_decode($response['body']);
        unset($data->code);
        return $data;
    }

    public function submit_data_back($request){
        
        $result=$this->result;
        $authorization=$request->get_header( 'authorization' );
        $response = wp_remote_post( get_site_url().'/wp-json/jwt-auth/v1/token/validate', array(
            'headers' => array(
                'Authorization' => $authorization,
            ),
        ) );
        
        $data=json_decode($response['body']);
        if($data->success){

            $email=$request->get_param('email');
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {   
                $result['message']='Please enter valid email.';
                return $result;
            }

            $mobile=$request->get_param('mobile');
            $phone_pattern='/^\d{10}$/';
            if(!preg_match($phone_pattern, $mobile))
            {   
                $result['message']='Please enter valid phone number.';
                return $result;
            }

            $table_name = $this->wpdb->prefix . 'user_data';
            $first_name = $request->get_param('first_name');
            $last_name = $request->get_param('last_name');
            $email = $request->get_param('email');
            $mobile = $request->get_param('mobile');
            
            $data = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $mobile,
                'created_at' => current_time('mysql')
            );
            $format = array(
                '%s', 
                '%s', 
                '%s', 
                '%s', 
                '%s'  
            );

            $this->wpdb->insert($table_name, $data, $format);

            // Check if the insert was successful
            if($this->wpdb->insert_id) {
                $data['id']=$this->wpdb->insert_id;
                $result['success']=true;
                $result['message']='Data inserted successfully';
                $result['statusCode']=200;
                $result['data']=$data;
            } else {
                $result['message']='Failed to insert data.';
            }

        }else{
            unset($data->code);
            $data->message='Authorization failed';
            $result=$data;
        }

        return $result;

    }

    private function validateToken($authorization){
        
        $response = wp_remote_post( get_site_url().'/wp-json/jwt-auth/v1/token/validate', array(
            'headers' => array(
                'Authorization' => $authorization,
            ),
        ) );
        $data=json_decode($response['body']);
        return $data;
    }


    public function get_data_call_back($request){

            $result=$this->result;
            $authorization=$request->get_header('authorization');
            $data=$this->validateToken($authorization);
            if($data->success){

                $table_name = $this->wpdb->prefix . 'user_data';
                $sql = "SELECT * FROM $table_name";
                // Execute the query and get the results
                $results = $this->wpdb->get_results($sql, ARRAY_A);
                // Check if there are any results
                if (!empty($results)) {
                    $result['success']=true;
                    $result['message']='Data recieved successfully';
                    $result['statusCode']=200;
                    $result['data']=$results;
                }
            }else{
                unset($data->code);
                $data->message='Authorization failed';
                $result=$data;
            }

            

        return $result;
    }
    
}
