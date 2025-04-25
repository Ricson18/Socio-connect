<?php
/**
 * Template for course archive init
 *
 * @package Tutor\Templates
 * @subpackage CourseArchive
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

use TUTOR\Input;


$current_page = $_GET[ 'current_page'];

! isset( $course_filter ) ? $course_filter         = false : 0;
! isset( $supported_filters ) ? $supported_filters = tutor_utils()->get_option( 'supported_course_filters', array() ) : 0;
! isset( $loop_content_only ) ? $loop_content_only = false : 0;
! isset( $column_per_row ) ? $column_per_row       = tutor_utils()->get_option( 'courses_col_per_row', 3 ) : 0;
! isset( $course_per_page ) ? $course_per_page     = tutor_utils()->get_option( 'courses_per_page', 12 ) : 0;
! isset( $show_pagination ) ? $show_pagination     = true : 0;
! isset( $current_page ) ? $current_page           = 1 : 0;


	// Set in global variable to avoid too many stack to pass to other templates.
	$GLOBALS['tutor_course_archive_arg'] = compact(
		'course_filter',
		'supported_filters',
		'loop_content_only',
		'column_per_row',
		'course_per_page',
		'show_pagination'
	);

	// Render the loop.
	ob_start();

	do_shortcode('[global_location_changer]');

	?> 

	<div style="margin-bottom:30px"></div>

	<?php


	do_action( 'tutor_course/archive/before_loop' );




	if ( ( isset( $the_query ) && $the_query->have_posts() ) || have_posts() ) {
		/* Start the Loop */

// echo "<pre>";
// echo(have_posts());
// echo "</pre>";

global $wpdb;

$all_courses_query = $wpdb->prepare(
    "SELECT ID, post_title, post_content, post_author 
    FROM {$wpdb->posts} 
    WHERE post_type = %s 
    AND post_status = %s 
    ORDER BY post_date DESC",
    'courses',
    'publish'
);



// echo ($current_page - 1) * 12;

$courses_query = $wpdb->prepare(
    "SELECT ID, post_title, post_content, post_author 
    FROM {$wpdb->posts} 
    WHERE post_type = %s 
    AND post_status = %s 
    ORDER BY post_date DESC 
    LIMIT %d OFFSET %d",
    'courses',
    'publish',
    $course_per_page,
    ($current_page - 1) * 12
);

// echo "$current_page - 1";


// exit;

// $courses = $wpdb->get_results($courses_query);
// $courses = $wpdb->get_results($courses_query);

// print_r($courses);

$courses_ids = $wpdb->get_col($courses_query);
$all_courses_ids = $wpdb->get_col($all_courses_query);


// $user_location = xprofile_get_field_data('location',get_current_user_id());	
$location_label = socio_connect_get_location_label();
$user_location = xprofile_get_field_data($location_label,get_current_user_id());

// echo "$course_location == $user_location";


// if($user_location !== 'All'){
// 	$courses_in_location_ids = [];
// 	foreach($courses_ids as $course_id){
// 		$course_location = get_post_meta( $course_id, 'tlcf_course_location', true );
// 		if(!empty($course_location) && ($course_location == $user_location))
// 			$courses_in_location_ids[] = $course_id;
// 	}

// 	$all_courses_in_location_ids = [];
// 	foreach($all_courses_ids as $course_id){
// 		$course_location = get_post_meta( $course_id, 'tlcf_course_location', true );
// 		if(!empty($course_location) && ($course_location == $user_location))
// 			$all_courses_in_location_ids[] = $course_id;
// 	}
// }else{
// 	$courses_in_location_ids = $courses_ids;
// 	$all_courses_in_location_ids = $all_courses_ids;
// }


	$temp = [];
	foreach($all_courses_ids as $course_id){
		$course_location = get_post_meta( $course_id, 'course_location', true );
		if( is_array($course_location) ){
			if( !empty($course_location) && in_array($user_location,$course_location) )
				$temp[] = $course_id;
		}else{
			if(!empty($course_location) && ($course_location == $user_location))
				$temp[] = $course_id;
		}
	}

	$courses_in_location_ids = [];

	if(isset($_GET['location']) && ($_GET['location'] === '---')){
		foreach($courses_ids as $course_id){
			$course_location = get_post_meta( $course_id, 'course_location', true );

			if( is_array($course_location) ){
				if( !empty($course_location) && in_array($user_location,$course_location) )
					$courses_in_location_ids[] = $course_id;
			}else{
				if(!empty($course_location) && ($course_location==$user_location))
					$courses_in_location_ids[] = $course_id;
			}
		}

		$all_courses_in_location_ids = $temp;
	}else if(isset($_GET['location']) && ($_GET['location'] === 'All')){
		foreach($courses_ids as $course_id)
			$courses_in_location_ids[] = $course_id;

		$all_courses_in_location_ids = $all_courses_ids;
	}else if(isset($_GET['location'])){
		foreach($courses_ids as $course_id){
			$course_location = get_post_meta( $course_id, 'course_location', true );
			if( is_array($course_location) ){
				if( !empty($course_location) && in_array($_GET['location'],$course_location) )
					$courses_in_location_ids[] = $course_id;
			}else{
				if( $_GET['location'] === $course_location)
					$courses_in_location_ids[] = $course_id;
			}
		}
		
		$all_courses_in_location_ids = $temp;
	}else{
		foreach($courses_ids as $course_id){
			$course_location = get_post_meta( $course_id, 'course_location', true );
			if( is_array($course_location) ){
				if( !empty($course_location) && in_array($user_location,$course_location) )
					$courses_in_location_ids[] = $course_id;
			}else{
				if(!empty($course_location) && ($course_location==$user_location))
					$courses_in_location_ids[] = $course_id;
			}
		}

		$all_courses_in_location_ids = $temp;
	}


	// if(!isset($_GET['location'])){
	// 	$all_courses_in_location_ids = [];
	// 	foreach($all_courses_ids as $course_id){
	// 		$course_location = get_post_meta( $course_id, 'tlcf_course_location', true );
	// 		if(!empty($course_location) && ($course_location == $user_location))
	// 			$all_courses_in_location_ids[] = $course_id;
	// 	}
	// }else{
	// 	$all_courses_in_location_ids = $all_courses_ids;
	// }

	// $courses_in_location_ids = $courses_ids;
	// $all_courses_in_location_ids = $all_courses_ids;



	// print_r($courses_in_location_ids);

	// if($user_location === 'All'){
	// }

	$per_page = 12;

	// echo "Total number of courses: ". count($all_courses_in_location_ids);

	// echo "<br>";
	// echo "Total number of pages: ". 
	$pages_count = ceil(count($all_courses_in_location_ids) / $per_page);
	// echo "<br>";
	// echo "Remaining courses after displayed courses: ". (count($all_courses_in_location_ids) - ($per_page*$current_page));



	// if(($_GET['current_page']==1) && 
	// echo "pages_count: ".$pages_count = ceil( (count($all_courses_in_location_ids) / $per_page) ) - $current_page;
	//){

	// }



	// echo (count($all_courses_ids)." / $per_page");

	// $pages_count = count($all_courses_ids) / $per_page;

	// echo "pages_count: $pages_count";
	$pages_count < 2 ? $show_pagination = false : 0;

	if(!empty($courses_in_location_ids)){
		$the_query = new WP_Query(array(
			'post__in' => $courses_in_location_ids,
			'post_type' => 'courses',
			'post_status' => 'publish',
			'posts_per_page' => $course_per_page,
			'orderby' => 'post__in',
			// 'paged' => $current_page
		));


		tutor_course_loop_start();

		while ( $the_query->have_posts()) {
		// while ( isset( $the_query ) ? $the_query->have_posts() : have_posts() ) {
			isset( $the_query ) ? $the_query->the_post() : the_post();

			/**
			 * Usage Idea, you may keep a loop within a wrap, such as bootstrap col
			 *
			 * @hook tutor_course/archive/before_loop_course
			 * @type action
			 */
			do_action( 'tutor_course/archive/before_loop_course' );

			tutor_load_template( 'loop.course' );

			/**
			 * Usage Idea, If you start any div before course loop, you can end it here, such as </div>
			 *
			 * @hook tutor_course/archive/after_loop_course
			 * @type action
			 */
			do_action( 'tutor_course/archive/after_loop_course' );
		}

		tutor_course_loop_end();
	} else {
		$the_query = [];
		tutor_utils()->tutor_empty_state( tutor_utils()->not_found_text() );
	} 
		
}else {

		/**
		 * No course found
		 */
		tutor_utils()->tutor_empty_state( tutor_utils()->not_found_text() );
	}

	do_action( 'tutor_course/archive/after_loop' );

	if ( $show_pagination ) {
		global $wp_query;

		$current_url = wp_doing_ajax() ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ?? '' ) ) : tutor()->current_url;
		//phpcs:disable WordPress.Security.NonceVerification.Missing
		$push_link = add_query_arg( array_merge( $_POST, $GLOBALS['tutor_course_archive_arg'] ), $current_url );

		$data            = wp_doing_ajax() ? Input::sanitize_array( $_POST ) : Input::sanitize_array( $_GET );
		$pagination_data = array(
			'total_page' => $pages_count, //isset( $the_query ) ? $the_query->max_num_pages : $wp_query->max_num_pages,
			'per_page'   => $course_per_page,
			'paged'      => $current_page,
			'data_set'   => array( 'push_state_link' => $push_link ),
			'ajax'       => array_merge(
				$data,
				array(
					'loading_container' => '.tutor-course-filter-loop-container',
					'action'            => 'tutor_course_filter_ajax',
					'course_per_page'   => $course_per_page,
					'column_per_row'    => $column_per_row,
				)
			),
		);
	?>

	<nav class="tutor-pagination tutor-mt-40" style="display: flex;">
		<div class="tutor-pagination-hints">
			<div class="tutor-fs-7 tutor-color-black-60">
				Page 
				<span class="tutor-fs-7 tutor-fw-medium tutor-color-black">
					<?php echo esc_html($current_page); ?>				
				</span>
				of 
				<span class="tutor-fs-7 tutor-fw-medium tutor-color-black">
					<?php echo esc_html($pages_count); ?></span>
			</div>
		</div>
		<ul class="tutor-pagination-numbers">
			<?php
			if($current_page > 1) {
				echo '<a class="prev page-numbers" href="' . esc_url(add_query_arg('current_page', ($current_page - 1), $current_url)) . '"><span class="tutor-icon-angle-left"></span></a>';
			}
			
			for($i = 1; $i <= $pages_count; $i++) {
				if($i == $current_page) {
					echo '<span aria-label="Page ' . esc_attr($i) . '" aria-current="page" class="page-numbers current">' . esc_html($i) . '</span>';
				} else {
					echo '<a aria-label="Page ' . esc_attr($i) . '" class="page-numbers" href="' . '?location='.$_GET['location'].'&current_page='.$i.'">' . esc_html($i) . '</a>';
				}
			}

			if($current_page < $pages_count) {
				echo '<a class="next page-numbers" href="'.'?location='.$_GET['location'].'&' . 'current_page='.($current_page + 1) . '"><span class="tutor-icon-angle-right"></span></a>';
				// echo '<a class="next page-numbers" href="'.'?location='.$_GET['location'].'&' . esc_url(add_query_arg('current_page', ($current_page + 1), $current_url)) . '"><span class="tutor-icon-angle-right"></span></a>';
			}
			?>		
		</ul>
	</nav>	
	
	
	
	
	
	
	
	
	<!-- <nav class="tutor-pagination tutor-mt-40" style="display: flex;">
		<div class="tutor-pagination-hints">
			<div class="tutor-fs-7 tutor-color-black-60">
				Page 
				<span class="tutor-fs-7 tutor-fw-medium tutor-color-black">
					1				
				</span>
				of 
				<span class="tutor-fs-7 tutor-fw-medium tutor-color-black">
					2				</span>
			</div>
		</div>
		<ul class="tutor-pagination-numbers">
			<span aria-label="Page 1" aria-current="page" class="page-numbers current">1</span>
			<a aria-label="Page 2" class="page-numbers" href="https://purposeos.socio-connect.com/wp-admin/admin-ajax.php/?current_page=2">2</a>
			<a class="next page-numbers" href="https://purposeos.socio-connect.com/wp-admin/admin-ajax.php/?current_page=2"><span class="tutor-icon-angle-right"></span></a>		
		</ul>
	</nav> -->



	<?php
		
		// tutor_load_template_from_custom_path(
		// 	tutor()->path . 'templates/dashboard/elements/pagination.php',
		// 	$pagination_data
		// );
	}

	$course_loop = ob_get_clean();

	if ( isset( $loop_content_only ) && true == $loop_content_only ) {
		echo $course_loop; //phpcs:ignore --$course_loop contain sanitized data
		return;
	}

	$course_archive_arg = isset( $GLOBALS['tutor_course_archive_arg'] ) ? $GLOBALS['tutor_course_archive_arg']['column_per_row'] : null;
	$columns            = null === $course_archive_arg ? tutor_utils()->get_option( 'courses_col_per_row', 3 ) : $course_archive_arg;
	$has_course_filters = $course_filter && count( $supported_filters );

	$supported_filters_keys = array_keys( $supported_filters );
	?>

<div class="tutor-wrap tutor-wrap-parent tutor-courses-wrap tutor-container course-archive-page" data-tutor_courses_meta="<?php echo esc_attr( json_encode( $GLOBALS['tutor_course_archive_arg'] ) ); ?>">
	<?php if ( $has_course_filters ) : ?>
		<div class="tutor-d-block tutor-d-xl-none tutor-mb-32">
			<div class="tutor-d-flex tutor-align-center tutor-justify-between">
				<span class="tutor-fs-3 tutor-fw-medium tutor-color-black"><?php esc_html_e( 'Courses', 'tutor' ); ?></span>
				<a href="#" class="tutor-iconic-btn tutor-iconic-btn-secondary tutor-iconic-btn-md" tutor-toggle-course-filter><span class="tutor-icon-slider-vertical"></span></a>
			</div>
		</div>
	<?php endif; ?>

	<div class="tutor-row tutor-gx-xl-5">
		<?php if ( $has_course_filters ) : ?>
			<div class="tutor-col-3 tutor-course-filter-container">
				<div class="tutor-course-filter" tutor-course-filter>
					<?php tutor_load_template( 'course-filter.filters', array( 'supported_filters' => $supported_filters ) ); ?>
				</div>
			</div>

			<!-- <?php if ( $columns < 3 ) : ?>
				<div class="tutor-col-1 tutor-d-none tutor-d-xl-block" area-hidden="true"></div>
			<?php endif; ?> -->

			<div class="tutor-col-xl-<?php echo $columns < 3 ? 8 : 9; ?> ">
				<!-- <div> -->
					<?php tutor_load_template( 'course-filter.course-archive-filter-bar' ); ?>
				<!-- </div> -->
				<div class="tutor-pagination-wrapper-replaceable" tutor-course-list-container>
					<?php echo $course_loop; //phpcs:ignore --$course_loop contain sanitized data ?> 
				</div>
			</div>
		<?php else : ?>
			<div class="tutor-col-12">
				<div class="">
					<?php tutor_load_template( 'course-filter.course-archive-filter-bar' ); ?>
				</div>
				<div class="tutor-pagination-wrapper-replaceable" tutor-course-list-container>
					<?php echo $course_loop; //phpcs:ignore --$course_loop contain sanitized data ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php if ( isset( $course_filter_category ) ) : ?>
<input type="hidden" id="course_filter_categories" value="<?php echo esc_html( $course_filter_category ); ?>"></input>
<?php endif; ?>

<?php if ( isset( $course_filter_exclude_ids ) ) : ?>
<input type="hidden" id="course_filter_exclude_ids" value="<?php echo esc_html( $course_filter_exclude_ids ); ?>"></input>
<?php endif; ?>

<?php if ( isset( $course_filter_post_ids ) ) : ?>
<input type="hidden" id="course_filter_post_ids" value="<?php echo esc_html( $course_filter_post_ids ); ?>"></input>
<?php endif; ?>

<?php
if ( ! is_user_logged_in() ) {
	tutor_load_template_from_custom_path( tutor()->path . '/views/modal/login.php' );
}
?>
