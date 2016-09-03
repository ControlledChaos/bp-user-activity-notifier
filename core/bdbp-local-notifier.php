<?php

/**
 * Class to manage Notifying the user activity locally
 *
 * Class BDBP_User_Local_Notifier
 */
class BDBP_User_Local_Notifier implements BDBP_User_Activity_Notifier {


	public function notify( $user_id, $activity_id, $content ) {

		$users = buddydev_user_nofier_get_notifiable_user_ids( $user_id );

		if ( empty( $users ) ) {
			return;
		}

		$activity = new BP_Activity_Activity( $activity_id );

		//and we will add a notification for each user
		foreach ( (array) $users as $notifiable_user_id ) {

			if ( $notifiable_user_id == $activity->user_id ) {
				continue;//but not for the current logged user who performed this action
			}

			//we need to make each notification unique, otherwise bp will group it
			$this->add_notification(  $notifiable_user_id,  $user_id, $activity_id );
		}
	}

	/**
	 * Delete notifications when activities are deleted
	 *
	 * @param $activities
	 */
	public function delete( $activities ) {

		$user_id = bp_loggedin_user_id();

		foreach ( $activities as $activity ) {
			$this->visited( $activity, $user_id );
		}

	}

	/**
	 * Mark notifications as read
	 *
	 * @param BP BP_Activity_Activity $activity
	 * @param int $user_id
	 */
	public function visited( $activity, $user_id ) {

		bp_core_delete_notifications_by_item_id( $user_id, $activity->user_id,  'bdbp_unotifier','new_update', $activity->id );

	}

	/**
	 * Add notifications
	 *
	 * @param $user_id
	 * @param $related_user_id
	 * @param $activity_id
	 *
	 * @return bool|int
	 */
	public function add_notification( $user_id, $related_user_id, $activity_id ) {
		return bp_core_add_notification( $related_user_id, $user_id, 'bdbp_unotifier', 'new_update', $activity_id );
	}

}