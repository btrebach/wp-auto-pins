
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
    		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
			
<div id="initialize" style="width:100%; height:400px">

<button type="button" href="#" id="view_map_button" onclick="" > View Map </button>
<div id="output">waiting...</div>
  
<script>

/**** AJAX FUNCTIONS *****/

var addresses = "";
var searchResults = "";
console.log("whatt");

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

// Adds each marker to the map
function placeMarkers(map) {
	var temp = addresses.split(" ");
	
	for (var x in temp) {
		var i = parseInt(x);
			if (i%2 == 0) {
				console.log(i);
				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(temp[i],temp[i+1]),
					map: map
				});
			}
	}
}

// Loads map after button clicked
google.maps.event.addDomListener(view_map_button, 'click', initialize)


</script>
  				</head>
  	<body onload="getOutput()">		<!-- This updates the markers on page load -->
   		<div id="map-canvas"></div>
  	</body>
</html>


