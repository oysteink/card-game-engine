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
function cge_init_bleed_effect( $effects ) {
	$effects['crs_bleed'] = [ 'id' => 'crs_bleed', 'name' => 'Bleed', 'class' => 'Cge_Bleed_Effect' ];
	return $effects;
}

add_filter( 'cge_effects', 'cge_init_bleed_effect' );

 
/**
 * Stun effect class
 *
 * @package    Cge
 * @subpackage Cge/effects
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Bleed_Effect extends Cge_Effect {
	
	// This function runs when the card is played
	public function do_effect( $target, $amount ) {
		
		$actions = [];
						
		$this->game->effect_handler->apply_curse(
											$target,
											[ 
												'effect' => 'crs_bleed',
												'amount' => $amount,
												'trigger' => 'enemy_attack',
												'expire' => '',
											]
										);
		
		return [
			'action_log' => $actions
		];
	}
	
	// This function runs when the curse is in effect
	public function trigger_curse( $target, $amount ) {

		$actions = [];
				
		$this->game->effect_handler->apply_damage( $target, $amount );
		
		$this->game->log_action( 'bleed', $target['enemy'], 'success', sprintf( '%s lose %s health to bleed', $target['target_name'], $amount ), $amount );

		return true;		
	}
	
}


