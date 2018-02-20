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

	
	public function do_effect( $target, $amount ) {
		
		$actions = [];
		
		$this->game->effect_handler->apply_damage( $target, $amount );
		
		$actions[] = [ 'target' => $target['type'], 'action' => 'damage', 'message' => sprintf( '%s damage', $amount ) ];
			
		return [
			'action_log' => $actions
		];
	}
	
}