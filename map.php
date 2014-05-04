
<!DOCTYPE html>
<html>
  	<head>
    		<title>Simple Map</title>
    			<meta 	name="viewport" 
				content="initial-scale=1.0, user-scalable=no">
    			<meta charset="utf-8">
    			<style>
      				.gmnoprint img { 
						max-width: none; 
					}
					html, body, #map-canvas {
	       				height: 100%;
						width: 100%;
						margin: 0px;
						padding: 0px
      				}
					#map-canvas {
						display:none;
					}
    			</style>
    		<script src="https://maps.googleapis.com/maps/api/js?v=3&sensor=false"></script>
			
<div id="initialize" style="width:100%; height:400px">

<button type="button" href="#" id="view_map_button" onclick="" > Loading Map </button>
  
<script>


var addresses = "";
var searchResults = "";
var mapReady = false;


var map;
var markers = new Array();

// Initialize map
function initialize() {
  	var mapOptions = {
   		zoom: 3,
    		center: new google.maps.LatLng(37.09024, -95.712891),
    		zoomControl: true,
    		zoomControlOptions: {
     			style: google.maps.ZoomControlStyle.DEFAULT
    		}
  	};

	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
	var view_map_button = document.getElementById('view_map_button');
	
	placeMarkers(map);	// Adds business directory listing addresses to map 
	
}

var contentString = new Array();

// Adds each marker to the map
function placeMarkers(map) {
	var testCount = 0;
	var listings = addresses.split(" ");			// split into lat/lng values
	console.log(listings);
	for (var x in listings) {
		var i = parseInt(x);
		
		/* Get Lat/Lng Values */
		if (listings[i] == "latlng") {			
			var pos = new google.maps.LatLng(listings[i+1],listings[i+2]);
			var marker = new google.maps.Marker({
				position: pos,
				map: map
			});
		}
		
		var infowindow = new google.maps.InfoWindow();
		var companyName;
		var category;
		var address;
		var zip;
		var city;
		var country;
		
		/* Get Company Name */
		if (listings[i] == "name") {				
			companyName = listings[i+1];
			if (listings[i+2] != "" && listings[i+2] != "category") {
				companyName += " "+listings[i+2];
				if (listings[i+3] != "" && listings[i+3] != "category") {
					companyName += " "+listings[i+3];
					if (listings[i+4] != "" && listings[i+4] != "category") {
						companyName += " "+listings[i+4];
					} 
				} 
			} 
		}
		
		/* Get Company Category */
		if (listings[i] == "category") {
			category = listings[i+1];
		}
			
		/* Get Company Address */
		if (listings[i] == "address") {
			address = listings[i+1];
			if (listings[i+2] != "" && listings[i+2] != "zip") {
				address += " "+listings[i+2];
				if (listings[i+3] != "" && listings[i+3] != "zip") {
					address += " "+listings[i+3];
					if (listings[i+4] != "" && listings[i+4] != "zip") {
						address += " "+listings[i+4];
						if (listings[i+5] != "" && listings[i+5] != "zip") {
							address += " "+listings[i+5];
							if (listings[i+6] != "" && listings[i+6] != "zip") {
								address += " "+listings[i+6];
								if (listings[i+7] != "" && listings[i+7] != "zip") {
									address += " "+listings[i+6];
								}
							}
						}
					}
				}
			} 
		}
		
		/* Get Company Zip Code */
		if (listings[i] == "zip") {
			zip = listings[i+1];
		}
		
		/* Get Company City */
		if (listings[i] == "city") {
			city = listings[i+1];
			if (listings[i+2] != "" && listings[i+2] != "country") {
				city += " "+listings[i+2];
				if (listings[i+3] != "" && listings[i+3] != "country") {
					city += " "+listings[i+3];
				}
			}
		}
		
		/* Get Company Country */
		if (listings[i] == "country") {
			country = listings[i+1];
			if (listings[i+2] != "") {
				country += listings[i+2];
			}
		}
		
		/* Write company contents to info window */
		contentString[i] = '<div>'+
			'<h2>'+companyName+'</h2>'+
			'<b>'+category+'</b><br>'+address+'<br>'+zip+'<br>'+city+'<br>'+country+
			'</div>';
		
		/* Create info window */
		google.maps.event.addListener(marker, 'click', (function(marker, i) {
			return function() {
				infowindow.setContent(contentString[i]);
				infowindow.open(map, marker);
			}
		})(marker, i));
	}
}

// Loads map after button clicked
document.getElementById("view_map_button").onclick=function(){
	if (mapReady) {
		initialize();
	}
};

</script>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script>
	
	/** ---- Show/Hide map on button click ---- **/
	$("#view_map_button").click(function(){
		if (mapReady) {
			// Toggle Map
			$('#map-canvas').toggle();
			
			// Initialize Map
			initialize();
			
			// Button Text Change
			var txt = $("#map-canvas").is(':visible') ? 'Hide Map' : 'View Map';
			$("#view_map_button").html(txt);
		}
	});
	
	
	/** ---- Handles the Ajax request to get listing addresses ---- **/
	function getOutput() {
		searchResults = '<?php if ($results != null) { echo(implode(" ",$results)); }  ?>';		// get search results if they exist
		$.ajax({
			url: 'wp-content/plugins/business-directory-plugin/views/geocode.php'.concat("?searchResults=").concat(searchResults), // URL for the PHP file
			complete: function (response) {
				var container = document.getElementById('view_map_button');
				container.innerHTML = "View Map";		// changes text inside button
				mapReady = true;						// map is ready for viewing
				addresses = response.responseText;		// store responses in this variable
			},
			error: function () {
				alert("It seems there was an error.");
			},
		});
		return false;
	}

	</script>
  				</head>
  	<body onload="getOutput()">		<!-- This updates the markers on page load -->
   		<div id="map-canvas"></div>
  	</body>
</html>


