
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
    			</style>
    		<script src="https://maps.googleapis.com/maps/api/js?v=3&sensor=false"></script>
			
<div id="initialize" style="width:100%; height:400px">

<button type="button" href="#" id="view_map_button" onclick="" > View Map </button>
<div id="output">waiting...</div>
  
<script>

/**** AJAX FUNCTIONS *****/

var addresses = "";
var searchResults = "";

// handles the click event for link 1, sends the query
function getOutput() {
  getRequest(
      'wp-content/plugins/business-directory-plugin/views/geocode.php', // URL for the PHP file
       drawOutput,  // handle successful request
       drawError    // handle error
  );
  return false;
}  
// handles drawing an error message
function drawError () {
    var container = document.getElementById('output');
    container.innerHTML = 'Bummer: there was an error!';
}
// handles the response, adds the html
function drawOutput(responseText) {
    var container = document.getElementById('output');
    container.innerHTML = responseText;
	addresses = responseText;
}
// helper function for cross-browser request object
function getRequest(url, success, error) {
    var req = false;
	searchResults = '<?php if ($results != null) { echo(implode(" ",$results)); }  ?>';		// get search results if they exist
	
    try{
        // most browsers
        req = new XMLHttpRequest();
    } catch (e){
        // IE
        try{
            req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            // try an older version
            try{
                req = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e){
                return false;
            }
        }
    }
    if (!req) return false;
    if (typeof success != 'function') success = function () {};
    if (typeof error!= 'function') error = function () {};
    req.onreadystatechange = function(){
        if(req .readyState == 4){
            return req.status === 200 ? 
                success(req.responseText) : error(req.status)
            ;
        }
    }
	req.open("GET", url.concat("?searchResults=").concat(searchResults), true);		// calls a request to open geocode file, sends search results as parameter
    req.send(null);
    return req;
}



/**** AJAX END ******/

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
	var listings = addresses.split(" ");			// split into lat/lng values
	console.log(listings);
	for (var x in listings) {
		var i = parseInt(x);
		if (!isNaN(listings[i]) && listings[i] < 300 && listings[i] != "" && i%2 == 0) {		// If value is a number, not a zip code - it's lat/lng
			var pos = new google.maps.LatLng(listings[i],listings[i+1]);
			var marker = new google.maps.Marker({
				position: pos,
				map: map
			});
			var infowindow = new google.maps.InfoWindow();
			var companyName = listings[i+2];
			if (listings[i+3] != "") {
				companyName += " "+listings[i+3];
				if (listings[i+4] != "") {
					companyName += " "+listings[i+4];
					if (listings[i+5] != "") {
						companyName += " "+listings[i+5];
					}
				}
			}
			console.log(companyName);
			contentString[i] = '<div id="content">'+
			  '<div id="siteNotice">'+
			  '</div>'+
			  '<h2 id="firstHeading" class="firstHeading">'+companyName+'</h2>'+
			  '<div id="bodyContent">'+
			  '<p><b>Uluru</b>, blah blah blah</p>'+
			  '</div>'+
			  '</div>';
			
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infowindow.setContent(contentString[i]);
					infowindow.open(map, marker);
				}
			})(marker, i));
		}
	}
}

// Loads map after button clicked
google.maps.event.addDomListener(view_map_button, 'click', initialize);


</script>
  				</head>
  	<body onload="getOutput()">		<!-- This updates the markers on page load -->
   		<div id="map-canvas"></div>
  	</body>
</html>


