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

		$this->settings = [ 'starting_mana' => 5, 'starting_cards' => 4, 'starting_health' => 30 ];

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
		$this->debug = [];

		// Load effects		
		$effect_loader = new Cge_Effects();
		$this->effects = $effect_loader->effects;
		
		

		// Load effects that are active on the board
		$this->load_active_effects();

		
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
		
		$gamedata = [
			'game_id' => $id,
			'turn' => 0,
			'status' => 'running',
			'player' => [
				'class' => '',
				'deck' => [],
				'hand' => [ 
					'count' => 0,
					'cards' => []
				 ],
				'draw_pile' => [
					'count' => 0,
				],
				'discard_pile' => [
					'count' => 0
				],
				'health' => $this->settings['starting_health'],
				'max_health' => $this->settings['starting_health'],
				'mana' => $this->settings['starting_mana'],
				'target' => 'self',
				'in_play' => [],
				'equipped' => [],
				'inventory' => [
				],
			],
			'enemy' => [
				'enemies' => [
					[
						'target' => 1,
						'name' => 'Sand Worm',
						'max_health' => 20,
						'health' => 20,
						'attack' => 5
					],
					[
						'target' => 2,
						'name' => 'Sand Worm',
						'health' => 20,
						'max_health' => 20,
						'attack' => 5,
						'shield' => 2
					],					
				]
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
	
	function trigger_actions( $trigger ) {
		
		if ( ! empty( $this->active_effects[ $trigger ] ) ) {
			
			foreach ( $this->active_effects[ $trigger ] as $effect ) {
			
				if ( isset( $this->effects[ $effect['effect'] ] ) )	{
					
					$effect_class_name = $this->effects[ $effect['effect'] ]['class'];
					$effect_class = new $effect_class_name;
					
					$response = $effect_class->do_effect( '', $effect['target'], $effect['effect_strength'], $this->gamedata );
					
					// If the effect returns gamedata, update
					if ( ! empty( $response['gamedata'] ) ) {
						$this->gamedata = $response['gamedata'];
					}
					
					if ( ! empty( $response['action_log'] ) ) {
						$this->action_log = array_merge( $this->action_log, $response['action_log'] );
					}
						
				}
	
			}

			
		}
		
		
		
	}
	
	function next_turn() {
		$this->gamedata['game_data']['turn']++;
	}

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
						
					} elseif( is_numeric( $target ) ) {
												
						$enemies = $this->gamedata['game_data']['enemy']['enemies'];
						
						foreach ( $enemies as $creature ) {
							
							if ( (int)$target === (int)$creature['target'] ) {
								
								$this->play_card_on_creature( $card, $creature );
							
							}
						}
						
					} elseif ( 'self' === $target )  {

					}
					
					// Update hand
					array_splice( $this->gamedata['game_data']['player']['hand']['cards'], $i, 1 );
					$this->gamedata['game_data']['player']['hand']['count']--;
					
					// Update discard pile
					$this->gamedata['game_data']['player']['discard_pile']['cards'][] = $card;
					$this->gamedata['game_data']['player']['discard_pile']['count']++;

					return true;
				}
				$i++;

			}

			return new WP_Error( 'wrong_card', __( 'Not a valid card.', 'cge' ) );

		} else {
			return new WP_Error( 'wrong_gamestate', __( 'Can not play card at this gamestate.', 'cge' ) );
		}
		
	}
	
	function play_card_on_creature( $card, $creature ) {
		
		//print_r( $card );
		//print_r( $creature );
		//print_r( $this->effects );

		$card_effects = $this->database->get_card_effects( $card['card_id'] );
		
		foreach ( $card_effects as $effect ) {
			
			if ( isset( $this->effects[ $effect['effect'] ] ) )	{
				
				$effect_class_name = $this->effects[ $effect['effect'] ]['class'];
				$effect_class = new $effect_class_name;
				
				$response = $effect_class->do_effect( $creature, 'creature', $effect['effect_strength'], $this->gamedata );
				
				// If the effect returns gamedata, update
				if ( ! empty( $response['gamedata'] ) ) {
					$this->gamedata = $response['gamedata'];
				}
				
				// If the effect returns gamedata, update
				if ( ! empty( $response['target'] ) ) {

					// The effect has updated the creature
					$creature = $response['target'];
					
					$this->update_creature( $creature );
					
				}
				
				if ( ! empty( $response['action_log'] ) ) {
					$this->action_log = array_merge( $this->action_log, $response['action_log'] );
				}

			}

		}
	}

	function play_permanent_card( $card ) {
		
		//print_r( $card );
		//print_r( $creature );
		//print_r( $this->effects );

		//$card_effects = $this->database->get_card_effects( $card['card_id'] );
		
		$this->gamedata['game_data']['player']['in_play'][] = $card;

	}

	
	function update_creature( $updated_creature ) {
		
		$enemy_enemies = $this->gamedata['game_data']['enemy']['enemies'];
		
		foreach ( $enemy_enemies as $index => $creature ) {
			if ( (int)$creature['target'] === (int)$updated_creature['target'] ) {
				$this->gamedata['game_data']['enemy']['enemies'][ $index ] = $updated_creature;
			}
		}
		
	}
	
	function enemy_attacks() {
		
		$enemy_enemies = $this->gamedata['game_data']['enemy']['enemies'];
		
		foreach ( $enemy_enemies as $index => $enemy ) {

			$this->gamedata['game_data']['player']['health'] -= $enemy['attack'];
			$this->action_log[] = [ 'action' => 'enemy_attacks', 'enemy' => $enemy['target'], 'amouht' => $enemy['attack'] ];

		}

		if ( $this->gamedata['game_data']['player']['health'] <= 0 ) {
			$this->gamedata['game_data']['player']['health'] = 0;
			$this->game_over();
			return;
		}
		
	}
	
	function game_over() {
		$this->set_current_state( 'game_over', [] );
		$this->gamedata['game_data']['status'] = 'finished';
	}
	
	function end_turn() {
		if ( isset( $this->gamedata['current_state'] ) && 'player_turn' === $this->gamedata['current_state'] ) {

			// Do enemy moves
			$this->enemy_attacks();
			
			// Set state to player_turn, add action log
			$this->set_current_state( 'player_turn', [] );
			
			$this->draw_cards( 1 );
			
			$this->gamedata['game_data']['player']['mana'] = 5;
			
			// Increase turn counter
			$this->next_turn();
			
			$this->trigger_actions( 'on_start_turn' );

			
		} else {
			return new WP_Error( 'wrong_gamestate', __( 'Can not end turn at this gamestate.', 'cge' ) );
		}
	}
	
	function select_class( $class_id ) {
		
		// Check for valid class
		if ( $class = $this->database->get_classes( $class_id ) ) {

			// Set class
			$this->gamedata['game_data']['player']['class'] = $class[0]['name'];

			// Get basic deck
			$this->gamedata['game_data']['player']['deck']['cards'] = $this->database->get_basic_deck( $class['id'] );
			$this->gamedata['game_data']['player']['deck']['count'] = count( $this->gamedata['game_data']['player']['deck']['cards'] );

			// Copy deck to draw pile and shuffle
			$draw_pile = $this->gamedata['game_data']['player']['deck']['cards'];
			shuffle( $draw_pile );

			$this->gamedata['game_data']['player']['draw_pile']['cards'] = $draw_pile;
			$this->gamedata['game_data']['player']['draw_pile']['count'] = count( $draw_pile );

			// Draw starting hand			
			$this->draw_cards( 4 );
			
			return true;
		} else {
			return false;
		}
	}

	function draw_cards( $amount ) {
		
		if ( $amount <= $this->gamedata['game_data']['player']['draw_pile']['count'] ) {
			
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
			
			
		} else {
			// do something smart
		}
	}


	// Change the current gamestate
	function set_current_state( $state, $action = []  ) {
		$this->gamedata['current_state'] = $state;
		$this->gamedata['action'] = $action;
	}
	
	function save_gamestate() {
		
		if ( $this->game_id ) {
			
			set_transient( 'game_' . $this->game_id, $this->gamedata, 12 * HOUR_IN_SECONDS );			
			
		}
	}
	
	function load_gamestate( $game_id ) {
		if ( $game_id ) {
			$data =  get_transient( 'game_' . $game_id );
			return $data;
		} else {
			return false;
		}
	}
	
	function get_starter_deck( $class ) {
		
		$args = [
			'post_type' => 'cge-deck',
			'cge-class' => $class
		];
		
		$deck_post = get_posts( $args );
		
	}
		
}