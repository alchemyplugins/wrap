<?php

class AP_Wrap {
    public $cpt = 'ap_wrap3';

    public function init() {
        add_action( 'init', array($this,'register_cpt') );
    }

    public function register_cpt() {
        $labels = array(
            'name'               => _x( 'Wraps', 'post type general name', '<%= pkg.name %>' ),
            'singular_name'      => _x( 'Wrap', 'post type singular name', '<%= pkg.name %>' ),
            'menu_name'          => _x( 'Wraps3', 'admin menu', '<%= pkg.name %>' ),
            'name_admin_bar'     => _x( 'Wrap', 'add new on admin bar', '<%= pkg.name %>' ),
            'add_new'            => _x( 'Add New', 'wrap', '<%= pkg.name %>' ),
            'add_new_item'       => __( 'Add New Wrap', '<%= pkg.name %>' ),
            'new_item'           => __( 'New Wrap', '<%= pkg.name %>' ),
            'edit_item'          => __( 'Edit Wrap', '<%= pkg.name %>' ),
            'view_item'          => __( 'View Wrap', '<%= pkg.name %>' ),
            'all_items'          => __( 'All Wraps', '<%= pkg.name %>' ),
            'search_items'       => __( 'Search Wraps', '<%= pkg.name %>' ),
            'parent_item_colon'  => __( 'Parent Wraps:', '<%= pkg.name %>' ),
            'not_found'          => __( 'No wraps found.', '<%= pkg.name %>' ),
            'not_found_in_trash' => __( 'No wraps found in Trash.', '<%= pkg.name %>' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'author', 'revisions' )
        );

        register_post_type( $this->cpt, $args );
    }
}
