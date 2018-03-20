<?php

/**
 * Ability handler class
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/includes
 */

/**
 * Ability handler class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Abilities {
	
	/**
	 * Initialize effects
	 *
	 * @since    1.0.0
	 */
	public function __construct( Cge_Game $game  ) {
			
		$this->game = $game;

	}
		
	// Check if this card effect is an ability and return ability name
	function is_ability( $ability_name ) {
		if ( strstr( $ability_name, 'ability' ) ) {
			return str_replace( 'ability-', '', $ability_name );
		} else {
			return false;
		}
	}
	
	// Give 
	function give_ability( $target, $ability, $value ) {
		
		if ( 'self' === $target['type'] ) {
			if ( isset( $this->game->gamedata['game_data']['player']['abilities'][ $ability ] ) ) {
				if ( is_numeric( $this->game->gamedata['game_data']['player']['abilities'][ $ability ] ) ) {
					$this->game->gamedata['game_data']['player']['abilities'][ $ability ] += $value;
				} else {
					$this->game->gamedata['game_data']['player']['abilities'][ $ability ] = $value;
				}
			} else {
				$this->game->gamedata['game_data']['player']['abilities'][ $ability ] = $value;
			}
		}
		
	}
	
	function get_ability( $target, $ability ) {

		if ( 'self' === $target['type'] ) {
			if ( isset( $this->game->gamedata['game_data']['player']['abilities'][ $ability ] ) ) {
				return $this->game->gamedata['game_data']['player']['abilities'][ $ability ];
			} else {
				return false;
			}
		}

	}
	
	// Check if attack is evaded
	function evade_attack( $target ) {

		if ( $evade_chance = $this->get_ability( $target, 'evade' ) ) {
			
			$hit = rand( 0, 100 );
			
			if ( $hit >= $evade_chance ) {
				return true;
			}
			
		}
		
		return false;

	}
	
}