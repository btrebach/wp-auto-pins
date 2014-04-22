
<!DOCTYPE html>
<html>
  	<head>
    		<title>Simple Map</title>
    			<meta 	name="viewport" 
				content="initial-scale=1.0, user-scalable=no">
    			<meta charset="utf-8">
    			<style>
      				.gmnoprint img { max-width: none; }
      				html, body, #map-canvas {
	       				height: 100%;
					width: 100%;
					margin: 0px;
					padding: 0px
      				}
    			</style>
    		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
			
<div id="initialize" style="width:100%; height:400px">

<button type="button" href="#" onclick="getOutput();"> Update Addresses </button>
<button type="button" href="#" id="addButton" > Add to Map </button>
<div id="output">waiting for action</div>
  
<script>

/**** AJAX FUNCTIONS *****/

var addresses = "";

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
    container.innerHTML = "Addresses Updated Successfully!";
	addresses = responseText;
}
// helper function for cross-browser request object
function getRequest(url, success, error) {
    var req = false;
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
    req.open("GET", url, true);
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
	
	var addButton = document.getElementById('addButton');
	
	google.maps.event.addDomListener(addButton, 'click', function(e) {		// Map is clicked, add markers!
		placeMarker(map);
	});
}

// Adds each marker to the map
function placeMarker(map) {
	var temp = addresses.split(" ");
	
	for (var x in temp) {
			var i = parseInt(x);
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(temp[i],temp[i+1]),
				map: map
			});
		
	}
}

google.maps.event.addDomListener(window, 'load', initialize);


</script>
  				</head>
  	<body>	// onload function here to pull addresses
   		<div id="map-canvas"></div>
  	</body>
</html>


