<?php

/**
 * Effect / Action handler class
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/includes
 */

/**
 * Effect / Action handler class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Effects {
	
	var $effects;
	
	/**
	 * Initialize effects
	 *
	 * @since    1.0.0
	 */
	public function __construct( Cge_Game $game  ) {
	
		/**
		 * Load basic effect class
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cge-effect.php';	
		
		// Include the basic effects
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'effects/class-effect-damage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'effects/class-effect-heal.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'effects/class-effect-weaken.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'effects/class-effect-stun.php';
				
		$this->effects = [];
		$this->effects = apply_filters( 'cge_effects', $this->effects );
		$this->game = $game;

	}
	
	// Helper function for reducing life on player og target
	function restore_health( $target, $amount ) {
		
		if ( 'self' === $target['type'] ) {

			$this->game->gamedata['game_data']['player']['health'] += $amount;
			
			if ( $this->game->gamedata['game_data']['player']['health'] > $this->game->gamedata['game_data']['player']['max_health'] ) {
				$this->game->gamedata['game_data']['player']['health'] = $this->game->gamedata['game_data']['player']['max_health'];
			}

		}
		
	}
	
	function apply_damage( $target, $amount ) {
		
		if ( 'self' === $target['type'] ) {

			$this->game->gamedata['game_data']['player']['health'] -= $amount;

		}
		
		if ( 'enemy' === $target['type'] ) {
			
			foreach ( $this->game->gamedata['game_data']['enemy']['enemies'] as $index => $enemy ) {
				
				// LOL
				if ( $enemy['target'] == $target['enemy'] ) {
					$this->game->gamedata['game_data']['enemy']['enemies'][$index]['health'] -= $amount;
				}
			}
		}
		
	}
	
	function apply_curse( $target, $curse ) {
		
		if ( 'self' === $target['type'] ) {
			// Can you curse yourself?
		}
		
		if ( 'enemy' === $target['type'] ) {

			foreach ( $this->game->gamedata['game_data']['enemy']['enemies'] as $index => $enemy ) {
				
				// LOL
				if ( $enemy['target'] == $target['enemy'] ) {
					$this->game->gamedata['game_data']['enemy']['enemies'][$index]['curses'][] = $curse;
				}
			}
		}		
		
	}
	
	function reduce_health( $target, $amount ) {

		if ( 'enemy' === $target['type'] ) {
			
			foreach ( $this->game->gamedata['game_data']['enemy']['enemies'] as $index => $enemy ) {
				
				// LOL
				if ( $enemy['target'] == $target['enemy'] ) {
					$this->game->gamedata['game_data']['enemy']['enemies'][$index]['health'] -= $amount;
					$this->game->gamedata['game_data']['enemy']['enemies'][$index]['max_health'] -= $amount;
				}
			}
		}
		
	}
	
	function reduce_attack( $target, $amount ) {

		if ( 'enemy' === $target['type'] ) {
			
			foreach ( $this->game->gamedata['game_data']['enemy']['enemies'] as $index => $enemy ) {
				
				// LOL
				if ( $enemy['target'] == $target['enemy'] ) {
					$this->game->gamedata['game_data']['enemy']['enemies'][$index]['attack'] -= $amount;
				}
			}
		}
		
	}
	
	function check_curse( $target, $effect ) {
		
		if ( isset( $target['curse'] ) && is_array( $target['curse'] ) ) {
			
			foreach ( $target['curse'] as $curse ) {
				if ( $curse[ $curse ] ) {
					return $curse[ $effect ];
				}
			}
			
		}
		
		return false;
 		
	}
	
}