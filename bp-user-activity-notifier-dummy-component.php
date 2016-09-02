<?php
//The ways of BuddyPress
//are strange some times,
//But so is life.
/**
 * Class BDBP_User_Activity_Notifier_Component
 * Dummy Component class
 */
class BDBP_User_Activity_Notifier_Component extends BP_Component {

	public function __construct() {

		$bp = buddypress();
		parent::start(
			'bdbp_unotifier', //it is a pain to find unique slugs, using obscure makes it worse
			__( 'User Activity Notifier', 'bp-local-group-notifier' ),
			plugin_dir_path( __FILE__ )
		);

		$bp->active_components[$this->id] = 1;
	}


	public function includes( $files = array() ) {

	}

	public function setup_globals( $global = array() ) {

		$globals = array(
			'slug'                  => $this->id,
			'root_slug'             => false,
			'has_directory'         => false,
			'notification_callback' => 'bdbp_local_user_notifier_format_notifications',
			'global_tables'         => false
		);

		parent::setup_globals( $globals );

	}

}

/**
 * Register the Dummy Component for the notifier
 */
function bdbp_setup_local_user_activity_notifier() {

	$bp = buddypress();

	$bp->bdbp_unotifier = new BDBP_User_Activity_Notifier_Component();
}
add_action( 'bp_loaded', 'bdbp_setup_local_user_activity_notifier' );

