<?php

/**
 * The main game controller
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/includes
 */

/**
 * Main game controller class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Game {
	
	var $gamedata;
	
	var $effects;
	
	var $database;
	
	var $action_log;
	
	var $settings;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $game_id = false ) {

		// Initiate database functions
		$this->database = new Cge_Database();

		// $this->settings = [ 'starting_mana' => 5, 'starting_cards' => 4, 'starting_health' => 30 ];

		if ( $game_id ) {
			// Get game state from id
			$this->gamedata = $this->load_gamestate( $game_id );
		} else {
			$game_id = uniqid();
			$this->gamedata = $this->create_new_game( $game_id );
		}
		
		$this->game_id = $game_id;

		// Log actions for frontend animations
		$this->action_log = []; 
		
		// Debug log is attached to the game object
		$this->debug = [];

		// Load effects		
		$this->effect_handler = new Cge_Effects( $this );
		$this->effects = $this->effect_handler->effects;

		// Load effects from cards in play
		$this->load_active_effects();
		
		// Load curses and buffs from enemies
		// $this->load_active_curses();
		
	}
	
	function get_gamestate() {
		$this->save_gamestate();
		
		// Remove data we don't want to reveal through the API
		$this->gamedata['game_data']['player']['draw_pile']['cards'] = [];
		$this->gamedata['game_data']['player']['discard_pile']['cards'] = [];
		$this->gamedata['game_data']['player']['deck']['cards'] = [];
		
		// Attach debug
		$this->gamedata['debug'] = $this->debug;
		
		if ( count( $this->action_log ) > 0 ) {
			$this->gamedata['action']['action_log'] = $this->action_log;	
		}
		
		return $this->gamedata;
	}
	
	function create_new_game( $id ) {
		
		$game_settings = $this->database->get_game_settings();
		
		$enemies = $this->database->get_level_enemies( 1 );
		
		$gamedata = [
			'game_id' => $id,
			'level' => 1,
			'turn' => 0,
			'status' => 'running',
			'settings' => $game_settings,
			'player' => [
				'class' => '',
				'deck' => [],
				'hand' => [ 
					'count' => 0,
					'cards' => []
				 ],
				'draw_pile' => [
					'count' => 0,
					'cards' => []
				],
				'discard_pile' => [
					'count' => 0,
					'cards' => []
				],
				'health' => $game_settings['starting_health'],
				'max_health' => $game_settings['starting_health'],
				'mana' => $game_settings['starting_mana'],
				'target' => 'self',
				'in_play' => [],
				'equipped' => [],
				'inventory' => [
				],
			],
			'enemy' => [
				'enemies' => $enemies
			]
		 ];
		 
		 $action = [
			 'message' => 'Select class',
			 'options' => $this->database->get_classes()
		 ];
		 
		 return [
			'current_state' => 'class_select',
			'action' => $action,
			'game_data' => $gamedata
		 ];
	}
	
	
	function player_response( $response ) {
		
		if ( isset( $this->gamedata['current_state'] ) ) {
			
			switch ( $this->gamedata['current_state'] ) {
				case 'class_select';
					if ( $this->select_class( $response ) ) {
						$this->set_current_state( 'player_turn' );
						$this->next_turn();					
					} else {
						return new WP_Error( 'wrong_class', __( 'No such class.', 'cge' ) );
					}
					break;
			}
			
		} else {
			return $this->return_error( 'Wrong game state' );
		}
		
	}
	
	// This function checks for active cards/artifacts and initialize their effect
	function load_active_effects() {
		
		$this->active_effects = [];
		
		
		$in_play = $this->gamedata['game_data']['player']['in_play'];
		
		foreach ( $in_play as $card ) {
			
			$card_effects = $this->database->get_card_effects( $card['card_id'] );
			
			foreach ( $card_effects as $effect ) {
								
				$this->active_effects[ $effect['effect_trigger'] ][] = $effect;
				
			}
			
		}
		
		$this->debug = $this->active_effects;
		
	}
	
	// Trigger effects for this action
	function trigger_actions( $trigger ) {
				
		// Expire curses
		$this->effect_handler->expire_curses( $trigger );
				
		if ( ! empty( $this->active_effects[ $trigger ] ) ) {

			foreach ( $this->active_effects[ $trigger ] as $effect ) {
			
				if ( isset( $this->effects[ $effect['effect'] ] ) )	{
					
					$effect_class_name = $this->effects[ $effect['effect'] ]['class'];
					$effect_class = new $effect_class_name( $this );
					
					$response = $effect_class->do_effect( [ 'type' => $effect['target'] ], $effect['effect_strength'] );
					
					if ( ! empty( $response['action_log'] ) ) {
						$this->action_log = array_merge( $this->action_log, $response['action_log'] );
					}
						
				}
	
			}
			
		}
				
	}
	
	// Increase turn counter
	function next_turn() {
		$this->gamedata['game_data']['turn']++;
	}

	// Play a card
	function play_card( $card_number, $target = '' ) {
		
		if ( isset( $this->gamedata['current_state'] ) && 'player_turn' === $this->gamedata['current_state'] ) {

			$hand = $this->gamedata['game_data']['player']['hand']['cards'];
			// do stuff.
			$i=0;
			foreach ( $hand as $card ) {
				
				if ( (int)$card['number'] === (int)$card_number ) {
					
					// Check for available mana
					if ( $this->gamedata['game_data']['player']['mana'] < $card['cost'] ) {
						return new WP_Error( 'no_mana', __( 'Not enough mana.', 'cge' ) );
					}
					
					// Reduce mana
					$this->gamedata['game_data']['player']['mana'] -= $card['cost'];
					
					if ( 'permanent' === $card['type'] ) {
						
						$this->play_permanent_card( $card );
						
					} elseif ( 'self' === $target || ( empty( $target ) && 'self' === $card['target'] ) ) {
						
						$this->play_card_on_target( $card, 'self' );
						
					} elseif( is_numeric( $target ) ) {
												
						$enemies = $this->gamedata['game_data']['enemy']['enemies'];
						
						foreach ( $enemies as $creature ) {
							
							if ( (int)$target === (int)$creature['target'] ) {
								
								$this->play_card_on_target( $card, 'enemy', $creature );
							
							}
						}
						
					}
					
					// Update hand
					array_splice( $this->gamedata['game_data']['player']['hand']['cards'], $i, 1 );
					$this->gamedata['game_data']['player']['hand']['count']--;
					
					// Update discard pile
					$this->gamedata['game_data']['player']['discard_pile']['cards'][] = $card;
					$this->gamedata['game_data']['player']['discard_pile']['count']++;

					$this->check_health_states();

					return true;
				}
				$i++;

			}

			return new WP_Error( 'wrong_card', __( 'Not a valid card.', 'cge' ) );

		} else {
			return new WP_Error( 'wrong_gamestate', __( 'Can not play card at this gamestate.', 'cge' ) );
		}
		
	}
	
	// Play a card on a creature
	function play_card_on_target( $card, $target_type, $enemy = false) {

		$card_effects = $this->database->get_card_effects( $card['card_id'] );
		
		foreach ( $card_effects as $effect ) {
			
			if ( isset( $this->effects[ $effect['effect'] ] ) )	{
				
				$effect_class_name = $this->effects[ $effect['effect'] ]['class'];
				$effect_class = new $effect_class_name( $this );
				
				$target = [ 'type' => $target_type ];
				
				// Is the target of this effect different from 
				if ( 'default' !== $effect['target'] ) {
					$target = [ 'type' => $effect['target'] ];			
				} elseif ( $enemy ) {
					$target['enemy'] = $enemy['target'];
				}
				
				$response = $effect_class->do_effect( $target, $effect['effect_strength'] );
				
				if ( ! empty( $response['action_log'] ) ) {
					$this->action_log = array_merge( $this->action_log, $response['action_log'] );
				}

			}

		}
	}

	// Put a permanent card in play
	function play_permanent_card( $card ) {
		
		$this->gamedata['game_data']['player']['in_play'][] = $card;

	}
	
	// Enemy creatures attack
	function enemy_attacks() {
		
		$enemy_enemies = $this->gamedata['game_data']['enemy']['enemies'];
		
		foreach ( $enemy_enemies as $index => $enemy ) {

			if ( $enemy['state'] === 'alive' && ! $this->effect_handler->check_curse( $enemy, 'prevent_attack' ) ) {
				$this->gamedata['game_data']['player']['health'] -= $enemy['attack'];
				$this->action_log[] = [ 'action' => 'enemy_attacks', 'enemy' => $enemy['target'], 'amount' => $enemy['attack'] ];
			}

		}
		
	}

	// check for dead enemies / heroes :-D	
	function check_health_states() {

		$has_live_enemy = false;
		
		foreach ( $this->gamedata['game_data']['enemy']['enemies'] as $index => $enemy ) {

			if ( $enemy['health'] <= 0 ) {
				$this->gamedata['game_data']['enemy']['enemies'][ $index ]['state'] = 'dead';
			} else {
				$has_live_enemy = true;
			}	
		}

		if ( ! $has_live_enemy ) {
			$this->level_complete();
			return;			
		}

		if ( $this->gamedata['game_data']['player']['health'] <= 0 ) {
			$this->gamedata['game_data']['player']['health'] = 0;
			$this->game_over();
			return;
		}
				
	}
	
	
	// End game (badly)
	function game_over() {
		$this->set_current_state( 'game_over', [] );
		$this->gamedata['game_data']['status'] = 'finished';
	}

	// set up new level
	function level_complete() {

		$this->gamedata['game_data']['level']++;
		
		if ( $enemies = $this->database->get_level_enemies( $this->gamedata['game_data']['level'] ) ) {
			$this->gamedata['game_data']['enemy']['enemies'] = $enemies;	
		} else {
			$this->set_current_state( 'victory', [] );
			$this->gamedata['game_data']['status'] = 'finished';
		}
				
	}
	
	// End current turn, run enmy turn, initiate next turn
	function end_turn() {
		if ( isset( $this->gamedata['current_state'] ) && 'player_turn' === $this->gamedata['current_state'] ) {

			// Do enemy moves
			$this->enemy_attacks();
			
			// Set state to player_turn, add action log
			$this->set_current_state( 'player_turn', [] );

			// Discard hand if game settings say so			
			if ( $this->get_setting( 'discard_on_end_turn' ) ) {
				$this->discard_hand();
			}
			
			$this->draw_cards( $this->get_setting( 'draw_cards_count' ) );
			
			$this->gamedata['game_data']['player']['mana'] = $this->get_setting( 'starting_mana' );
			
			$this->check_health_states();
			
			// Increase turn counter
			$this->next_turn();
			
			$this->trigger_actions( 'on_start_turn' );

			$this->check_health_states();
			
		} else {
			return new WP_Error( 'wrong_gamestate', __( 'Can not end turn at this gamestate.', 'cge' ) );
		}
	}
	
	// Select class and set up deck/hands
	function select_class( $class_id ) {
		
		// Check for valid class
		if ( $class = $this->database->get_classes( $class_id ) ) {

			// Set class
			$this->gamedata['game_data']['player']['class'] = $class[0]['name'];

			// Get basic deck
			$this->gamedata['game_data']['player']['deck']['cards'] = $this->database->get_basic_deck( $class_id );
			$this->gamedata['game_data']['player']['deck']['count'] = count( $this->gamedata['game_data']['player']['deck']['cards'] );

			// Copy deck to draw pile and shuffle
			$draw_pile = $this->gamedata['game_data']['player']['deck']['cards'];
			shuffle( $draw_pile );

			$this->gamedata['game_data']['player']['draw_pile']['cards'] = $draw_pile;
			$this->gamedata['game_data']['player']['draw_pile']['count'] = count( $draw_pile );

			// Draw starting hand			
			$this->draw_cards( $this->get_setting( 'starting_hand_count' ) );
			
			return true;
		} else {
			return false;
		}
	}

	// Draw x cards
	function draw_cards( $amount ) {
		
		$remaining = $amount;
		
		while ( $remaining > 0 ) {
			
			if ( $amount >= $this->gamedata['game_data']['player']['draw_pile']['count'] ) {
				$remaining = $amount - $this->gamedata['game_data']['player']['draw_pile']['count'];
				$amount = 	$this->gamedata['game_data']['player']['draw_pile']['count'];
			} else {
				$remaining = 0;
			}

			// Get new cards from draw pile
			$new_cards = array_slice( $this->gamedata['game_data']['player']['draw_pile']['cards'], 0, $amount );
	
			// Remove drawn cards from draw pile
			$new_draw_pile = array_slice( $this->gamedata['game_data']['player']['draw_pile']['cards'], $amount );
			$this->gamedata['game_data']['player']['draw_pile']['cards'] = $new_draw_pile;
			$this->gamedata['game_data']['player']['draw_pile']['count'] = count( $new_draw_pile );
			
			// Merge old hand with new
			$hand = array_merge( $this->gamedata['game_data']['player']['hand']['cards'], $new_cards );
			
			// Add a number to each card in hand
			$i = 0;
			foreach ( $hand as $id => $card ) {
				$i++;
				$hand[ $id ]['number'] = $i;
			}
			
			// Set numbered hand
			$this->gamedata['game_data']['player']['hand']['cards'] = $hand;
			$this->gamedata['game_data']['player']['hand']['count'] = count( $this->gamedata['game_data']['player']['hand']['cards'] );
			
			if ( $remaining > 0 ) {
				$this->shuffle_discard_pile();
			}

		}
			
	}

	// Discard your whole hand
	function discard_hand() {
		
		// Add hand to discard pile
		$this->gamedata['game_data']['player']['discard_pile']['cards'] = array_merge( $this->gamedata['game_data']['player']['discard_pile']['cards'], $this->gamedata['game_data']['player']['hand']['cards'] );
		$this->gamedata['game_data']['player']['discard_pile']['count'] = count( $this->gamedata['game_data']['player']['discard_pile']['cards'] );

		$this->gamedata['game_data']['player']['hand']['cards'] = [];
		$this->gamedata['game_data']['player']['hand']['count'] = 0;
		
	}

	function shuffle_discard_pile() {
		
		$cards = $this->gamedata['game_data']['player']['discard_pile']['cards'];
		
		$this->gamedata['game_data']['player']['discard_pile']['cards'] = [];
		$this->gamedata['game_data']['player']['discard_pile']['count'] = 0;

		shuffle( $cards );
				
		$this->gamedata['game_data']['player']['draw_pile']['cards'] = $cards;
		$this->gamedata['game_data']['player']['draw_pile']['count'] = count( $cards );
		
	}

	// Change the current gamestate
	function set_current_state( $state, $action = []  ) {
		$this->gamedata['current_state'] = $state;
		$this->gamedata['action'] = $action;
	}
	
	// Save the current gamestate to cache
	function save_gamestate() {
		
		if ( $this->game_id ) {
			
			set_transient( 'game_' . $this->game_id, $this->gamedata, 12 * HOUR_IN_SECONDS );			
			
		}
	}
	
	// Load the current gamestate from cache
	function load_gamestate( $game_id ) {
		if ( $game_id ) {
			$data =  get_transient( 'game_' . $game_id );
			return $data;
		} else {
			return false;
		}
	}
	
	// Get setting value
	function get_setting( $setting_name ) {
		if ( ! empty( $this->gamedata['game_data']['settings'][ $setting_name ] ) ) {
			return $this->gamedata['game_data']['settings'][ $setting_name ];
		} else {
			return false;
		}
	}
}