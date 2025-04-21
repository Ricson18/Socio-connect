<?php
/**
 * BuddyBoss - Groups Loop
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/groups-loop.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 *
 * @package BuddyBoss\Core
 */

add_filter( 'bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2 );

bp_nouveau_before_loop();

if ( bp_get_current_group_directory_type() ) {
	?>
	<div class="bp-feedback info">
		<span class="bp-icon" aria-hidden="true"></span>
		<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
	</div>
	<?php
}

$cover_class        = ! bb_platform_group_element_enable( 'cover-images' ) ? 'bb-cover-disabled' : 'bb-cover-enabled';
$meta_privacy       = ! bb_platform_group_element_enable( 'group-privacy' ) ? 'meta-privacy-hidden' : '';
$meta_group_type    = ! bb_platform_group_element_enable( 'group-type' ) ? 'meta-group-type-hidden' : '';
$group_members      = ! bb_platform_group_element_enable( 'members' ) ? 'group-members-hidden' : '';
$join_button        = ! bb_platform_group_element_enable( 'join-buttons' ) ? 'group-join-button-hidden' : '';
$group_alignment    = bb_platform_group_grid_style( 'left' );
$group_cover_height = function_exists( 'bb_get_group_cover_image_height' ) ? bb_get_group_cover_image_height() : 'small';

global $wpdb;

// Custom SQL query to get groups
$groups_query = $wpdb->get_results(
	"SELECT g.*, gm.* 
	FROM {$wpdb->prefix}bp_groups g
	LEFT JOIN {$wpdb->prefix}bp_groups_members gm ON g.id = gm.group_id 
	WHERE g.status != 'hidden'
	ORDER BY g.date_created DESC"
);

$user_location = xprofile_get_field_data('location',get_current_user_id());


$groups_in_location = [];
foreach ($groups_query as $group) {
	$group_location = groups_get_groupmeta( $group->group_id, 'group-location');
	// print_r($group);
	// echo "($group_location==$user_location)";
	if(!empty($group_location) && ($group_location==$user_location))
		$groups_in_location[] = $group;
}

if($user_location == 'All')
	$groups_in_location = $groups_query;

if (!empty($groups_in_location)) { ?>
	<div id="groups-dir-list" class="groups dir-list" data-bp-list="groups" data-ajax="false">

		<div class="bp-pagination top" data-bp-pagination="grpage">
			<div class="pag-count top">
				<p class="pag-data">
					Viewing 1 - 2 of 2 groups				</p>
			</div>
		</div>
	
	<ul id="groups-list" class="item-list groups-list bp-list grid bb-cover-enabled left groups-dir-list">

	<?php
		foreach ($groups_in_location as $group) {
			$bp_group_id = $group->id;
			?>
		
		
		<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr( $bp_group_id ); ?>" data-bp-item-component="groups">
				<div class="list-wrap">
				
				<?php 
					if ( ! bp_disable_group_cover_image_uploads() && bb_platform_group_element_enable( 'cover-images' ) ) {
						$group_cover_image_url = bp_attachments_get_attachment(
							'url',
							array(
								'object_dir' => 'groups',
								'item_id'    => $bp_group_id,
							)
						);
						$has_default_cover     = function_exists( 'bb_attachment_get_cover_image_class' ) ? bb_attachment_get_cover_image_class( $bp_group_id, 'group' ) : '';
						?>
						<div class="bs-group-cover only-grid-view <?php echo esc_attr( $has_default_cover . ' cover-' . $group_cover_height ); ?>">
							<a href="<?php bp_group_permalink($group); ?>">
								<?php if ( ! empty( $group_cover_image_url ) ) { ?>
									<img src="<?php echo esc_url( $group_cover_image_url ); ?>">
								<?php } ?>
							</a>
						</div>
						<?php
					}
				?>


					<?php 
						if ( ! bp_disable_group_avatar_uploads() && bb_platform_group_element_enable( 'avatars' ) ) {
							?>
							<div class="item-avatar"><a href="<?php bp_group_permalink($group); ?>" class="group-avatar-wrap"><?php echo bp_group_avatar_thumb($group); ?></a></div>
							<?php
						}
					?>

					<div class="item <?php echo esc_attr( $group_members . ' ' . $join_button ); ?>">

						<div class="group-item-wrap">
							<div class="item-block">
								<h2 class="list-title groups-title"><?php bp_group_link($group); ?></h2>
								<div class="item-meta-wrap <?php echo esc_attr( bb_platform_group_element_enable( 'last-activity' ) || empty( $meta_privacy ) || empty( $meta_group_type ) ? 'has-meta' : 'meta-hidden' ); ?> ">
									
									<?php // if ( bp_nouveau_group_has_meta() ) { ?>

										<p class="item-meta group-details <?php echo esc_attr( $meta_privacy . ' ' . $meta_group_type ); ?>">
											<?php echo bp_get_group_type($group); ?>

											<!-- <span class="group-visibility public">Public</span>
											<span class="group-type">Group</span> -->
										<?php
											// $meta = bp_nouveau_get_group_meta();
											// echo wp_kses_post( $meta['status'] );

											if ( bb_platform_group_element_enable( 'last-activity' ) ) {
												echo '<p class="last-activity item-meta">' .
												sprintf(
														/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
													esc_attr__( 'Active %s', 'buddyboss' ),
													wp_kses_post( bp_get_group_last_active($group) )
												) .
												'</p>';
											}
											
										?>
										<!-- <span class="group-visibility public">Public</span> <span class="group-type">Group</span>										</p> -->
										<!-- <p class="last-activity item-meta">Active Just now</p> -->

									<?php //} ?>

								</div>
							</div>

							<?php if ( bb_platform_group_element_enable( 'group-descriptions' ) ) { ?>
								<div class="item-desc group-item-desc only-list-view"><?php bp_group_description_excerpt( $group, 150 ); ?></div>
							<?php } ?>
					</div>

						
			<div class="group-footer-wrap <?php echo esc_attr( $group_members . ' ' . $join_button ); ?>">
					<div class="group-members-wrap">
						<?php bb_groups_loop_members($group->id); ?>
					</div>

					<!--?php if ( bb_platform_group_element_enable( 'join-buttons' ) ) {
						$args = ['type'=>'group'];
						<div class="groups-loop-buttons footer-button-wrap"><!?php  
							// echo "<pre>";
							// ob_start();
							// bp_nouveau_group_buttons($group); 
							// $output = ob_get_clean();
							// $args = array( 'classes' => array( 'item-buttons' ) );
							// bp_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
							// echo $output;
							// echo "</pre>";
							
							?></div-->
						<div class="groups-loop-buttons footer-button-wrap">
							<div class="bp-generic-meta groups-meta action">
								<div id="groupbutton-2" class="generic-button">
									<button class="group-button leave-group bp-toggle-action-button button" data-title="Leave Group" data-title-displayed="Organizer" data-bb-group-name="Group 2" data-bb-group-link="http://localhost/multivendorpodplaceholder/groups/group-2/" data-only-admin="1" data-bp-nonce="http://localhost/multivendorpodplaceholder/groups/group-2/leave-group/?_wpnonce=b8269cea68" data-bp-btn-action="leave_group">Organizer</button>
								</div>
							</div>
						</div>
					<!--?php } ?-->
			</div>

					</div>
				</div>
			</li>

		
			<?php } ?>
			
			</ul>

	<!-- Leave Group confirmation popup -->
	<div class="bb-leave-group-popup bb-action-popup" style="display: none">
		<transition name="modal">
			<div class="modal-mask bb-white bbm-model-wrap">
				<div class="modal-wrapper">
					<div class="modal-container">
						<header class="bb-model-header">
							<h4><span class="target_name">Leave Group</span></h4>
							<a class="bb-close-leave-group bb-model-close-button" href="#">
								<span class="bb-icon-l bb-icon-times"></span>
							</a>
						</header>
						<div class="bb-leave-group-content bb-action-popup-content">
							<p>Are you sure you want to leave <span class="bb-group-name"></span>?</p>
						</div>
						<footer class="bb-model-footer flex align-items-center">
							<a class="bb-close-leave-group bb-close-action-popup" href="#">Cancel</a>
							<a class="button push-right bb-confirm-leave-group" href="#">Confirm</a>
						</footer>

					</div>
				</div>
			</div>
		</transition>
	</div> <!-- .bb-leave-group-popup -->

	
	
	<div class="bp-pagination bottom" data-bp-pagination="grpage">

					<div class="pag-count bottom">

				<p class="pag-data">
					Viewing 1 - 2 of 2 groups				</p>

			</div>
		
		
	</div>

		</div>
		
		<?php
} else {
	bp_nouveau_user_feedback('groups-loop-none');
}

bp_nouveau_after_loop();

remove_filter('bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2);
 