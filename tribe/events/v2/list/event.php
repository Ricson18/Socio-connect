<?php
/**
 * View: List Event
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list/event.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$container_classes = [ 'tribe-common-g-row', 'tribe-events-calendar-list__event-row' ];
$container_classes['tribe-events-calendar-list__event-row--featured'] = $event->featured;

$event_classes = tribe_get_post_class( [ 'tribe-events-calendar-list__event', 'tribe-common-g-row', 'tribe-common-g-row--gutters' ], $event->ID );
?>
<div <?php tribe_classes( $container_classes ); ?>>

	<?php $this->template( 'list/event/date-tag', [ 'event' => $event ] ); ?>

	<div class="tribe-events-calendar-list__event-wrapper tribe-common-g-col">
		<article <?php tribe_classes( $event_classes ) ?>>
			<?php $this->template( 'list/event/featured-image', [ 'event' => $event ] ); ?>

			<div class="tribe-events-calendar-list__event-details tribe-common-g-col">

				<header class="tribe-events-calendar-list__event-header">
					<?php $this->template( 'list/event/date', [ 'event' => $event ] ); ?>
					
					<?php $fields = tribe_get_custom_fields( $event->ID ); ?>

					<?php if(!empty($fields['Location'])){ ?>
						<span class="tribe-events-calendar-list__event-venue-address tribe-common-c-svgicon--location">
							<span>
								<svg fill="#ff0000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
									width="10px" height="10px" viewBox="0 0 395.71 395.71" xml:space="preserve" stroke="#ff0000"><g id="SVGRepo_bgCarrier" 
									stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
									<g id="SVGRepo_iconCarrier"> <g> <path d="M197.849,0C122.131,0,60.531,61.609,60.531,137.329c0,72.887,124.591,243.177,129.896,250.388l4.951,6.738 c0.579,0.792,1.501,1.255,2.471,1.255c0.985,0,1.901-0.463,2.486-1.255l4.948-6.738c5.308-7.211,129.896-177.501,129.896-250.388 C335.179,61.609,273.569,0,197.849,0z M197.849,88.138c27.13,0,49.191,22.062,49.191,49.191c0,27.115-22.062,49.191-49.191,49.191 c-27.114,0-49.191-22.076-49.191-49.191C148.658,110.2,170.734,88.138,197.849,88.138z"></path> 
								</g> </g></svg>
							</span> 
							<span style="font-size:12px;"><?php echo $fields['Location']; ?></span>
						</span>
					<?php } ?>
					
					<?php $this->template( 'list/event/title', [ 'event' => $event ] ); ?>

					
					<?php //$this->template( 'list/event/location', [ 'event' => $event ] ); ?>
					<?php $this->template( 'list/event/venue', [ 'event' => $event ] ); ?>
				</header>

				<?php $this->template( 'list/event/description', [ 'event' => $event ] ); ?>
				<?php $this->template( 'list/event/cost', [ 'event' => $event ] ); ?>

			</div>
		</article>
	</div>

</div>
