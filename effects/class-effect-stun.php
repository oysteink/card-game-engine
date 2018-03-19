<?php

/**
 * Stun effect
 *
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/effects
 */
 
 /**
 * Init effect
 */
function cge_init_stun_effect( $effects ) {
	$effects['stun'] = [ 'id' => 'stun', 'name' => 'Stun', 'class' => 'Cge_Stun_Effect' ];
	return $effects;
}

add_filter( 'cge_effects', 'cge_init_stun_effect' );

 
/**
 * Stun effect class
 *
 * @package    Cge
 * @subpackage Cge/effects
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Stun_Effect extends Cge_Effect {
	
	public function do_effect( $target, $amount ) {
		
		$actions = [];
		
		$stun = true;

		// Check if we can.. actually stun the target
		$stun = apply_filters( 'stun', $stun, $target );
		
		$this->game->effect_handler->apply_curse( $target, [ 'effect' => 'stun', 'expire' => 'on_start_turn', 'prevent_attack' => true ] );
		
		return [
			'action_log' => $actions
		];
	}
	
}


