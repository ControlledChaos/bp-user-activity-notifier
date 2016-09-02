<?php

/**
 * Notifier interface
 *
 * Interface BDBP_User_Activity_Notifier
 */
interface BDBP_User_Activity_Notifier {

	public function notify( $user_id, $activity_id, $content );
	public function delete( $activities );
	public function visited( $activity, $user_id );

}