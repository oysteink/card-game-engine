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
	
	public function do_effect( $target, $amount ) {
		
		$actions = [];
			
		$amount = apply_filters( 'heal', $amount );

		$this->game->effect_handler->restore_health( $target, $amount );
		
		$actions[] = [ 'target' => $target['type'], 'action' => sprintf( 'restored %s life', $amount ) ];
						
		return [
			'action_log' => $actions
		];
		
	}
	
}


