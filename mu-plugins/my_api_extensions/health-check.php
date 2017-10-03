<?php

new My_API_HealthCheck();

class My_API_HealthCheck {
    
    public function __construct() {
        add_action( 'rest_api_init', 'My_API_HealthCheck::register_routes' );            
    }

    /**
    * Register our routes with the wordpress api
    */
    public function register_routes() {

        register_rest_route(Api_Extensions::$namespace, '/health-check', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => 'My_API_HealthCheck::health_check',
        ));
    }

    /**
    * Callback function for an update check
    */
    public function health_check() {
        include_once "./wp-admin/includes/update.php";
        
        $cur = get_preferred_from_update_core();
        if( ! is_object($cur) )
            $cur = new stdClass;

        if(! isset($cur->current) )
            $cur->current = '';

        if(! isset( $cur->url))
            $cur->url = '';

        if(! isset($cur->response))
            $cur->response = '';
        
        global $wp_version;

        $health_check = new My_HealthCheck();
        $s_results = array();
        //message defaults
        $text = "Unknown Status";
        $warning = 0;
        $error = 1;

        //return $cur;
        //What to do
        switch($cur->response) {

            case 'latest' :
                array_push($s_results, new My_HealthCheck_Result('Latest Version', true));
                $error = 0;
                $warning = 0;
                $text = sprintf( __( 'Installed WP version %s' ), get_bloginfo( 'version', 'display' ));
                break;
            case '' :
                array_push($s_results, new My_HealthCheck_Result('Latest Version', false));
                $text = "Unable to fetch WP version information.";
                $warning = 1;
                $error = 0;
                break;
            default:
                array_push($s_results, new My_HealthCheck_Result('Latest Version', false));
                $text = sprintf( __( 'Installed WP version %s. Update to WP version %s ASAP.' ), get_bloginfo( 'version', 'display' ), sprintf( __( 'Get Version %s' ), $cur->current ));
                $error = 1;
                $warning = 0;
        }

        $health_check->prtg = new My_HealthCheck_PRTG($s_results, $text, $error, $warning);
        return $health_check;
    }
}

if(!class_exists('My_HealthCheck')) {
    /**
    * HealthCheck class for current wordpress version status info
    */
    class My_HealthCheck {
        public $prtg;       
    }
    class My_HealthCheck_PRTG {
        //public $result;
        public $text;
        public $error;
        public $warning;
        function __construct($result, $text, $error, $warning) {
            $this->result = $result;
            $this->text = $text;
            $this->error = $error;
            $this->warning = $warning;
        }
    }
    class My_HealthCheck_Result {
        public $channel;
        public $value;
        function __construct($channel, $latestVerson) {
            $this->channel = $channel;
            $this->value = ($latestVerson == false ? 0 : 1);
        }
    }
}
?>