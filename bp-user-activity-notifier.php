<?php
/**
 * Plugin Name: BuddyPress User Activity Notifier
 * Version: 1.0.0
 * Plugin URI: https://buddydev.com
 * Author: BuddyDev Team
 * Author URI: https://buddydev.com
 * Description: Notify users of any activity of their friends
 */

class BDBP_User_Activity_Notifier_Helper {


	private static $instance = null;
	/**
	 * An array of registered notifiers
	 *
	 * @var BDBP_User_Activity_Notifier []
	 */
	private $notifiers = array();

	private function __construct() {

		add_action( 'bp_include', array( $this, 'setup' ) );
	}

	public static function get_instance() {

		if ( is_null( self::$instance ) ){
			self::$instance  = new self();
		}

		return self::$instance;

	}


	public function setup() {

		if ( ! bp_is_active( 'activity' )  ) {
			return;
		}

		$this->load();
		//notify on activity add
		add_action( 'bp_activity_posted_update', array( $this, 'notify' ), 100, 3 );
		//delete on activity delete
		add_action( 'bp_activity_after_delete', array( $this, 'delete' ) );

		add_action( 'bp_activity_screen_single_activity_permalink', array( $this, 'visited' ), 10, 2 );

		//format notifications
	}


	public function load() {

		$path = plugin_dir_path( __FILE__ );

		$files = array(
			'bp-user-activity-notifier-dummy-component.php',
			'core/bdbp-user-activity-notifier-functions.php',
			'core/bdbp-user-activity-notifier.php',
			'core/bdbp-local-notifier.php'
		);

		foreach ( $files as $file ) {
			require $path . $file;
		}
		//loaded
		//add local notifier
		if ( bp_is_active( 'notifications' ) ) {
			$this->register_notifier( 'local' , new BDBP_User_Local_Notifier() );
		}
	}


	public function notify( $content, $user_id, $activity_id ) {

		foreach ( $this->notifiers as $notifier ) {
			$notifier->notify( $user_id, $activity_id, $content );
		}

	}

	public function delete( $activities ) {

		foreach ( $this->notifiers as $notifier ) {
			$notifier->delete( $activities );
		}
	}


	public function visited( $activity ) {

		$user_id = get_current_user_id();

		foreach ( $this->notifiers as $notifier ) {
			$notifier->visited( $activity, $user_id );
		}
	}

	/**
	 * Register a new notifier
	 *
	 * @param string $key
	 * @param BDBP_User_Activity_Notifier $notifier
	 */
	public function register_notifier( $key, BDBP_User_Activity_Notifier $notifier ) {
		$this->notifiers[ $key ]  = $notifier;
	}

	public function deregister_notifier( $key ) {
		unset( $this->notifiers[ $key ] );
	}

}

 BDBP_User_Activity_Notifier_Helper::get_instance();
