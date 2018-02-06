<?php

/**
 * Heal effect
 *
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/effects
 */
 
 /**
 * Init effect
 */
function cge_init_heal_effect( $effects ) {
	$effects['heal'] = [ 'id' => 'heal', 'name' => 'Heal', 'class' => 'Cge_Heal_Effect' ];
	return $effects;
}

add_filter( 'cge_effects', 'cge_init_heal_effect' );

 
/**
 * Main game controller class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Heal_Effect extends Cge_Effect {
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		$this->id = 'heal';

	}
	
	public function do_effect( $target, $target_type, $amount, $gamedata ) {
		
		$actions = [];
		
		if ( 'self' === $target_type  ) {
			
			$amount = apply_filters( 'heal', $amount );

			$gamedata['game_data']['player']['health'] += $amount;

			$actions[] = [ 'target' => $target_type, 'action' => sprintf( 'restored %s life', $amount ) ];
			
		}
		
		if ( $gamedata['game_data']['player']['health'] > $gamedata['game_data']['player']['max_health'] ) {
			$target[ 'health' ] = $target[ 'max_health' ];
		}
				
		return [
			'action_log' => $actions,
			'gamedata' => $gamedata
		];
		
	}
	
}


