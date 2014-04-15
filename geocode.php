<?php

/** BD map-pins auto generation
DIRECTIONS:
  *To Setup Wordpress Business Directory for this function
   -Navigate to Directory Admin in WP Dashboard
   -Navigate to Manage Form Fields
   -Click 'Add New Form Field'
   -Repeat to add fields 'Address', 'Zip', 'City', 'Country'

  *To use this function
   -Create child theme in WP-Content 
   -Copy this file into child theme folder
**/


// Markers Function - pulls BD addresses and creates Map Markers 
function addMarkers() {		

	global $wpdb;
	$wpdb = new mysqli("localhost","root","","wordpress_alumni");			// ****  CONFIGURE THIS FOR YOUR OWN DATABASE!  ****

	// Query to pull all addresses from Business Directory
	$addresses = $wpdb->query(
	"
	SELECT DISTINCT pm1.post_id 'id',
	(SELECT pm.meta_value FROM wp_postmeta pm WHERE pm.meta_key = '_wpbdp[fields][10]' AND pm.post_id = pm1.post_id ) AS address,
	(SELECT pm.meta_value FROM wp_postmeta pm WHERE pm.meta_key = '_wpbdp[fields][11]' AND pm.post_id = pm1.post_id) AS zip,
	(SELECT pm.meta_value FROM wp_postmeta pm WHERE pm.meta_key = '_wpbdp[fields][12]' AND pm.post_id = pm1.post_id) AS city,
	(SELECT pm.meta_value FROM wp_postmeta pm WHERE pm.meta_key = '_wpbdp[fields][13]' AND pm.post_id = pm1.post_id) AS country,
	(SELECT p.post_title 'title' FROM wp_posts p WHERE p.ID = pm1.post_id) AS company 

	FROM wp_posts p, wp_postmeta pm1
	WHERE p.ID = pm1.post_id
	AND p.post_type = 'wpbdp_listing'
	"
	);
	
	foreach ($addresses as $row) {	// get each value
		
		$id = $row['id'];
		$address = $row['address']." ";
		$zip = $row['zip']." ";
		$city = $row['city']." ";
		$country = $row['country']." ";
		$company = $row['company']." ";
		$coord = explode(" ",geocode($address.$zip.$city.$country));	// create lat-lng Array using geocode function
			
		$lat = $coord[0];	// stores Array values
		$lng = $coord[1];
		
		echo $lat." ".$lng." ";
//		echo $address.$zip.$city.$country.$company.$lat.$lng;		// This is the final output 
	} 
}


/** Geocoding function - turns Address (String) into coordinates **/
function geocode($address) {

	$parts = explode(" ", $address);  // separate address values
	$url_address = "";

	foreach ($parts as &$value) {	// translate for url
		$url_address .= $value."+";
	}

	$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$url_address."&sensor=false";
	$contents;
	$page_contents = "";
	$resp_json = file_get_contents($url);	// query google
	$resp = json_decode($resp_json, true);

	if($resp['status']='OK'){   // gets values from google
	    $contents=$resp['results'][0]['geometry']['location'];
	}else{
	    echo "error";
	}

	foreach ($contents as $value) {  // separates lat-lng
		$page_contents.= $value." ";
	}

	return $page_contents;
}

addMarkers();		// Function call!


?>
