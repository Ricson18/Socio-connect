<?php
/**
 * BP Nouveau Group's edit details template.
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/single/admin/edit-details.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 */

$bp_is_group_create = bp_is_group_create();
if ( $bp_is_group_create ) : ?>

	<h3 class="bp-screen-title creation-step-name">
		<?php esc_html_e( 'Enter Group Name &amp; Description', 'buddyboss' ); ?>
	</h3>

<?php else : ?>

	<h2 class="bp-screen-title">
		<?php esc_html_e( 'Edit Group Name &amp; Description', 'buddyboss' ); ?>
	</h2>

<?php endif; ?>

<label for="group-name"><?php esc_html_e( 'Group Name (required)', 'buddyboss' ); ?></label>
<input type="text" name="group-name" id="group-name" value="<?php $bp_is_group_create ? bp_new_group_name() : bp_group_name_editable(); ?>" aria-required="true" />

<label for="group-desc"><?php esc_html_e( 'Group Description', 'buddyboss' ); ?></label>
<textarea name="group-desc" id="group-desc" aria-required="true"><?php $bp_is_group_create ? bp_new_group_description() : bp_group_description_editable(); ?></textarea>

<?php 
    $location_label = socio_connect_get_location_label();
?>

<label for="group-location"><?php esc_html_e( "Group $location_label", 'buddyboss' ); ?></label>

<?php
	$bp   = buddypress();
	$ggroup_id = isset( $bp->groups->current_group->id )
		? $bp->groups->current_group->id
		: '';
		
	$group_location = groups_get_groupmeta( $ggroup_id, 'group-location');

	$all_locations = socio_connect_get_locations();

	// $all_locations = [
	// 	'Canada' => "Canada",
	// 	'United States' => "United States",
	// 	'Las Vegas' => "Las Vegas",
	// 	'Miami' => "Miami"
	// ]; 
?>


<div class="group-location-checkboxes">
	<div class="checkbox-label"><?php esc_html_e( 'Select Group Location', 'buddyboss' ); ?></div>
	<?php foreach($all_locations as $key=>$location){ ?>
		<label class="location-checkbox">
			<input type="checkbox" name="group-location[]" value="<?php echo $location; ?>" <?php echo ($key == $group_location)?"checked":"" ?>>
			<?php echo $location; ?>
		</label>
	<?php } ?>
</div>


<!-- <select name="group-location" id="group-location">
	<option value=""><!?php esc_html_e( "Select Group $location_label", 'buddyboss' ); ?></option>
	<!?php foreach($all_locations as $key=>$location){ ?>
		<option value="<!?php echo $location; ?>" <!?php echo ($key == $group_location)?"selected":"" ?>><!?php echo $location; ?></option>
	<!?php } ?>
</select> -->