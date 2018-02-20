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
function cge_init_weaken_effect( $effects ) {
	$effects['weaken'] = [ 'id' => 'weaken', 'name' => 'Weaken (-n/-n)', 'class' => 'Cge_Weaken_Effect' ];
	return $effects;
}

add_filter( 'cge_effects', 'cge_init_weaken_effect' );

 
/**
 * Main game controller class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Weaken_Effect extends Cge_Effect {

	
	public function do_effect( $target, $amount ) {

		$actions = [];
		
		$amount = apply_filters( 'weaken', $amount );
	
		$this->game->effect_handler->reduce_health( $target, $amount );
		$this->game->effect_handler->reduce_attack( $target, $amount );
		
		$actions[] = [ 'target' => $target['target'], 'action' => 'weaken', 'message' => sprintf( 'Weakened by -%s/-%s', $amount, $amount ) ];
				
		return [
			'action_log' => $actions
		];
	}
	
}


