<?php

/**
 * The Game REST API
 *
 * @link       https://github.com/oysteink/
 * @since      1.0.0
 *
 * @package    Cge
 * @subpackage Cge/includes
 */

/**
 * The Game REST API class
 *
 * @package    Cge
 * @subpackage Cge/includes
 * @author     oysteink <oysteink@gmail.com>
 */

if ( class_exists( 'WP_REST_Controller' ) ) {

	class CGE_REST_API extends WP_REST_Controller {

		/**
		 * Register the routes for the objects of the controller.
		 */
		public function register_routes() {

			$namespace = 'cge/game';

			register_rest_route( $namespace, '/' . 'create', array(

					array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'create_game' ),
					),

				) );

			register_rest_route( $namespace, '/' . 'response', array(

					array(
						'methods'         => array( WP_REST_Server::READABLE ),
						'callback'        => array( $this, 'player_response' ),
						'args' => array(
									'game_id' 			=> array( 'required' => true ),
									'response'	 		=> array( 'required' => true ),
								),
					),

				) );

			register_rest_route( $namespace, '/' . 'play_card', array(

					array(
						'methods'         => array( WP_REST_Server::READABLE ),
						'callback'        => array( $this, 'play_card' ),
						'args' => array(
									'game_id' 			=> array( 'required' => true ),
									'card'	 			=> array( 'required' => true ),
									'target'	 		=> array( 'optional' => true ),
								),
					),

				) );

			register_rest_route( $namespace, '/' . 'end_turn', array(

					array(
						'methods'         => array( WP_REST_Server::READABLE ),
						'callback'        => array( $this, 'end_turn' ),
						'args' => array(
									'game_id' 			=> array( 'required' => true )
								),
					),

				) );
				
			register_rest_route( $namespace, '/' . 'game_state', array(

					array(
						'methods'         => array( WP_REST_Server::READABLE ),
						'callback'        => array( $this, 'game_state' ),
						'args' => array(
									'game_id' 			=> array( 'required' => true ),
								),
					),

				) );

		}

		/**
		 * Set up a new game.
		 */
		function create_game() {
			
			$game = new Cge_Game();
			$gamestate = $game->get_gamestate();
			
			return new WP_REST_Response( $gamestate, 200 );
			
		}

		/**
		 * Retrieve player response.
		 */
		function player_response( $request ) {
			
			$game = new Cge_Game( $request['game_id'] );
			
			$response = $game->player_response( $request['response'] );
			
			if ( is_wp_error( $response ) ) {
				return $response; 
			} else {
				return new WP_REST_Response( $game->get_gamestate(), 200 );				
			}
			
		}

		/**
		 * Play a card.
		 */
		function play_card( $request ) {
			
			$game = new Cge_Game( $request['game_id'] );
			
			$response = $game->play_card( $request['card'], $request['target'] );
			
			if ( is_wp_error( $response ) ) {
				return $response; 
			} else {
				return new WP_REST_Response( $game->get_gamestate(), 200 );				
			}
			
		}

		/**
		 * Return the current game state.
		 */
		function game_state( $request ) {
			
			$game = new Cge_Game( $request['game_id'] );
			
			return new WP_REST_Response( $game->get_gamestate(), 200 );
			
		}

		/**
		 * Return the current game state.
		 */
		function end_turn( $request ) {
			
			$game = new Cge_Game( $request['game_id'] );
			
			$response = $game->end_turn( );
			
			return new WP_REST_Response( $game->get_gamestate(), 200 );
			
		}		
	}

}

// Function to register our new routes from the controller.
function cge_rest_api() {

	$cge_rest_api = new CGE_REST_API();
	$cge_rest_api->register_routes();

}

add_action( 'rest_api_init', 'cge_rest_api' );
