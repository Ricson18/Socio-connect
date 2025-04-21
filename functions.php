<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  

add_action( 'wp_enqueue_scripts', 'buddyboss_theme_child_style' );
				function buddyboss_theme_child_style() {
					wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
					wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
				}


/**
 * Your code goes below.
 */

//Buddyboss
function update_group_location($group_id, $member, $group){
	if(isset($_POST['group-location']))
		groups_update_groupmeta( $group_id, 'group-location', $_POST['group-location'] );
}

add_action( 'groups_create_group', 'update_group_location', 10, 3);


function update_group_location_after_edit($group_id, $old_group, $notify_members){
	if(isset($_POST['group-location']))
		groups_update_groupmeta( $group_id, 'group-location', $_POST['group-location'] );
}

add_action( 'groups_details_updated', 'update_group_location_after_edit', 10, 3 );




//TutorLMS
function tlcf_enqueue_scripts() {
	$js_url = get_stylesheet_directory_uri() . '/custom-fields.js';	
	wp_enqueue_script('tlcf-custom-fields-js', $js_url, array( 'tutor-course-builder' ), '1.0.0', true );
}
add_action( 'tutor_after_course_builder_load', 'tlcf_enqueue_scripts' );

function tlcf_save_course_meta( int $post_id ) {
	$course_location = sanitize_text_field( wp_unslash( $_POST['course_location'] ) ?? '' );
	if ( $course_location ) {
		update_post_meta( $post_id, 'tlcf_course_location', $course_location );
	}
}

add_action( 'save_post_courses', 'tlcf_save_course_meta' );

function add_location_to_course($default_course_sidebar_meta, $course_id){
	// $course_id   = $data['ID'];
	$course_location = get_post_meta( $course_id, 'tlcf_course_location', true );
	if ( $course_location ) {
		$default_course_sidebar_meta[] = [
			'icon_class' => 'fa fa-map-marker fa-lg fa-fw',
			'label' => 'Location',
			'value' => $course_location
		];
	}

	return $default_course_sidebar_meta;
}
add_filter( 'tutor/course/single/sidebar/metadata', 'add_location_to_course', 10, 2 );




function global_location_changer(){ 
	$locations = socio_connect_get_locations();
    $location_label = socio_connect_get_location_label();
	?>

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Get the current URL parameters
			const urlParams = new URLSearchParams(window.location.search);
			// Set the dropdown to match the current location parameter if it exists
			if(urlParams.has('location')) {
				const currentLocation = urlParams.get('location');
				$('#tribe-events-location-filter').val(currentLocation);
			}			
			// Add change event listener to the location dropdown
			$('#tribe-events-location-filter').on('change', function() {
				const selectedLocation = $(this).val();
				// Get current URL and parse its parameters
				const url = new URL(window.location.href);
				const params = new URLSearchParams(url.search);
				
				// Update or add the location parameter
				if(selectedLocation === 'all') {
					params.delete('location');
				} else {
					params.set('location', selectedLocation);
				}
				
				// Update the URL with the new parameters and reload the page
				url.search = params.toString();
				window.location.href = url.toString();
			});
		});
	</script>

	<label style="margin-left:5px"><?php echo esc_html($location_label); ?></label>

	<select name="locations" id="tribe-events-location-filter" style="width:100%;border-radius:10px">
        <option>---</option>
        <option value="All">All</option>
        <?php foreach ($locations as $key => $value) : ?>
            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($value); ?></option>
        <?php endforeach; ?>
    </select>

	<!-- <label style="margin-left:5px">Locations</label> -->
	<!-- <select name="locations" id="tribe-events-location-filter" style="width:100%;border-radius:10px">
		<option>---</option>
		<option>All</option>
		<option>Canada</option>
		<option>United States</option>
		<option>Las Vegas</option>
		<option>Miami</option>
	</select> -->
<?php }


add_shortcode('global_location_changer', 'global_location_changer');




/**
 * Location Management Admin Menu
 */
function socio_connect_locations_menu() {
    add_menu_page(
        'Location Management',
        'Locations',
        'manage_options',
        'socio-connect-locations',
        'socio_connect_locations_page',
        'dashicons-location',
        30
    );
}
add_action('admin_menu', 'socio_connect_locations_menu');

/**
 * Register settings for locations
 */
function socio_connect_register_settings() {
    register_setting('socio_connect_locations_group', 'socio_connect_locations');
    register_setting('socio_connect_locations_group', 'socio_connect_location_label');
}
add_action('admin_init', 'socio_connect_register_settings');




/**
 * Admin page for location management
 */
function socio_connect_locations_page() {

    $assigned_index = 4;
    // Process form submissions
    if (isset($_POST['socio_connect_add_location']) && isset($_POST['location_value'])) {
    // if (isset($_POST['socio_connect_add_location']) && isset($_POST['location_key']) && isset($_POST['location_value'])) {
        if (check_admin_referer('socio_connect_locations_action', 'socio_connect_locations_nonce')) {
            $locations = get_option('socio_connect_locations', array());
            $key = sanitize_text_field($_POST['location_value']);
            // $key = sanitize_text_field($_POST['location_key']);
            $value = sanitize_text_field($_POST['location_value']);
            

            if (!empty($key) && !empty($value)) {
                $locations[$key] = $value;
                update_option('socio_connect_locations', $locations);


                $label = get_option('socio_connect_location_label');
                $values = implode("\r\n",array_values($locations));

                $ecp_options = Tribe__Settings_Manager::get_options();


                
                $ecp_options['custom-fields'][$assigned_index] = [
                    'name'   => '_ecp_custom_' . $assigned_index,
                    'label'  => $label,
                    'type'   => 'dropdown',
                    'values' => $values//"A1\r\nA5\r\nA3\r\nA4"
                ];

                Tribe__Settings_Manager::set_options( $ecp_options );



                echo '<div class="notice notice-success is-dismissible"><p>Location added successfully!</p></div>';
            }
        }
    }
    
    if (isset($_POST['socio_connect_update_label'])) {
        if (check_admin_referer('socio_connect_locations_action', 'socio_connect_locations_nonce')) {
            $label = sanitize_text_field($_POST['location_label']);
            update_option('socio_connect_location_label', $label);



            $locations = get_option('socio_connect_locations', array());
            
            $values = implode("\r\n",array_values($locations));

            $ecp_options = Tribe__Settings_Manager::get_options();


            // $assigned_index = 4;
            $ecp_options['custom-fields'][$assigned_index] = [
                'name'   => '_ecp_custom_' . $assigned_index,
                'label'  => $label,
                'type'   => 'dropdown',
                'values' => $values//"A1\r\nA5\r\nA3\r\nA4"
            ];

            Tribe__Settings_Manager::set_options( $ecp_options );




            echo '<div class="notice notice-success is-dismissible"><p>Location label updated successfully!</p></div>';
        }
    }
    
    if (isset($_POST['socio_connect_delete_location']) && isset($_POST['delete_key'])) {
        if (check_admin_referer('socio_connect_locations_action', 'socio_connect_locations_nonce')) {
            $locations = get_option('socio_connect_locations', array());
            $key = sanitize_text_field($_POST['delete_key']);
            
            if (isset($locations[$key])) {
                unset($locations[$key]);
                update_option('socio_connect_locations', $locations);



                $label = get_option('socio_connect_location_label');
                $locations = get_option('socio_connect_locations', array());
            
                $values = implode("\r\n",array_values($locations));

                $ecp_options = Tribe__Settings_Manager::get_options();


                // $assigned_index = 4;
                $ecp_options['custom-fields'][$assigned_index] = [
                    'name'   => '_ecp_custom_' . $assigned_index,
                    'label'  => $label,
                    'type'   => 'dropdown',
                    'values' => $values//"A1\r\nA5\r\nA3\r\nA4"
                ];

                Tribe__Settings_Manager::set_options( $ecp_options );



                echo '<div class="notice notice-success is-dismissible"><p>Location deleted successfully!</p></div>';
            }
        }
    }



	if (isset($_POST['socio_connect_edit_location']) && isset($_POST['edit_value'])) {
	// if (isset($_POST['socio_connect_edit_location']) && isset($_POST['edit_key']) && isset($_POST['edit_value'])) {
        if (check_admin_referer('socio_connect_locations_action', 'socio_connect_locations_nonce')) {
            $locations = get_option('socio_connect_locations', array());
            $old_key = sanitize_text_field($_POST['original_key']);
            $new_key = sanitize_text_field($_POST['edit_value']);
            $new_value = sanitize_text_field($_POST['edit_value']);
            
            if (isset($locations[$old_key]) && !empty($new_key) && !empty($new_value)) {
                // Remove old key
                unset($locations[$old_key]);
                // Add with new key/value
                $locations[$new_key] = $new_value;
                update_option('socio_connect_locations', $locations);



                $label = get_option('socio_connect_location_label');
            
                $values = implode("\r\n",array_values($locations));

                $ecp_options = Tribe__Settings_Manager::get_options();


                // $assigned_index = 4;
                $ecp_options['custom-fields'][$assigned_index] = [
                    'name'   => '_ecp_custom_' . $assigned_index,
                    'label'  => $label,
                    'type'   => 'dropdown',
                    'values' => $values//"A1\r\nA5\r\nA3\r\nA4"
                ];

                Tribe__Settings_Manager::set_options( $ecp_options );





                echo '<div class="notice notice-success is-dismissible"><p>Location updated successfully!</p></div>';
            }
        }
    }
    
    $locations = get_option('socio_connect_locations', array());
    $location_label = get_option('socio_connect_location_label', 'Location');
    ?>
    <div class="wrap">
        <h1>Location Management</h1>
        
        <div class="card">
            <h2>Change Location Label</h2>
            <form method="post" action="">
                <?php wp_nonce_field('socio_connect_locations_action', 'socio_connect_locations_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Label Name</th>
                        <td>
                            <input type="text" name="location_label" value="<?php echo esc_attr($location_label); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="socio_connect_update_label" class="button-primary" value="Update Label">
                </p>
            </form>
        </div>
        
        <div class="card">
            <h2>Add New Location</h2>
            <form method="post" action="">
                <?php wp_nonce_field('socio_connect_locations_action', 'socio_connect_locations_nonce'); ?>
                <table class="form-table">
                    <!-- <tr>
                        <th scope="row">Location Key</th>
                        <td>
                            <input type="text" name="location_key" class="regular-text" required>
                            <p class="description">This is used internally (should be unique)</p>
                        </td>
                    </tr> -->
                    <tr>
                        <th scope="row">Location Name</th>
                        <td>
                            <input type="text" name="location_value" class="regular-text" required>
                            <p class="description">This is what users will see</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="socio_connect_add_location" class="button-primary" value="Add Location">
                </p>
            </form>
        </div>
        
        <div class="card">
            <h2>Manage Existing Locations</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Location Key</th>
                        <th>Location Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($locations)) : ?>
                        <tr>
                            <td colspan="3">No locations found.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($locations as $key => $value) : ?>
                            <tr>
                                <td><?php echo esc_html($key); ?></td>
                                <td><?php echo esc_html($value); ?></td>
                                <td>
                                    <button type="button" class="button edit-location" 
                                            data-key="<?php echo esc_attr($key); ?>" 
                                            data-value="<?php echo esc_attr($value); ?>">
                                        Edit
                                    </button>
                                    <form method="post" action="" style="display:inline;">
                                        <?php wp_nonce_field('socio_connect_locations_action', 'socio_connect_locations_nonce'); ?>
                                        <input type="hidden" name="delete_key" value="<?php echo esc_attr($key); ?>">
                                        <button type="submit" name="socio_connect_delete_location" class="button" 
                                                onclick="return confirm('Are you sure you want to delete this location?');">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Edit Location Modal -->
        <div id="edit-location-modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; 
                                            overflow:auto; background-color:rgba(0,0,0,0.4); padding-top:100px;">
            <div style="background-color:#fefefe; margin:auto; padding:20px; border:1px solid #888; width:50%;">
                <span id="close-modal" style="color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
                <h2>Edit Location</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('socio_connect_locations_action', 'socio_connect_locations_nonce'); ?>
                    <input type="hidden" id="original_key" name="original_key" value="">
                    <table class="form-table">
                        <!-- <tr>
                            <th scope="row">Location Key</th>
                            <td>
                                <input type="text" id="edit_key" name="edit_key" class="regular-text" required>
                            </td>
                        </tr> -->
                        <tr>
                            <th scope="row">Location Name</th>
                            <td>
                                <input type="text" id="edit_value" name="edit_value" class="regular-text" required>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="socio_connect_edit_location" class="button-primary" value="Update Location">
                    </p>
                </form>
            </div>
        </div>

		
		<script>
			jQuery(document).ready(function($) {
				// Edit location functionality
				$('.edit-location').click(function() {
					var key = $(this).data('key');
					var value = $(this).data('value');
					
					$('#original_key').val(key);
					// $('#edit_key').val(key);
					$('#edit_value').val(value);
					$('#edit-location-modal').show();
				});
				
				// Close modal
				$('#close-modal').click(function() {
					$('#edit-location-modal').hide();
				});
				
				// Close modal when clicking outside
				$(window).click(function(event) {
					if ($(event.target).is('#edit-location-modal')) {
						$('#edit-location-modal').hide();
					}
				});
			});
        </script>

	</div>

<?php
}


/**
 * Get all locations for use in other parts of the theme
 */
function socio_connect_get_locations() {
    return get_option('socio_connect_locations', array(
        'Canada' => 'Canada',
        'United States' => 'United States',
        'Las Vegas' => 'Las Vegas',
        'Miami' => 'Miami'
    ));
}

/**
 * Get location label
 */
function socio_connect_get_location_label() {
    return get_option('socio_connect_location_label', 'Location');
}
