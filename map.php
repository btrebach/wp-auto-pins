
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

/**** AJAX FUNCTIONS *****/

var addresses = "";
var searchResults = "";
var mapReady = false;

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
	var container = document.getElementById('view_map_button');
    container.innerHTML = "View Map";
	mapReady = true;
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
			var category;
			var addressIndex;
			var address;
			var zip;
			var cityIndex;
			var cityAndCountry;
			
			var companyName = listings[i+2];
			if (listings[i+3] != "") {
				companyName += " "+listings[i+3];
				if (listings[i+4] != "") {
					companyName += " "+listings[i+4];
					if (listings[i+5] != "") {
						companyName += " "+listings[i+5];
					} else {
						category = listings[i+6];
						addressIndex = i+8;
					}
				} else {
					category = listings[i+5];
					addressIndex = i+7;
				}
			} else {
				category = listings[i+4];
				addressIndex = i+6;
			}
			address = listings[addressIndex];
			if (listings[addressIndex+1] != "") {
				address += " "+listings[addressIndex+1];
				if (listings[addressIndex+2] != "") {
					address += " "+listings[addressIndex+2];
					if (listings[addressIndex+3] != "") {
						address += " "+listings[addressIndex+3];
						if (listings[addressIndex+4] != "") {
							address += " "+listings[addressIndex+4];
						} else {
							zip = listings[addressIndex+5];
							cityIndex = addressIndex+7;
						} 
					} else {
						zip = listings[addressIndex+4];
						cityIndex = addressIndex+6;
					}
				} else {
					zip = listings[addressIndex+3];
					cityIndex = addressIndex+5;
				}
			} else {
				zip = listings[addressIndex+2];
				cityIndex = addressIndex+4;
			}
			cityAndCountry = listings[cityIndex];
			if (isNaN(listings[cityIndex+1]) ||	listings[cityIndex+1] == "") {
				cityAndCountry += " "+listings[cityIndex+1];
				if (isNaN(listings[cityIndex+2]) || listings[cityIndex+2] == "") {
					cityAndCountry += " "+listings[cityIndex+2];
					if (isNaN(listings[cityIndex+3]) || listings[cityIndex+3] == "") {
						cityAndCountry += " "+listings[cityIndex+3];
						if (isNaN(listings[cityIndex+4]) || listings[cityIndex+4] == "") {
							cityAndCountry += " "+listings[cityIndex+4];
							if (isNaN(listings[cityIndex+5]) && listings[cityIndex+5] != "") {
								cityAndCountry += " "+listings[cityIndex+5];
								if (isNaN(listings[cityIndex+6]) && listings[cityIndex+6] != "") {
									cityAndCountry += " "+listings[cityIndex+6];
								}
							}
						}
					}
				}
			} 
			
			
			contentString[i] = '<div id="content">'+
			  '<div id="siteNotice">'+
			  '</div>'+
			  '<h2 id="firstHeading" class="firstHeading">'+companyName+'</h2>'+
			  '<div id="bodyContent">'+
			  '<p><b>'+category+'</b></p>'+
			  '<p>'+address+'</p>'+
			  '<p>'+zip+'</p>'+
			  '<p>'+cityAndCountry+'</p>'+
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
document.getElementById("view_map_button").onclick=function(){
	if (mapReady) {
		initialize();
	}
};

</script>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script>
	
	/*** ---- Jquery Script on Click ****/
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

	</script>
  				</head>
  	<body onload="getOutput()">		<!-- This updates the markers on page load -->
   		<div id="map-canvas"></div>
  	</body>
</html>


