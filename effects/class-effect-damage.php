<?php

/**
 * Damage effect
 *
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/effects
 */
 
 /**
 * Init effect
 */
function cge_init_damage_effect( $effects ) {
	$effects['damage'] = [ 'id' => 'damage', 'name' => 'Damage', 'class' => 'Cge_Damage_Effect' ];
	return $effects;
}

add_filter( 'cge_effects', 'cge_init_damage_effect' );

 
/**
 * Main game controller class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Damage_Effect extends Cge_Effect {
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		$this->id = 'damage';

	}
	
	public function do_effect( $target, $target_type, $amount, $gamedata ) {
		
		$actions = [];
		
		if ( 'creature' === $target_type  ) {
			
			$amount = apply_filters( 'creature_damage', $amount );
			
			$target['health'] = $target['health'] - $amount;
			
			$actions[] = [ 'target' => $target['target'], 'action' => sprintf( '%s damage', $amount ) ];
		}
		
		if ( $target[ 'health' ] < 0 ) {
			$target['health'] = 0;
			$actions[] = [ 'target' => $target['target'], 'action' => 'died' ];
		}
				
		return [
			'target' => $target,
			'action_log' => $actions
		];
	}
	
}


