<?php

/**
 * Set up game data types
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/includes
 */

/**
 *  Set up game data types
 *
 * Set up post types and taxonomies
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Data_Types {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 * Register post types
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function register_post_types( ) {

	    $labels = array(
	        'name'                  => _x( 'Cards', 'Post type general name', 'cge' ),
	        'singular_name'         => _x( 'Card', 'Post type singular name', 'cge' ),
	        'menu_name'             => _x( 'Cards', 'Admin Menu text', 'cge' ),
	        'name_admin_bar'        => _x( 'Card', 'Add New on Toolbar', 'cge' ),
	        'add_new'               => __( 'Add New', 'cge' ),
	        'add_new_item'          => __( 'Add New Card', 'cge' ),
	        'new_item'              => __( 'New Card', 'cge' ),
	        'edit_item'             => __( 'Edit Card', 'cge' ),
	        'view_item'             => __( 'View Vard', 'cge' ),
	        'all_items'             => __( 'All Cards', 'cge' ),
	        'search_items'          => __( 'Search Cards', 'cge' ),
	        'parent_item_colon'     => __( 'Parent Cards:', 'cge' ),
	        'not_found'             => __( 'No books found.', 'cge' ),
	        'not_found_in_trash'    => __( 'No books found in Trash.', 'cge' ),
	        'featured_image'        => _x( 'Card image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'cge' ),
	        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'remove_featured_image' => _x( 'Remove card image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'use_featured_image'    => _x( 'Use as card image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'archives'              => _x( 'Card archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'cge' ),
	        'insert_into_item'      => _x( 'Insert into card', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'cge' ),
	        'uploaded_to_this_item' => _x( 'Uploaded to this card', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'cge' ),
	        'filter_items_list'     => _x( 'Filter cards list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'cge' ),
	        'items_list_navigation' => _x( 'Cards list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'cge' ),
	        'items_list'            => _x( 'Cards list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'cge' ),
	    );
	 
	    $args = array(
	        'labels'             => $labels,
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
	        'query_var'          => true,
	        'rewrite'            => false,
	        'capability_type'    => 'post',
	        'has_archive'        => false,
	        'hierarchical'       => false,
	        'menu_position'      => null,
	        'supports'           => array( 'title', 'thumbnail', 'excerpt', 'custom-fields' ),
	    );
	 
	    register_post_type( 'cge-card', $args );

	    $labels = array(
	        'name'                  => _x( 'Decks', 'Post type general name', 'cge' ),
	        'singular_name'         => _x( 'Deck', 'Post type singular name', 'cge' ),
	        'menu_name'             => _x( 'Decks', 'Admin Menu text', 'cge' ),
	        'name_admin_bar'        => _x( 'Deck', 'Add New on Toolbar', 'cge' ),
	        'add_new'               => __( 'Add New', 'cge' ),
	        'add_new_item'          => __( 'Add New Deck', 'cge' ),
	        'new_item'              => __( 'New Deck', 'cge' ),
	        'edit_item'             => __( 'Edit Deck', 'cge' ),
	        'view_item'             => __( 'View Deck', 'cge' ),
	        'all_items'             => __( 'All Decks', 'cge' ),
	        'search_items'          => __( 'Search Decks', 'cge' ),
	        'parent_item_colon'     => __( 'Parent Decks:', 'cge' ),
	        'not_found'             => __( 'No decks found.', 'cge' ),
	        'not_found_in_trash'    => __( 'No decks found in Trash.', 'cge' ),
	        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'remove_featured_image' => _x( 'Remove card image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'use_featured_image'    => _x( 'Use as card image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'archives'              => _x( 'Card archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'cge' ),
	        'insert_into_item'      => _x( 'Insert into card', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'cge' ),
	        'uploaded_to_this_item' => _x( 'Uploaded to this card', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'cge' ),
	        'filter_items_list'     => _x( 'Filter cards list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'cge' ),
	        'items_list_navigation' => _x( 'Cards list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'cge' ),
	        'items_list'            => _x( 'Cards list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'cge' ),
	    );
	 
	    $args = array(
	        'labels'             => $labels,
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
	        'query_var'          => true,
	        'rewrite'            => false,
	        'capability_type'    => 'post',
	        'has_archive'        => false,
	        'hierarchical'       => false,
	        'menu_position'      => null,
	        'supports'           => array( 'title', 'excerpt', 'custom-fields' ),
	    );
	 
	    register_post_type( 'cge-deck', $args );

	    $labels = array(
	        'name'                  => _x( 'Items', 'Post type general name', 'cge' ),
	        'singular_name'         => _x( 'Item', 'Post type singular name', 'cge' ),
	        'menu_name'             => _x( 'Items', 'Admin Menu text', 'cge' ),
	        'name_admin_bar'        => _x( 'Item', 'Add New on Toolbar', 'cge' ),
	        'add_new'               => __( 'Add New', 'cge' ),
	        'add_new_item'          => __( 'Add New Item', 'cge' ),
	        'new_item'              => __( 'New Item', 'cge' ),
	        'edit_item'             => __( 'Edit Item', 'cge' ),
	        'view_item'             => __( 'View Item', 'cge' ),
	        'all_items'             => __( 'All Items', 'cge' ),
	        'search_items'          => __( 'Search Items', 'cge' ),
	        'parent_item_colon'     => __( 'Parent Items:', 'cge' ),
	        'not_found'             => __( 'No items found.', 'cge' ),
	        'not_found_in_trash'    => __( 'No items found in Trash.', 'cge' ),
	        'set_featured_image'    => _x( 'Set item image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'remove_featured_image' => _x( 'Remove item image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'use_featured_image'    => _x( 'Use as item image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'archives'              => _x( 'Item archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'cge' ),
	        'insert_into_item'      => _x( 'Insert into item', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'cge' ),
	        'uploaded_to_this_item' => _x( 'Uploaded to this item', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'cge' ),
	        'filter_items_list'     => _x( 'Filter items list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'cge' ),
	        'items_list_navigation' => _x( 'Items list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'cge' ),
	        'items_list'            => _x( 'Items list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'cge' ),
	    );
	 
	    $args = array(
	        'labels'             => $labels,
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
	        'query_var'          => true,
	        'rewrite'            => false,
	        'capability_type'    => 'post',
	        'has_archive'        => false,
	        'hierarchical'       => false,
	        'menu_position'      => null,
	        'supports'           => array( 'title', 'thumbnail' ),
	    );
	 
	    register_post_type( 'cge-item', $args );

	    $labels = array(
	        'name'                  => _x( 'Creatures', 'Post type general name', 'cge' ),
	        'singular_name'         => _x( 'Creature', 'Post type singular name', 'cge' ),
	        'menu_name'             => _x( 'Creatures', 'Admin Menu text', 'cge' ),
	        'name_admin_bar'        => _x( 'Creature', 'Add New on Toolbar', 'cge' ),
	        'add_new'               => __( 'Add New', 'cge' ),
	        'add_new_item'          => __( 'Add New creature', 'cge' ),
	        'new_item'              => __( 'New Creature', 'cge' ),
	        'edit_item'             => __( 'Edit Creature', 'cge' ),
	        'view_item'             => __( 'View Creature', 'cge' ),
	        'all_items'             => __( 'All Creatures', 'cge' ),
	        'search_items'          => __( 'Search Creatures', 'cge' ),
	        'parent_item_colon'     => __( 'Parent Creatures:', 'cge' ),
	        'not_found'             => __( 'No Creatures found.', 'cge' ),
	        'not_found_in_trash'    => __( 'No Creatures found in Trash.', 'cge' ),
	        'set_featured_image'    => _x( 'Set Creatures image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'remove_featured_image' => _x( 'Remove Creature image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'use_featured_image'    => _x( 'Use as Creature image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'archives'              => _x( 'Creature archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'cge' ),
	        'insert_into_item'      => _x( 'Insert into Creatur', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'cge' ),
	        'uploaded_to_this_item' => _x( 'Uploaded to this item', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'cge' ),
	        'filter_items_list'     => _x( 'Filter items list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'cge' ),
	        'items_list_navigation' => _x( 'Items list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'cge' ),
	        'items_list'            => _x( 'Items list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'cge' ),
	    );
	 
	    $args = array(
	        'labels'             => $labels,
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
	        'query_var'          => true,
	        'rewrite'            => false,
	        'capability_type'    => 'post',
	        'has_archive'        => false,
	        'hierarchical'       => false,
	        'menu_position'      => null,
	        'supports'           => array( 'title', 'thumbnail' ),
	    );
	 
	    register_post_type( 'cge-creature', $args );
	    
	    $labels = array(
	        'name'                  => _x( 'Levels', 'Post type general name', 'cge' ),
	        'singular_name'         => _x( 'Level', 'Post type singular name', 'cge' ),
	        'menu_name'             => _x( 'Levels', 'Admin Menu text', 'cge' ),
	        'name_admin_bar'        => _x( 'Level', 'Add New on Toolbar', 'cge' ),
	        'add_new'               => __( 'Add New', 'cge' ),
	        'add_new_item'          => __( 'Add New Level', 'cge' ),
	        'new_item'              => __( 'New Level', 'cge' ),
	        'edit_item'             => __( 'Edit Level', 'cge' ),
	        'view_item'             => __( 'View Level', 'cge' ),
	        'all_items'             => __( 'All Levels', 'cge' ),
	        'search_items'          => __( 'Search Levels', 'cge' ),
	        'parent_item_colon'     => __( 'Parent Levels:', 'cge' ),
	        'not_found'             => __( 'No Levels found.', 'cge' ),
	        'not_found_in_trash'    => __( 'No Levels found in Trash.', 'cge' ),
	        'set_featured_image'    => _x( 'Set Level image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'remove_featured_image' => _x( 'Remove Level image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'use_featured_image'    => _x( 'Use as Level image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'cge' ),
	        'archives'              => _x( 'Level archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'cge' ),
	        'insert_into_item'      => _x( 'Insert into Level', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'cge' ),
	        'uploaded_to_this_item' => _x( 'Uploaded to this item', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'cge' ),
	        'filter_items_list'     => _x( 'Filter items list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'cge' ),
	        'items_list_navigation' => _x( 'Items list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'cge' ),
	        'items_list'            => _x( 'Items list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'cge' ),
	    );
	 
	    $args = array(
	        'labels'             => $labels,
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
	        'query_var'          => true,
	        'rewrite'            => false,
	        'capability_type'    => 'post',
	        'has_archive'        => false,
	        'hierarchical'       => false,
	        'menu_position'      => null,
	        'supports'           => array( 'title', 'thumbnail' ),
	    );
	 
	    register_post_type( 'cge-level', $args );	    
	    

		// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Item type', 'taxonomy general name', 'cge' ),
		'singular_name'     => _x( 'Item type', 'taxonomy singular name', 'cge' ),
		'search_items'      => __( 'Search Items', 'cge' ),
		'all_items'         => __( 'All item types', 'cge' ),
		'parent_item'       => __( 'Parent Items', 'cge' ),
		'parent_item_colon' => __( 'Parent Items:', 'cge' ),
		'edit_item'         => __( 'Edit Item', 'cge' ),
		'update_item'       => __( 'Update Item', 'cge' ),
		'add_new_item'      => __( 'Add New Item type', 'cge' ),
		'new_item_name'     => __( 'New Item type', 'cge' ),
		'menu_name'         => __( 'Item type', 'cge' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'gem' ),
	);

	register_taxonomy( 'cge-item-type', array( 'cge-item' ), $args );

		// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Classes', 'taxonomy general name', 'cge' ),
		'singular_name'     => _x( 'Class', 'taxonomy singular name', 'cge' ),
		'search_items'      => __( 'Search classes', 'cge' ),
		'all_items'         => __( 'All Classes', 'cge' ),
		'parent_item'       => __( 'Parent Class', 'cge' ),
		'parent_item_colon' => __( 'Parent Class:', 'cge' ),
		'edit_item'         => __( 'Edit Class', 'cge' ),
		'update_item'       => __( 'Update Gem', 'cge' ),
		'add_new_item'      => __( 'Add New Class', 'cge' ),
		'new_item_name'     => __( 'New Class', 'cge' ),
		'menu_name'         => __( 'Class', 'cge' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'class' ),
	);

	register_taxonomy( 'cge-class', array( 'cge-card', 'cge-deck' ), $args );


	}
 
	
}
