<?php
	  $rawdata = file_get_contents('php://input');
	  $obj = json_decode($rawdata, True);
	  if ($obj['type'] == 'nearby') {
	  	if($obj['locatio'] == 'here') {
	  		$coordinates = $obj['from'];
	  	} else {
	  		$tempjson = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$obj['from'].'&key=AIzaSyCEp9G4hsG0TQnBrcuzNCsPW0e64LAYMoA');
			$tempobj = json_decode($tempjson, True);
			$lat = 'invalid';
			$lang = 'invalid';
			if (isset($tempobj['results'][0]['geometry'])) {
				$lat =  ($tempobj['results'][0]['geometry']['location']['lat']);
			}
			if (isset($tempobj['results'][0]['geometry'])) {
				$lang =  ($tempobj['results'][0]['geometry']['location']['lng']);
			}
			$coordinates = $lat.",".$lang;

	  	}


	  	$json2 = file_get_contents('https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$coordinates.'&radius='.$obj['distance'].'&type='.$obj['category'].'&keyword='.$obj['keyword'].'&key=AIzaSyCEp9G4hsG0TQnBrcuzNCsPW0e64LAYMoA');
	  	if($obj['locatio'] != 'here') {

	  	   $arr = json_decode($json2, TRUE);
		   array_push($arr, ['coord' => $coordinates]);
		   $json = json_encode($arr);
		   echo $json;
		   exit();
	  	}

	  	echo $json2;
	  	exit();
	  }
	  if ($obj['type'] == 'review') {
	  	$directoryName = 'images';

		//Check if the directory already exists.
		if(!is_dir($directoryName)){
		    //Directory does not exist, so lets create it.
		    mkdir($directoryName, 0755);
		}

	  	$json2 = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$obj['place_id'].'&key=AIzaSyCEp9G4hsG0TQnBrcuzNCsPW0e64LAYMoA');
	  	$obj2 = json_decode($json2, True);
	  	$j = 0;
	  	if (isset($obj2['result']['photos'])) {

		  	foreach ($obj2['result']['photos'] as $item) {
		  		if ($j == 5) {
		  			break;
		  		}
		  		foreach ($item as $key => $value) {
		  			# code...
		  			if ($key == 'photo_reference') {
		  				$name = 'images/'.$obj['place_id'].'-'.$j.'.jpg';
		  				$photo = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=750&photoreference='.$value.'&key=AIzaSyCEp9G4hsG0TQnBrcuzNCsPW0e64LAYMoA';
		  			   file_put_contents($name, file_get_contents($photo));

		  			}
		  		}
		  		$j = $j+1;
		  	}
		 }



	  	echo $json2;
	  	exit();

	  }

	  if ($obj['type'] == 'yelp') {
	  	$API_KEY = 'Fg1vzPQjVzOiOvasE7LTy4A70bsbSiHEYUAez9GNqVU2s5Ejfr0i8ip4JhxDKRNRelHRCAElosLDIidxXoAvU79kAJxooQ3zGAmXQ7wxzalTn1JTzwQ6NgbZB4S3WnYx';
		// Complain if credentials haven't been filled out.
		assert($API_KEY, "Please supply your API key.");
		// API constants, you shouldn't have to change these.
		$host = "https://api.yelp.com";
		$path = "/v3/businesses/matches/best";
		$url_params = array();
		$url_params['name'] = 'Tutor Hall Cafe';
    	$url_params['city'] = 'Los Angeles';
    	$url_params['state'] = 'CA';
    	$url_params['country'] = 'US';
    	$url_params['address1'] = 'McClintock Avenue';

    	try {
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');
        $url = $host . $path . "?" . http_build_query($url_params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $API_KEY,
                "cache-control: no-cache",
            ),
		        ));
		        $response = curl_exec($curl);
		        if (FALSE === $response)
		            throw new Exception(curl_error($curl), curl_errno($curl));
		        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		        if (200 != $http_status)
		            throw new Exception($response, $http_status);
		        curl_close($curl);
		    } catch(Exception $e) {
		        trigger_error(sprintf(
		            'Curl failed with error #%d: %s',
		            $e->getCode(), $e->getMessage()),
		            E_USER_ERROR);
		    }


		    $respons = json_decode($response);
    		$business_id = $respons->businesses[0]->id;
    		$new_path = '/v3/businesses/'.$business_id.'/reviews';


    	try {
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');
        $url = $host . $new_path;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $API_KEY,
                "cache-control: no-cache",
            ),
		        ));
		        $response = curl_exec($curl);
		        if (FALSE === $response)
		            throw new Exception(curl_error($curl), curl_errno($curl));
		        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		        if (200 != $http_status)
		            throw new Exception($response, $http_status);
		        curl_close($curl);
		    } catch(Exception $e) {
		        trigger_error(sprintf(
		            'Curl failed with error #%d: %s',
		            $e->getCode(), $e->getMessage()),
		            E_USER_ERROR);
		    }

		    echo $response;


			exit();




	  }
?>


<HTML>
</pre></BODY></HTML>
<script language="javascript">
window.onload = function(e){
    document.getElementById("submit").disabled = true;
   function showPosition(position) {

	    currentLocation = position.coords.latitude + "," + position.coords.longitude
	    document.getElementById("Here").setAttribute("value", currentLocation)
	     document.getElementById("submit").disabled = false;
	}
   navigator.geolocation.getCurrentPosition(showPosition);
    url = 'http://ip-api.com/json'
    var xmlhttp=new XMLHttpRequest();
	xmlhttp.open("GET",url,false);
	try {
		xmlhttp.send(null);
	}
	 catch(err) {
	 	alert("Please reload again")
	 	return
	 }
	jsonDoc=xmlhttp.responseText;
	obj = JSON.parse(jsonDoc);
	latitude = obj.lat;
	longitude = obj.lon;
	currentLocation = latitude + "," + longitude;
	document.getElementById("radio1").setAttribute("value", currentLocation)
	document.getElementById("submit").disabled = false;

 }

function doFunction(event) {
	reviewFlag = true;
	photoFlag = true;

	place_id = event.getAttribute("place_id");
	name = event.getAttribute("name");
	sjson = {"type": "review",  "place_id": place_id};
	str_json = JSON.stringify(sjson);
	hea = "<h3 id='headingH'>"+name+"</h3>";
	document.getElementById("heading").innerHTML = '';
	document.getElementById("heading").innerHTML = hea;
    console.log("NAME CLICKED");
    var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 34.0266, lng: -118.2831},
          zoom: 15
    });
    var request = {
	  placeId: place_id
	};
	var rendererOptions = {
        preserveViewport: true
    };
	var infowindow = new google.maps.InfoWindow();
	var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
    var directionsService = new google.maps.DirectionsService;
    directionsDisplay.setMap(map);
	service = new google.maps.places.PlacesService(map);
	service.getDetails(request, callback);
	console.log("SJJSKSO");

	function callback(place, status) {
	  if (status == google.maps.places.PlacesServiceStatus.OK) {
	  	console.log(place);
	  	html = ''
	  	html+='<table class="table table-striped"><thead></thead><tbody>';
	  	if (place.formatted_address != null) {
	  		html+='<tr class="table-striped"><td><b> Address </b></td><td id="addressP">'+place.formatted_address+'</td><tr>';
	  	}
	  	if (place.international_phone_number != null) {
	  		html+='<tr><td><b> Phone number </b></td><td>'+place.international_phone_number+'</td><tr>';
	  	}
	  	if (place.price_level != null) {
	  		tmp = '$';
	  		proice = parseInt(place.price_level);
	  		while (proice > 1) {
	  			tmp+='$';
	  			proice = proice - 1;
	  		}
	  		html+='<tr><td><b> Price Level </b></td><td>'+tmp+'</td><tr>';
	  	}
	  	if (place.rating != null) {
	  		html+="<tr><td><b> Ratings</b></td><td><div id = 'rating1' class='rating'>★★★★★</div></td><tr>";
	  	}
	  	if (place.url != null) {
	  		html+='<tr><td><b> Google Page</b></td><td><a href='+place.url+'>'+place.url+'</a></td><tr>';
	  	}
	  	if (place.website != null) {
	  		html+='<tr><td><b> Website</b></td><td><a href='+place.website+'>'+place.website+'</a></td><tr>';
	  	}


	  	html+='</tbody></table>';
	  	document.getElementById("info").innerHTML = html;
	  	if (place.rating != null) {
	  		console.log(place.rating);
	  		stars = parseFloat(place.rating);
		  	var cw = document.getElementById("rating1").offsetWidth;
		  	console.log(cw);
		  	window.rating1.style.width = Math.round(cw * (stars / 5)) + 'px';
		 }
		 console.log("JIKNK")
		var photos = place.photos;
		 console.log(photos)
		   j = 0;
		   html = '';
		   html+='<table class="table"><thead></thead><tbody><tr>';
		  for (i =0; i < photos.length; i++) {
		  	photoOb = photos[i];
		  	url = photoOb.getUrl({'maxWidth': 300, 'maxHeight': 300});
		  	html+= "<td><a href="+url+"><img height='300' width='300' src="+url+"></img></a></td>"
		  	if (j==3) {
		  		html+="</tr><tr>";
		  		j=0
		  	}
		  	else {
		  		j = j+1
		  	}

		  }

		  html+='</tbody></table>';
	  	   document.getElementById("photo").innerHTML = html;
	  	   var marker = new google.maps.Marker({
              map: map,
              position: place.geometry.location
            });
	      google.maps.event.addListener(marker, 'click', function() {
	        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' +
	          'Place ID: ' + place.place_id + '<br>' +
	          place.formatted_address + '</div>');
	        infowindow.open(map, this);

	      });

	      calcRoute(directionsService, directionsDisplay);
	      console.log(place.reviews);

	      reviews = place.reviews;

	      	sjson = {'type': 'yelp', 'name': 'Tutor Hall Cafe', 'city': 'Los Angeles', 'state': 'CA', 'country': 'US', 'address1': 'McClintock Avenue'}
	        str_json = JSON.stringify(sjson);
			xhr= new XMLHttpRequest();
			xhr.open("POST", "index.php", true);
			xhr.setRequestHeader("Content-type", "application/json");
			xhr.send(str_json);

			xhr.onreadystatechange = function () {
			  if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
			  	jsonDoc = xhr.responseText;
			    console.log(jsonDoc);
			    try {
				 	obj = JSON.parse(jsonDoc);
				 	console.log(obj.status);
				 }
				 catch(err) {
				 	alert(err);
				 	return
				 }


			  }
			}





	  }
	}


	    document.getElementById('results').innerHTML = '';
	    document.getElementById('inter-results').style.display = 'block';



}

function calcRoute(directionsService, directionsDisplay) {
		des = (document.getElementById("headingH").innerHTML + ', ' + document.getElementById("addressP").innerHTML);


		console.log(des);
        directionsService.route({
          origin: document.getElementById('radio1').value,
          destination: des,
          provideRouteAlternatives: true,
          travelMode: 'WALKING'
        }, function(response, status) {
          if (status === 'OK') {
          	console.log(response);
            directionsDisplay.setDirections(response);
            // directionsDisplay.setRouteIndex(2);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }

function sampleFunction() {
	var fulldate = new Date(1370001284000);
	var converted_date = moment(fulldate).format();
	console.log(converted_date);
	document.getElementById('results').innerHTML = '';
	document.getElementById('inter-results').style.display = 'none';
	keyword= document.getElementById("keyword").value;
	e= document.getElementById("selectcategory");
	var category = e.options[e.selectedIndex].value;
	category = category.replace(/' '/g,'+');
	var distance = document.getElementById("distance").value;
	if (distance == ''){
		distance = '10';
	}
	distance = parseFloat(distance)*1609.344;
	distance = distance.toString();


	if (document.getElementById('radio1').checked == true) {
	    from = document.getElementById('radio1').value;
	    locatio = "here";
	  } else if (document.getElementById('radio2').checked == true) {
	    from = document.getElementById('locationName').value;
	    locatio = "location";
	  }

	keyword = keyword.replace(/\s/g, '+');
    category=category.replace(/\s/g, '+');
    from = from.replace(/\s/g, '+');
	sjson = {"type": "nearby", "keyword":keyword, "category":category, "distance": distance, "from": from, "locatio": locatio};
	console.log(sjson);
	str_json = JSON.stringify(sjson);
	xhr= new XMLHttpRequest();
	xhr.open("POST", "index.php", true);
	xhr.setRequestHeader("Content-type", "application/json");
	xhr.send(str_json);

	xhr.onreadystatechange = function () {
	  if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
	    jsonDoc = xhr.responseText;
	    console.log(jsonDoc);
	    try {
		 	obj = JSON.parse(jsonDoc);
		 	console.log(obj.status);
		 }
		 catch(err) {
		 	alert(err);
		 	return
		 }

		 if (obj.status == 'ZERO_RESULTS' || obj.status == 'INVALID_REQUEST') {
		 	html= "<table width=1000 style='background-color:rgb(220,220,220);'><tr><td style='text-align: center;'><b>No Records has been Found</td></tr></table>";
		 	document.getElementById("results").innerHTML = '';
			document.getElementById("results").innerHTML = html;
			return;

		 }
		 if (locatio == 'location') {
		 	pcoord = obj['0'].coord;

		 } else {
		 	pcoord = from

		 }

		html='';
	 	html+="<TABLE width='1000' BORDER='2'><tr>";
	 	html+= "<th>#</th>"
		html+="<th>Category</th>";
		html+="<th>Name</th>";
		html+="<th>Address</th>";
		for (i=0; i< obj.results.length; i++) {
			data = obj.results[i];
			html+="<tr>";
			html+="<td><b>"+ (i+1) +"</b></td>"
			dlat = data['geometry']['location']['lat'];
			dlang = data['geometry']['location']['lng'];
			for (key in data) {
				if (key == 'icon') {
				  html+= '<td width=150><img src=' + data[key] + '></td>';
				}
				if  (key == 'name') {
					name =  data[key];
				}

				if (key == 'place_id') {
					html+="<td><a name='"+name+ "'class= 'reviews' onclick= 'doFunction(this);' style='color: black; text-decoration:none;' href='javascript:;' place_id=" + data[key] + '>' + name + "</a></td>";
				}
				if (key == 'vicinity') {
					 html+= "<td style='position: relative;'><a lat="+dlat+" lang="+dlang+" divid=map_id_"+i+" mapId = map-"+i+" onclick= 'initMap(this);' style='color: black; text-decoration:none;' href='javascript:;'>" + data[key] + "</a>";
					 html+="<div style='position: absolute; display: none' id=map_id_"+i+" width='400px'>";
					 html+="<table class= 'float-panel' cellpadding='0' cellspacing='0' border='0'>";
					 html+="<tr>";
					 html+="<td style='background-color: rgb(169, 169, 169); text-align: center;'>";
					 html+="<a data-lat="+dlat+" data-lang="+dlang+" data-coord="+pcoord+" id= WALKING-"+i+" style='color: black; text-decoration:none;' href='javascript:;'>Walk there</a></td></tr>";
					 html+="<tr><td style='background-color: rgb(169, 169, 169); text-align: center;'><a data-lat="+dlat+" data-lang="+dlang+" data-coord="+pcoord+" id=BICYCLING-"+i+" style='color: black; text-decoration:none;' href='javascript:;'>Bike there</a></td></tr>";
					 html+="<td style='background-color: rgb(169, 169, 169); text-align: center;'><a data-lat="+dlat+" data-lang="+dlang+" data-coord="+pcoord+" id= DRIVING-"+i+" style='color: black; text-decoration:none;' href='javascript:;'>Drive there</a></td>";
					 html+= "</tr></table><div class='maps' id=map-"+i+"></div></div>";
					 html+="</td>"




				}
			}
			html+="<tr>";
			document.getElementById("results").innerHTML = '';
			document.getElementById("results").innerHTML = html;
		}

	  }
	};


}
function initMap(event) {
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsService = new google.maps.DirectionsService;
        map_id = event.getAttribute("mapid");
        div_id = event.getAttribute("divid");
        lat = parseInt(event.getAttribute("lat"));
        long = parseInt(event.getAttribute("lang"));
        id = map_id.split('-').pop();
        driving_id = "DRIVING"+'-'+id;
        bicyling_id = "BICYCLING"+'-'+id;
        walking_id = "WALKING"+'-'+id;
        if (document.getElementById(map_id).innerHTML !== "") {
            document.getElementById(map_id).innerHTML = '';
            document.getElementById( div_id).style.display = 'none';
            return
        }
        document.getElementById(div_id).style.display = 'block';

        // document.getElementById('map_id').style.setProperty("top", x.toString() + 'px');
        // document.getElementById('map_id').style.setProperty("left", y.toString() + 'px');
        // document.getElementById('map_id').style.setProperty("bottom", bottom.toString() + 'px');

        var uluru = {lat: lat, lng: long};
        var map = new google.maps.Map(document.getElementById(map_id), {
          zoom: 12,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
        directionsDisplay.setMap(map);
        document.getElementById(driving_id).addEventListener('click', function(event) {
          calculateAndDisplayRoute(event, directionsService, directionsDisplay);
        });
        document.getElementById(bicyling_id).addEventListener('click', function(event) {
          calculateAndDisplayRoute(event, directionsService, directionsDisplay);
        });
        document.getElementById(walking_id).addEventListener('click', function(event) {
          calculateAndDisplayRoute(event, directionsService, directionsDisplay);
        });
      }

      function calculateAndDisplayRoute(event, directionsService, directionsDisplay) {
        selectedMode = event.target.id.split('-')[0];
        dlat = parseFloat(event.target.attributes.getNamedItem("data-lat").value);
        dlong = parseFloat(event.target.attributes.getNamedItem("data-lang").value);
        pcoord = event.target.attributes.getNamedItem("data-coord").value.split(',');
        plat = parseFloat(pcoord[0]);
        plang = parseFloat(pcoord[1]);
        directionsService.route({
          origin: {lat: plat, lng: plang},  // Haight.
          destination: {lat: dlat, lng: dlong},  // Ocean Beach.
          travelMode: google.maps.TravelMode[selectedMode]
        }, function(response, status) {
          if (status == 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }

      function arrowFunction(event) {
      	id = event.getAttribute('id');
      	if (id == 'downreview') {
      	  document.getElementById('phototext').innerHTML = 'click to show photos';
      	  document.getElementById('reviewtext').innerHTML = 'click to hide reviews';
      	  document.getElementById('upphoto').style.visibility = 'hidden';
      	  document.getElementById('downphoto').style.visibility = 'visible';
      	  document.getElementById('photos1').style.display = 'none';

      	  document.getElementById(id).style.visibility = 'hidden';
      	  document.getElementById('upreview').style.visibility = 'visible';
      	  document.getElementById('reviews1').style.display = 'block';
      	}
      	if (id == 'upreview') {
      	  document.getElementById('reviewtext').innerHTML = 'click to show reviews';
      	  document.getElementById(id).style.visibility = 'hidden';
      	  document.getElementById('downreview').style.visibility = 'visible';
      	  document.getElementById('reviews1').style.display = 'none';
      	}

      	if (id == 'upphoto') {
      	  document.getElementById('phototext').innerHTML = 'click to show photos';
      	  document.getElementById(id).style.visibility = 'hidden';
      	  document.getElementById('downphoto').style.visibility = 'visible';
      	  document.getElementById('photos1').style.display = 'none';
      	}

      	if (id == 'downphoto') {
      	  document.getElementById('reviewtext').innerHTML = 'click to show reviews';
      	  document.getElementById('phototext').innerHTML = 'click to hide photos';
      	  document.getElementById('upreview').style.visibility = 'hidden';
      	  document.getElementById('downreview').style.visibility = 'visible';
      	  document.getElementById('reviews1').style.display = 'none';
      	  document.getElementById(id).style.visibility = 'hidden';
      	  document.getElementById('upphoto').style.visibility = 'visible';
      	  document.getElementById('photos1').style.display = 'block';
      	}



      }
function clearStuff() {

	document.getElementById('results').innerHTML = '';
	document.getElementById('inter-results').style.display = 'none';
	document.getElementById("locationName").disabled =true;

}

function enableButton() {

  document.getElementById("locationName").disabled = false;

}

function disableButton() {

  document.getElementById("locationName").disabled =true;

}
var placeSearch, autocomplete;

function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('locationName')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
      }
// Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
console.log("here");
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function(position) {
    var geolocation = {
      lat: position.coords.latitude,
      lng: position.coords.longitude
    };
    var circle = new google.maps.Circle({
      center: geolocation,
      radius: position.coords.accuracy
    });
    autocomplete.setBounds(circle.getBounds());
  });
}
}

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEp9G4hsG0TQnBrcuzNCsPW0e64LAYMoA&libraries=places&callback=initAutocomplete" async defer>
</script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js" type="text/javascript"></script>
<HTML>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Travel and entertainment search</title>
  </head>
  <BODY>
<style>
.address {
    color:black;
    text-decoration:none;
    font-weight: lighter;
}

.maps {
        height: 300px;
        width: 400px;
        z-index: 2;

       }
#map {
  height: 100%;
}

#map_id {
height: 300px;
width: 100%;
}

.float-panel {
position: absolute;
margin-top: 1px;
width: 90px;
height: 100px;
z-index: 5;

}

.rating {
  font-size: 14px;
  color: orange;
  display: inline-block;
  overflow: hidden;
}

</style>
<div style="max-width: 800px; margin: auto; background-color: rgb(242, 242, 242)">
<div style="border-style: groove;">
<H1 style='text-align: center;'><i>Travel and Entertainment Search</i></H1>
<hr>
<form METHOD="POST" onsubmit=" sampleFunction(); return false;" ACTION="">
<div class="form-group">
<label for="keyword">Keyword</label>
<INPUT id=keyword required></INPUT>
</div>
<div class="form-group">
<label for="selectcategory"> Category </label>
<SELECT class="form-control" id= 'selectcategory' name=state>
<OPTION value='default'>default</OPTION>
<OPTION value='cafe'>cafe</OPTION>
<OPTION value='bakery'>bakery</OPTION>
<OPTION value='restaurant'>restaurant</OPTION>
<OPTION value='beauty salon'>beauty salon</OPTION>
<OPTION value='casino'>casino</OPTION>
<OPTION value='movie theater'>movie theater</OPTION>
<OPTION value='lodging'>lodging</OPTION>
<OPTION value='airport'>airport</OPTION>
<OPTION value='train station'>train station</OPTION>
<OPTION value='subway station'>subway station</OPTION>
<OPTION value='bus station'>bus station</OPTION>
</SELECT>
</div>
Distance(miles) <INPUT id='distance' name=name placeholder="10"></INPUT><br>

from<INPUT id='radio1' checked  onclick="disableButton();" type=radio name=radio value='34.0266,-118.2831' required>Here<br>
<INPUT id='radio2' onclick="enableButton();" type=radio name=radio  value=location><INPUT disabled onFocus="geolocate(this);" required id='locationName' placeholder="location" name=LocationName><BR>
<INPUT type=submit name="submit" id="submit" value="Search"/> <INPUT type=reset value="clear" onclick="clearStuff();"/>
</FORM>
</BODY>
</div>

</div>

<div class="FinalResults" style="max-width: 1000px; margin: auto;">
<div id="results">
</div>
</div>
<div class="FinalResults" style="max-width: 1700px; margin: auto;">
<div id = 'inter-results' style="display: none;">
<div   style="text-align: center;" id="heading">
</div>
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Info</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="photo-tab" data-toggle="tab" href="#photo" role="tab" aria-controls="profile" aria-selected="false">Photos</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="mapd-tab" data-toggle="tab" href="#mapd" role="tab" aria-controls="mapd" aria-selected="false">Maps</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviewsd" role="tab" aria-controls="reviewsd" aria-selected="false">Reviews</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab"></div>
  <div class="tab-pane fade" id="photo" role="tabpanel" aria-labelledby="photo-tab"></div>
  <div class="tab-pane fade" id="mapd" role="tabpanel" aria-labelledby="mapd-tab">
  <div class="form-group">
  <label for="fromMap">From</label>
   <INPUT id=fromMap required></INPUT>
   </div>
    <div class="form-group">
   <label for="toMap">To</label>
   <INPUT id=toMap required></INPUT>
   </div>
   <div class="tab-pane fade" id="reviewsd" role="tabpanel" aria-labelledby="reviewsd-tab"></div>


   <div class="form-group">
<label for="selectcategory"> Mode </label>
<SELECT class="form-control" id= 'selectcategory' name=state>
<option value="DRIVING">Driving</option>
  <option value="WALKING">Walking</option>
  <option value="BICYCLING">Bicycling</option>
  <option value="TRANSIT">Transit</option>
</SELECT>
</div>



  	<div id="map">
	</div>
  </div>
</div>
<br>
<br>
<br>
<div  style="text-align: center; display: none;" id ="reviews1">
</div>
<br>
<br>
<br>
<div style="text-align: center; display: none;" id = "photos1">
</div>
</div>
</div>

</HTML>
