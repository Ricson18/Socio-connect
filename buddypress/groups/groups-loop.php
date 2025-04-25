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

if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) {

	bp_nouveau_pagination( 'top' );
	?>

	<ul id="groups-list" class="
	<?php
	bp_nouveau_loop_classes();
	echo esc_attr( ' ' . $cover_class . ' ' . $group_alignment );
	?>
	groups-dir-list">


		<?php
		$groups_query = $wpdb->get_results(
			"SELECT g.*, gm.* 
			FROM {$wpdb->prefix}bp_groups g
			LEFT JOIN {$wpdb->prefix}bp_groups_members gm ON g.id = gm.group_id 
			WHERE g.status != 'hidden'
			ORDER BY g.date_created DESC"
		);
		
		$location_label = socio_connect_get_location_label();
		$user_location = xprofile_get_field_data($location_label,get_current_user_id());
		

		echo $user_location;
		
		exit;
		
		$groups_in_location = [];

		if(isset($_GET['location']) && ($_GET['location'] === '---')){
			foreach($groups_query as $group){
				$group_location = groups_get_groupmeta( $group->group_id, 'group-location', true);


				// print_r($group_location);

				// echo "<br>";

				if( is_array($group_location) ){
					if( !empty($group_location) && in_array($user_location,$group_location) )
						$groups_in_location[] = $group->group_id;
				}else{
					if(!empty($group_location) && ($group_location==$user_location))
						$groups_in_location[] = $group->group_id;
				}
			}
		}else if(isset($_GET['location']) && ($_GET['location'] === 'All')){
			foreach($groups_query as $group)
				$groups_in_location[] = $group->group_id;
		}else if(isset($_GET['location'])){
			foreach($groups_query as $group){
				$group_location = groups_get_groupmeta( $group->group_id, 'group-location', true);

				if( is_array($group_location) ){
					if( !empty($group_location) && in_array($_GET['location'],$group_location) )
						$groups_in_location[] = $group->group_id;
				}
				else{
					if( $_GET['location'] === $group_location)
						$groups_in_location[] = $group->group_id;
				}
			}
		}else{
			foreach($groups_query as $group){
				$group_location = groups_get_groupmeta( $group->group_id, 'group-location', true);

				if( is_array($group_location) ){
					if( !empty($group_location) && in_array($user_location,$group_location) )
						$groups_in_location[] = $group->group_id;
				}else{
					if(!empty($group_location) && ($group_location==$user_location))
						$groups_in_location[] = $group->group_id;
				}
			}
		}


		

		// $allgroups = $groups_query;

		// $groups_query = $groups_in_location;

		$args =
			array(
				'type'               => false,          // Active, newest, alphabetical, random, popular.
				'order'              => 'DESC',         // 'ASC' or 'DESC'
				'orderby'            => 'date_created', // date_created, last_activity, total_member_count, name, random, meta_id.
				'user_id'            => false,          // Pass a user_id to limit to only groups that this user is a member of.
				'include'            => $groups_in_location,          // Only include these specific groups (group_ids).
				'exclude'            => false,          // Do not include these specific groups (group_ids).
				'parent_id'          => null,           // Get groups that are children of the specified group(s).
				'slug'               => array(),        // Find a group or groups by slug.
				'search_terms'       => false,          // Limit to groups that match these search terms.
				'search_columns'     => array(),        // Select which columns to search.
				'group_type'         => !empty($groups_in_location)?'':'asd',             // Array or comma-separated list of group types to limit results to.
				'group_type__in'     => '',             // Array or comma-separated list of group types to limit results to.
				'group_type__not_in' => '',             // Array or comma-separated list of group types that will be excluded from results.
				'meta_query'         => false, 			// Filter by groupmeta. See WP_Meta_Query for syntax.
				'show_hidden'        => false,          // Show hidden groups to non-admins.
				'status'             => array(),        // Array or comma-separated list of group statuses to limit results to.
				'per_page'           => 20,             // The number of results to return per page.
				'page'               => 1,              // The page to return if limiting per page.
				'update_meta_cache'  => true,           // Pre-fetch groupmeta for queried groups.
				'update_admin_cache' => true,
				'fields'             => 'all',          // Return BP_Groups_Group objects or a list of ids.
			);

		global $groups_template;
		
		$groups_template = new BP_Groups_Template( $args );


		while ( $groups_template->groups() ) :
			$groups_template->the_group();

			$bp_group_id = bp_get_group_id();
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
							<a href="<?php bp_group_permalink(); ?>">
								<?php if ( ! empty( $group_cover_image_url ) ) { ?>
									<img src="<?php echo esc_url( $group_cover_image_url ); ?>">
								<?php } ?>
							</a>
						</div>
						<?php
					}

					if ( ! bp_disable_group_avatar_uploads() && bb_platform_group_element_enable( 'avatars' ) ) {
						?>
						<div class="item-avatar"><a href="<?php bp_group_permalink(); ?>" class="group-avatar-wrap"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a></div>
						<?php
					}
					?>
					<div class="item <?php echo esc_attr( $group_members . ' ' . $join_button ); ?>">

						<div class="group-item-wrap">
							<div class="item-block">
								<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>
								<div class="item-meta-wrap <?php echo esc_attr( bb_platform_group_element_enable( 'last-activity' ) || empty( $meta_privacy ) || empty( $meta_group_type ) ? 'has-meta' : 'meta-hidden' ); ?> ">

									<?php if ( bp_nouveau_group_has_meta() ) { ?>

										<p class="item-meta group-details <?php echo esc_attr( $meta_privacy . ' ' . $meta_group_type ); ?>">
											<?php
												$meta = bp_nouveau_get_group_meta();
												echo wp_kses_post( $meta['status'] );
											?>
										</p>
										<?php
									}

									if ( bb_platform_group_element_enable( 'last-activity' ) ) {
										echo '<p class="last-activity item-meta">' .
										sprintf(
												/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
											esc_attr__( 'Active %s', 'buddyboss' ),
											wp_kses_post( bp_get_group_last_active() )
										) .
										'</p>';
									}
									?>

								</div>
							</div>

							<?php if ( bb_platform_group_element_enable( 'group-descriptions' ) ) { ?>
								<div class="item-desc group-item-desc only-list-view"><?php bp_group_description_excerpt( false, 150 ); ?></div>
							<?php } ?>
						</div>

						<?php bp_nouveau_groups_loop_item(); ?>

						<div class="group-footer-wrap <?php echo esc_attr( $group_members . ' ' . $join_button ); ?>">
							<div class="group-members-wrap">
								<?php bb_groups_loop_members(); ?>
							</div>
							<?php if ( bb_platform_group_element_enable( 'join-buttons' ) ) { ?>
								<div class="groups-loop-buttons footer-button-wrap"><?php bp_nouveau_groups_loop_buttons(); ?></div>
							<?php } ?>
						</div>

					</div>
				</div>
			</li>

		<?php endwhile; ?>
	</ul>

	<!-- Leave Group confirmation popup -->
	<div class="bb-leave-group-popup bb-action-popup" style="display: none">
		<transition name="modal">
			<div class="modal-mask bb-white bbm-model-wrap">
				<div class="modal-wrapper">
					<div class="modal-container">
						<header class="bb-model-header">
							<h4><span class="target_name"><?php esc_html_e( 'Leave Group', 'buddyboss' ); ?></span></h4>
							<a class="bb-close-leave-group bb-model-close-button" href="#">
								<span class="bb-icon-l bb-icon-times"></span>
							</a>
						</header>
						<div class="bb-leave-group-content bb-action-popup-content">
							<p><?php esc_html_e( 'Are you sure you want to leave ', 'buddyboss' ); ?><span class="bb-group-name"></span>?</p>
						</div>
						<footer class="bb-model-footer flex align-items-center">
							<a class="bb-close-leave-group bb-close-action-popup" href="#"><?php esc_html_e( 'Cancel', 'buddyboss' ); ?></a>
							<a class="button push-right bb-confirm-leave-group" href="#"><?php esc_html_e( 'Confirm', 'buddyboss' ); ?></a>
						</footer>

					</div>
				</div>
			</div>
		</transition>
	</div> <!-- .bb-leave-group-popup -->

	<?php
	bp_nouveau_pagination( 'bottom' );
} else {
	bp_nouveau_user_feedback( 'groups-loop-none' );
}

bp_nouveau_after_loop();

remove_filter( 'bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2 );
