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

		$activity_ids = wp_list_pluck( $activities, 'id' );

		foreach ( $activity_ids as $activity_id ) {

			BP_Notifications_Notification::delete( array(
				'component'         => 'bdbp_unotifier',
				'secondary_item_id' => $activity_id
			) );
		}

	}

	/**
	 * Mark notifications as read
	 *
	 * @param BP BP_Activity_Activity $activity
	 * @param int $user_id
	 */
	public function visited( $activity, $user_id ) {

		BP_Notifications_Notification::update( array(
			'is_new'         => 0
			),
			array(
				'secondary_item_id' => $activity->id,
				'component_name'    => 'bdbp_unotifier',
				'user_id'           => $user_id
			)
		);
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

		return bp_notifications_add_notification( array(
			'item_id'           => $related_user_id,
			'secondary_item_id' => $activity_id,
			'user_id'           => $user_id,
			'component_name'    => 'bdbp_unotifier',//buddyPress user notifier in short
			'component_action'  => 'new_update'//we may need to update it?
		) );
	}


}