<?php

/**
 * Base effect class
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/includes
 */

/**
 * Main game controller class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Effect {
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {


	}	
	
	// Helper function for reducing life on player og target
	function reduce_health( $target, $amount, $gamedata ) {
		
	}
	
		// Helper function for reducing life on player og target
	function restore_health( $target, $amount, $gamedata ) {

		if ( 'self' === $target ) {

			$gamedata['game_data']['player']['health'] += $amount;
			
			if ( $gamedata['game_data']['player']['health'] > $gamedata['game_data']['player']['max_health'] ) {
				$gamedata['game_data']['player']['health'] = $gamedata['game_data']['player']['max_health'];
			}

		}
				
		return $gamedata;
		
	}
	
	function add_buff( $target, $buff, $gamedata ) {
		
	}

	function remove_buff( $target, $buff, $gamedata ) {
		
	}

	
}