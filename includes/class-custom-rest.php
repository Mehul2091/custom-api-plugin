<?php
class CustomRestApi
{
    protected $plugin_name;
    protected $version;
    public function __construct()
    {
        $this->plugin_name = 'custom-auth';
        $this->version = '1.1.0';
        $this->load_dependencies();
        $this->define_public_hooks();
    }

    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)).'public/class-custom-rest-public.php';
    }

    private function define_public_hooks()
    {
        
        $plugin_public = new CustomRestApi_Public($this->get_plugin_name(), $this->get_version());
        add_action('rest_api_init',array($plugin_public,'add_api_routes'));
        
    }


    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

   
    public function get_version()
    {
        return $this->version;
    }
}
