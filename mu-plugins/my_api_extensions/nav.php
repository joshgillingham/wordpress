<?php

//Instnatiate api
new My_API_Nav();

class My_API_Nav {
    
    private static $menuName = 'some-menu-name';

    public function __construct() {
        add_action( 'rest_api_init', 'My_API_Nav::register_routes' );            
    }

    /**
    * Register our routes with the wordpress api
    */
    public function register_routes() {
        
        // Navigation
        register_rest_route(Api_Extensions::$namespace, '/navigation', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => 'My_API_Nav::getNavigation'
        ));

        // Posts / Pages
        register_rest_route(Api_Extensions::$namespace, '/page/(?P<id>\d+)', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => 'My_API_Nav::getPageOrPost',
            'args' => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                )
            )
        ));
    }

    /**
    * Get navigation
    */
    public function getNavigation() {
        $menu = array();
        
        try {
            $menu_array = wp_get_nav_menu_items( API_Nav::$menuName );
            
            foreach($menu_array as $item) {
                $menu[] = new My_MenuItem($item);
            }
        }
        catch(Eception $e) {
            error_log($e, 0);
        }

        return new WP_REST_Response($menu);
    }

    /**
    * Get page by id
    */
    public function getPageOrPost( WP_REST_Request $request ) {

        $page;

        try {
            $params = $request->get_url_params();
            
            $object_id = $params['id'];

            $object = get_post($object_id);
            
            $page = new My_Page($object);
        }
        catch(Eception $e) {
            error_log($e, 0);
        }

        return new WP_REST_Response($page);
    }

    /**
    * Get page children nav
    */
    
}

if(!class_exists('My_MenuItem')) {
    class My_MenuItem {
        public $object_id;
        public $object_type;
        public $title;
        public $description;
        public $menu_order;
        public $target;

        function __construct($menu_item) {
            $menu;
            try {
                $this->object_id = $menu_item->object_id;
                $this->object_type = $menu_item->type;
                $this->title = $menu_item->title;
                $this->description = $menu_item->description;
                $this->menu_order = $menu_item->menu_order;
                $this->target = $menu_item->target;
            }
            catch(Eception $e) {
                error_log($e, 0);
            }
            return $menu;
        }
    }
}

if(!class_exists('My_Page')) {
    class My_Page {
        public $id;
        public $published_date;
        public $status;
        public $title;
        public $content;
        public $excerpt;

        function __construct($post_object) {
            $this->id = $post_object->ID;
            $this->published_date = $post_object->post_date;
            $this->status = $post_object->post_status;
            $this->title = $post_object->post_title;
            $this->content = $post_object->post_content;
            $this->excerpt = $post_object->post_excerpt;
        }
    }
}

?>