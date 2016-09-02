<?php
/**
 * Notification formatting function for local notifications
 *
 * @param $action
 * @param $item_id
 * @param $secondary_item_id
 * @param $total_items
 * @param string $format
 *
 * @return array|string
 */
function bdbp_local_user_notifier_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

	$related_user_id = $item_id;

	$related_user_url = bp_core_get_user_domain( $related_user_id );

	$display_name = bp_core_get_user_displayname( $related_user_id );

	if ( (int) $total_items > 1 ) {

		$text = sprintf( __( '%s posted %d new activities', 'bp-user-activity-notifier' ),  $display_name, (int) $total_items );

		if ( 'string' == $format ) {
			return '<a href="' . $related_user_url . '" title="' . __( 'User Activities', 'bp-user-activity-notifier' ) . '">' . $text . '</a>';
		} else {
			return array(
				'link' => $related_user_url,
				'text' => $text
			);
		}
	} else {

		$activity = new BP_Activity_Activity( $secondary_item_id );
		$text = strip_tags( $activity->action );//here is the hack, think about it :)

		$notification_link = bp_activity_get_permalink( $activity->id, $activity );

		if ( 'string' == $format ) {
			return '<a href="' . $notification_link . '" title="' .$text . '">' . $text . '</a>';
		} else {
			return array(
				'link' => $notification_link,
				'text' => $text
			);
		}
	}
}

/**
 * Get all users who should be notified
 * By default, we only check for friends
 * @param $user_id
 *
 * @return mixed|void
 */
function buddydev_user_nofier_get_notifiable_user_ids( $user_id ) {

	$user_ids = array();

	if (  function_exists( 'friends_get_friend_user_ids' ) ) {
		$user_ids = friends_get_friend_user_ids( $user_id );
	}

	return apply_filters( 'buddydev_user_notifier_notifibale_user_ids', $user_ids );

}