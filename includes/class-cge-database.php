<?php

/**
 * Handles retrieving cards, inventory from database
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/includes
 */

/**
 * Game database handler
 *
 * Functions for retrieving cards, inventory, levels etc from the game database
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */
class Cge_Database {

	/**
	 * Fetch list of available classes, or specific class
	 *
	 * @since    1.0.0
	 * @param    string               $class             Optional. If specified, the function will look for this specific class.
	 */
	public function get_classes( $class = false ) {

		$args = [ 'taxonomy' => 'cge-class', 'hide_empty' => false ];
		
		if ( $class ) {
			$args['slug'] = $class;
		}

		if ( $classes = get_terms( $args ) ){
		
			$return_data = [];
			
			foreach ( $classes as $class ) {
				$return_data[] = [ 'id' => $class->slug, 'name' => $class->name ];
			}
			
			return $return_data;
			
		} else {
			return false;
		}

	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $class             Specify class to receive deck from.
	 */
	public function get_basic_deck( $class = false ) {
		
		$args = [
			'post_type' => 'cge-deck',
			'cge-class' => $class 
		];
		
		$posts = get_posts( $args );
		
		$deck = [];
		
		if ( isset( $posts[0]->ID ) ) {
			
			$cards = get_post_meta( $posts[0]->ID, 'cge-cards', true );
			
			foreach ( $cards as $card ) {
				$deck[] = $this->get_card( $card );
			}
			
			return $deck;
			
		} else {
			return false;
		}
		
	}

	/**
	 * Fetch card object.
	 *
	 * @since    1.0.0
	 * @param    int               $card_id 
	 */
	function get_card( $card_id ) {
		return get_post_meta( $card_id, 'cge-card-details', true );
	}
	
	/**
	 * Fetch card effects.
	 *
	 * @since    1.0.0
	 * @param    int               $card_id
	 */
	function get_card_effects( $card_id ) {
		return get_post_meta( $card_id, 'cge-card-effects', true );
	}
	
	/**
	 * Fetch game settings.
	 *
	 * @since    1.0.0
	 * @param    int               $card_id
	 */
	function get_game_settings() {

		$settings = [
			'starting_health' => 30,
			'starting_mana' => 5,
			'discard_on_end_turn' => true,
			'starting_hand_count' => 5,
			'reshuffle_when_draw_pile_empty' => true,
			'draw_cards_count' => 5
		];
		
		// Loop through options and override with custom value
		foreach ( $settings as $option_name => $option_value ) {
			if ( $option_value = get_option( 'options_cge_' . $option_name ) ) {
				$settings[ $option_name ] = $option_value;
			}
		}
		
		// Return game settings
		return $settings;
		
	}
	
	// fetch a post based on level - load enemies
	function get_level_enemies( $level ) {
		
		$args = [
			'post_type' => 'cge-level',
			'meta_key' => 'tier',
			'meta_value' => $level,
			'order' => 'rand'
		];
		
		$posts = get_posts( $args );
		
		$level_enemies = [];
		
		if ( isset( $posts[0]->ID ) ) {
			
			$creatures = get_post_meta( $posts[0]->ID, 'cge-enemies', true );
			
			$target = 0;
			
			foreach ( $creatures as $creature ) {

				$target++;

				$level_enemies[] = [
					'state' => 'alive',
					'target' => $target,
					'name' => $creature['name'],
					'health' => $creature['health'],
					'max_health' => $creature['health'],
					'attack' => $creature['attack'],
				];
			
			}
			
			return $level_enemies;
			
		} else {
			return false;
		}
		
	}

}
