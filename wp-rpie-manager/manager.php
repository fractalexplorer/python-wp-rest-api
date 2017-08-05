<?php
/*
Plugin Name: Rpie Manager
Plugin URI: 
Description: To Manage Rpie
Author: knoxweb
Version: 1.0
Author URI: 
*/


add_action( 'init', 'rpi_post_init' );
/**
 * Register a book post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function rpi_post_init() {
	$labels = array(
		'name'               => _x( 'Rpi Messages', 'post type general name', 'rpie-manager' ),
		'singular_name'      => _x( 'Rpi Message', 'post type singular name', 'rpie-manager' ),
		'menu_name'          => _x( 'Rpi Messages', 'admin menu', 'rpie-manager' ),
		'name_admin_bar'     => _x( 'Rpi Message', 'add new on admin bar', 'rpie-manager' ),
		'add_new'            => _x( 'Add New', 'Rpi Message', 'rpie-manager' ),
		'add_new_item'       => __( 'Add New Rpi Message', 'rpie-manager' ),
		'new_item'           => __( 'New Rpi Message', 'rpie-manager' ),
		'edit_item'          => __( 'Edit Rpi Message', 'rpie-manager' ),
		'view_item'          => __( 'View Rpi Message', 'rpie-manager' ),
		'all_items'          => __( 'All Rpi Messages', 'rpie-manager' ),
		'search_items'       => __( 'Search Rpi Messages', 'rpie-manager' ),
		'parent_item_colon'  => __( 'Parent Rpi Messages:', 'rpie-manager' ),
		'not_found'          => __( 'No Rpi messages found.', 'rpie-manager' ),
		'not_found_in_trash' => __( 'No Rpi messages found in Trash.', 'rpie-manager' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'rpie-manager' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'rpie-message' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author' )// 'thumbnail', 'excerpt', 'comments'
	);

	register_post_type( 'rpi-message', $args );
}


add_filter( 'page_template', 'set_rpie_template_page' );
function set_rpie_template_page( $page_template )
{
    if ( is_page( 'manage-raspberry-pi' ) ) {
        $page_template = dirname( __FILE__ ) . '/rpie-template.php';
    }
    return $page_template;
}


add_action( 'wp_ajax_nopriv_post_call_for_python', 'post_call_for_python' );
add_action( 'wp_ajax_post_call_for_python', 'post_call_for_python' );

function post_call_for_python() {
	if (is_super_admin())
	{
		$mime_type = array( 'image/jpeg','image/png' );
		$type = $_FILES["pinImage"]["type"];

		foreach ($type as $key => $value) {
			if($value){
				if( !in_array($value, $mime_type) ){
					echo 'Only .jpg, .jpeg, .png extension accepted.'; exit;
				}	
			}
		}

		$uploads = wp_upload_dir();
		$UploadFolder = "/rpie/";
		$file_data = json_decode(get_option('python_pin_uploads'),true);
		
		foreach($_FILES["pinImage"]["tmp_name"] as $key=>$tmp_name){
			$temp = $_FILES["pinImage"]["tmp_name"][$key];
			$name = $_FILES["pinImage"]["name"][$key];
			if($name){
				if(move_uploaded_file($temp,$uploads['basedir'].$UploadFolder.$name)){
					$file_data[$key] = $name;	
    			}	
			}
		}	
		
		update_option( 'python_pin_uploads', json_encode($file_data) );  	
		update_option( 'python_button_clicked', $_POST['message'] );
		update_option( 'python_button_time', $_POST['time'] );
		update_option( 'python_button_output_pin', $_POST['output_pin'] );
		update_option( 'python_button_speed', $_POST['speed'] );
		update_option( 'twitter_fetch_hash_tag', $_POST['hashtag'] );
		update_option( 'rpi_mode', $_POST['rpi_mode'] );
		update_option( 'twitter_mode', $_POST['twitter_mode'] );

		echo "Raspberry Pi settings saved successfully.";exit;
	}
}


add_action( 'rest_api_init', function () {
  register_rest_route( 'rest/v1', '/get_python_message', array(
    'methods' => 'GET',
    'callback' => 'get_python_message',
  ) );
  register_rest_route( 'rest/v1', '/add_message_post', array(
    'methods' => 'PSOT',
    'callback' => 'add_message_post',
  ) );
} );


function get_python_message( WP_REST_Request $request )
{
	$params = $request->get_params();
	$output = array();
	$output['code'] = "0";
	$output['message'] = get_option( 'python_button_clicked');
	$output['time'] = (int)get_option( 'python_button_time');
	$output['output_pin'] = (int)get_option( 'python_button_output_pin');
	$output['speed'] = (double)get_option( 'python_button_speed');
	$output['hashtag'] = get_option( 'twitter_fetch_hash_tag');
	$output['twitter_mode'] = get_option( 'twitter_mode');
	$output['rpi_mode'] = get_option( 'rpi_mode');
	

	$response = new WP_REST_Response( $output );
	return $response;
}

function add_message_post( WP_REST_Request $request )
{
	$params = $request->get_params();
	
	$content = $params['message'];
	$terminal_id = $params['terminal_id'];

	// Create post object
	$my_post = array(
		'post_title'    => 'Message overridden by Raspberry Pi terminal - '.$terminal_id,
		'post_content'  => $content,
		'post_status'   => 'publish',
		'post_author'   => 1,
	);
	// Insert the post into the database
	$new_post_id = wp_insert_post( $my_post );
	update_post_meta($new_post_id,'terminal_id',$terminal_id);

	$output = array();
	$output['code'] = "0";
	$output['message'] = "success";
	$output['id'] = $new_post_id;

	$response = new WP_REST_Response( $output );
	return $response;
}