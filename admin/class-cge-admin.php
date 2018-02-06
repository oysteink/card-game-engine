<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cge
 * @subpackage Cge/admin
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function admin_init( ) {

		$this->init_acf();

	}
	
	/**
	 * Save post function
	 *
	 * @since    1.0.0
	 * @param      int    $post_oid       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function save_post( $post_id, $post ) {
		

		if ( isset( $post->post_type ) ) {

			// We will create a deck data object based on the acf repeater			
			if ( 'cge-deck' === $post->post_type ) {
				
				$card_data = [];
				$cards = get_field( 'cards', $post_id );
				
				if ( is_array( $cards ) ) {

					foreach ( $cards as $card ) {
						$card_data[] = $card['card'];
					}

					update_post_meta( $post_id, 'cge-cards', $card_data );
				}
				

			}

			// We will create a deck data object based on the acf repeater			
			if ( 'cge-card' === $post->post_type ) {
				
				$card_data = [
					'card_id' 		=> $post_id,
					'name' 		=> get_field( 'card_name', $post_id ),
					'cost' 		=> get_field( 'card_cost', $post_id ),
					'type' 		=> get_field( 'card_type', $post_id),
					'target'	=> get_field( 'card_target', $post_id ),
					'text'		=> get_field( 'card_text', $post_id ),
					
				];
				
				update_post_meta( $post_id, 'cge-card-details', $card_data );
				
				$effects = get_field( 'card_effects', $post_id );
				update_post_meta( $post_id, 'cge-card-effects', $effects );

			}
			
		}

	}
	

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cge-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cge-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Init ACF
	 *
	 * We use ACF for editing the cards for now.
	 *
	 * @since    1.0.0
	 */
	public function init_acf() {
		
		// 1. customize ACF path
		add_filter('acf/settings/path', function ( $path ) {
		 
		    // update path
		    $path =  plugin_dir_path( dirname( __FILE__ ) ) . 'admin/lib/acf/';
		    
		    // return
		    return $path;
		    
		} );
		
		// 2. customize ACF dir
		add_filter('acf/settings/dir', 	function ( $dir ) {
		 
		    // update path
		    $dir = plugin_dir_url( __FILE__ ) . '/lib/acf/';
		    
		    // return
		    return $dir;
		    
		} );
		
		// 3. Hide ACF field group menu item
		// add_filter('acf/settings/show_admin', '__return_false');

		include_once( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/lib/acf/acf.php' );

	}

}
