<?php

/**
 * Evade effect
 *
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/effects
 */
 
 /**
 * Init effect
 */
function cge_init_evade_effect( $effects ) {
	$effects['evade'] = [ 'id' => 'evade', 'name' => 'Evade', 'class' => 'Cge_Evade_Effect' ];
	return $effects;
}

add_filter( 'cge_effects', 'cge_init_evade_effect' );

 
/**
 * Evade effect class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Evade_Effect extends Cge_Effect {
	
	public function do_effect( $target, $amount ) {
		
		$actions = [];
			
		// $amount = apply_filters( 'evade', $amount );
		$actions[] = [ 'target' => $target['type'], 'action' => sprintf( 'attack evaded', $amount ) ];
						
		return [
			'action_log' => $actions
		];
		
	}
	
}