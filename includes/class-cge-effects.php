<?php

/**
 * Effect / Action handler class
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/includes
 */

/**
 * Effect / Action handler class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Effects {
	
	var $effects;
	
	/**
	 * Initialize effects
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	
		/**
		 * Load basic effect class
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cge-effect.php';		
		
		// Include the basic effects
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'effects/class-effect-damage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'effects/class-effect-heal.php';
				
		$this->effects = [];
		$this->effects = apply_filters( 'cge_effects', $this->effects );

	}	
}