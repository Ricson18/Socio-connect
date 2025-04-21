<?php
/**
 * View: List View
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list.php
 *
 * See more documentation about our views templating system.
 *
 * @link    http://evnt.is/1aiy
 *
 * @since   6.1.4 Changing our nonce verification structures.
 *
 * @version 6.2.0
 * @since 6.2.0 Moved the header information into a new components/header.php template.
 *
 * @var array    $events               The array containing the events.
 * @var string   $rest_url             The REST URL.
 * @var string   $rest_method          The HTTP method, either `POST` or `GET`, the View will use to make requests.
 * @var int      $should_manage_url    int containing if it should manage the URL.
 * @var bool     $disable_event_search Boolean on whether to disable the event search.
 * @var string[] $container_classes    Classes used for the container of the view.
 * @var array    $container_data       An additional set of container `data` attributes.
 * @var string   $breakpoint_pointer   String we use as pointer to the current view we are setting up with breakpoints.
 */

$header_classes = [ 'tribe-events-header' ];
if ( empty( $disable_event_search ) ) {
	$header_classes[] = 'tribe-events-header--has-event-search';
}

?>
<div
	<?php tribe_classes( $container_classes ); ?>
	data-js="tribe-events-view"
	data-view-rest-url="<?php echo esc_url( $rest_url ); ?>"
	data-view-rest-method="<?php echo esc_attr( $rest_method ); ?>"
	data-view-manage-url="<?php echo esc_attr( $should_manage_url ); ?>"
	<?php foreach ( $container_data as $key => $value ) : ?>
		data-view-<?php echo esc_attr( $key ) ?>="<?php echo esc_attr( $value ) ?>"
	<?php endforeach; ?>
	<?php if ( ! empty( $breakpoint_pointer ) ) : ?>
		data-view-breakpoint-pointer="<?php echo esc_attr( $breakpoint_pointer ); ?>"
	<?php endif; ?> >

	
	<div class="tribe-common-l-container tribe-events-l-container">

		<?php do_shortcode('[global_location_changer]'); ?>
		<div style="height: 20px;"></div>

		<?php $this->template( 'components/loader', [ 'text' => __( 'Loading...', 'the-events-calendar' ) ] ); ?>

		<?php $this->template( 'components/json-ld-data' ); ?>

		<?php $this->template( 'components/data' ); ?>

		<?php $this->template( 'components/before' ); ?>

		<?php $this->template( 'components/header' ); ?>

		<?php $this->template( 'components/filter-bar' ); ?>

		<div class="tribe-events-calendar-list">
			<?php 
			$user_location = xprofile_get_field_data('location',get_current_user_id());
			$location_events = [];

			// foreach($events as $event){
			// 	$fields = tribe_get_custom_fields( $event->ID );
			// 	if($user_location === $fields['Location'])// $location)
			// 		$location_events[] = $event;
			// }

			if(isset($_GET['location']) && ($_GET['location'] === '---')){
				foreach($events as $event){
					$fields = tribe_get_custom_fields( $event->ID );
					if( $user_location === $fields['Location'])
						$location_events[] = $event;
				}
			}else if(isset($_GET['location']) && ($_GET['location'] === 'All')){
				$location_events = $events;
			}else if(isset($_GET['location'])){
				foreach($events as $event){
					$fields = tribe_get_custom_fields( $event->ID );
					if( $_GET['location'] === $fields['Location'])
						$location_events[] = $event;
				}
			}else{
				foreach($events as $event){
					$fields = tribe_get_custom_fields( $event->ID );
					if( $user_location === $fields['Location'])
						$location_events[] = $event;
				}
			}


			$allevents = $events;

			$events = $location_events;

			//$events = get_event_posts() 
			// 
			// ?>
			<?php foreach ( $events as $event ) : ?>

				<?php $this->setup_postdata( $event ); ?>

				<?php $this->template( 'list/month-separator', [ 'event' => $event ] ); ?>

				<?php $this->template( 'list/event', [ 'event' => $event ] ); ?>

			<?php endforeach; ?>

		<?php
			if(!empty($allevents) && empty($events)){ ?>
				<div class="tribe-events-header__messages tribe-events-c-messages tribe-common-b2 tribe-common-c-loader__dot tribe-common-c-loader__dot--third">
					<div class="tribe-events-c-messages__message tribe-events-c-messages__message--notice" role="alert">
								<ul class="tribe-events-c-messages__message-list" role="alert" aria-live="polite">
											<li class="tribe-events-c-messages__message-list-item" data-key="0">
							There were no results found.					</li>
									</ul>
					</div>
				</div>
		<?php } ?>


		</div>

		<?php $this->template( 'list/nav' ); ?>

		<?php $this->template( 'components/ical-link' ); ?>

		<?php $this->template( 'components/after' ); ?>

	</div>
</div>

<?php $this->template( 'components/breakpoints' ); ?>