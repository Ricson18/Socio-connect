<?php
/**
 * View: List Single Event Venue
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list/event/venue.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 6.2.0
 * @since 6.2.0 Added the `tec_events_view_venue_after_address` action.
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var string  $slug  The slug of the view.
 *
 * @see tribe_get_event() For the format of the event object.
 */

if ( ! $event->venues->count() ) {
	return;
}

$separator            = esc_html_x( ', ', 'Address separator', 'the-events-calendar' );
$venue                = $event->venues[0];
$append_after_address = array_filter( array_map( 'trim', [ $venue->state_province, $venue->state, $venue->province ] ) );
$address              = $venue->address . ( $venue->address && ( $append_after_address || $venue->city ) ? $separator : '' );
?>
<address class="tribe-events-calendar-list__event-venue tribe-common-b2">
	<span class="tribe-events-calendar-list__event-venue-title tribe-common-b2--bold">
		<?php // echo wp_kses_post( $venue->post_title ); ?>
	</span>
	<span class="tribe-events-calendar-list__event-venue-address tribe-common-c-svgicon--location">
		<span>
		<svg fill="#ff0000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
			width="14px" height="14px" viewBox="0 0 395.71 395.71" xml:space="preserve" stroke="#ff0000"><g id="SVGRepo_bgCarrier" 
			stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
			<g id="SVGRepo_iconCarrier"> <g> <path d="M197.849,0C122.131,0,60.531,61.609,60.531,137.329c0,72.887,124.591,243.177,129.896,250.388l4.951,6.738 c0.579,0.792,1.501,1.255,2.471,1.255c0.985,0,1.901-0.463,2.486-1.255l4.948-6.738c5.308-7.211,129.896-177.501,129.896-250.388 C335.179,61.609,273.569,0,197.849,0z M197.849,88.138c27.13,0,49.191,22.062,49.191,49.191c0,27.115-22.062,49.191-49.191,49.191 c-27.114,0-49.191-22.076-49.191-49.191C148.658,110.2,170.734,88.138,197.849,88.138z"></path> 
		</g> </g></svg>
		</span> 
		<?php
		echo esc_html( $address );

		?>
	</span>
	<?php
	/**
	 * Fires after the full venue has been displayed.
	 *
	 * @since 6.2.0
	 *
	 * @param WP_Post $event Event post object.
	 * @param string  $slug  Slug of the view.
	 */
	do_action( 'tec_events_view_venue_after_address', $event, $slug );
	?>
</address>
