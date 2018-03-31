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
?>


<HTML>
</pre></BODY></HTML>
<script language="javascript">
	// Sets the coordinates of the current location
	window.onload = function(e){
	    document.getElementById("submit").disabled = true;
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

	// Gets the place details of a particular place when the corresponding place is clicked
	function placeDetails(event) {

	  	var reviewFlag, photoFlag, place_id, sjson, str_json, jsonDoc, obj, reviewObj, photoObj;
	  	reviewFlag = true;
	  	photoFlag = true;
	  	place_id = event.getAttribute("place_id");
		name = event.getAttribute("name");
		sjson = {"type": "review",  "place_id": place_id};
		str_json = JSON.stringify(sjson);
		hea = "<h3>"+name+"</h3>";
		document.getElementById("heading").innerHTML = '';
		document.getElementById("heading").innerHTML = hea;
		xhr= new XMLHttpRequest();
		xhr.open("POST", "index.php", true);
		xhr.setRequestHeader("Content-type", "application/json");
		xhr.send(str_json);
	  	xhr.onreadystatechange = function () {
	    if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
	        jsonDoc = xhr.responseText;

	        try {
	      		obj = JSON.parse(jsonDoc);
	     	}
	     	catch(err) {
	      		alert("No such file present")
	      		return
	     	}

	    	reviewObj =  obj.result.reviews;
	    	photoObj = obj.result.photos;
	    	if (reviewObj == null) {
	      		reviewFlag = false
	      		document.getElementById("reviews1").innerHTML = '';
	      		html1="<table width=800 border=1><tr><td style='text-align: center;'><b>No reviews found</td></tr></table>"
	      		document.getElementById("reviews1").innerHTML = html1;
	     	}

	    if (photoObj == null) {
	      	photoFlag = false
	      	document.getElementById("photos1").innerHTML = '';
	      	html1="<table width=800 border=1><tr><td style='text-align: center;'><b>No photos found</td></tr></table>"
	      	document.getElementById("photos1").innerHTML = html1;
	    }

	    if (reviewFlag == true) {
	      	mini = obj.result.reviews.length;
	      	if (mini > 5) {
	       	 	mini = 5
	      	}
	        html='';
	        html+="<TABLE style='margin-left: auto; margin-right: auto'; width='800' BORDER='2'>";
	        for (j = 0; j < mini; j++) {
	            data = obj.result.reviews[j];
	            html+="<tr>";
	            for (key in data) {
	              if (key == 'author_name') {
	                name = data[key]
	              }
	              if (key == 'profile_photo_url') {
	                html+= "<td style='text-align: center;'> <img height='15' width='12' src="+ data[key] + ">" + name + "</td></tr><tr>"
	              }
	              if (key == 'text') {
	                html+= "<td style= 'height: 20;'>"+ data[key] + "</td>"
	              }
	            }
	            html+="</tr>"
	        }
	        document.getElementById("reviews1").innerHTML = '';
	        document.getElementById("reviews1").innerHTML = html;
	    }
	    if (photoFlag == true) {
	        miniPhoto = obj.result.photos.length;

	      	if (miniPhoto > 5) {
	        	miniPhoto = 5;
	      	}

	        html='';
	     	html+="<TABLE style='margin-left: auto; margin-right: auto'; width='800'BORDER='2'>";

	        for (k =0; k < miniPhoto; k++) {
	            html+="<tr>";
	            photo_src = 'images/'+place_id+'-'+k+'.jpg';
	            html+="<td style='text-align: center;'><a target='_blank' href="+ photo_src+"><img height='450' width= '700' src="+photo_src+"></a></td>"
	            html+="</tr>";
	        }
	        html+="</TABLE>";
	        document.getElementById("photos1").innerHTML = '';
	        document.getElementById("photos1").innerHTML =html;

	      }
	      document.getElementById('results').innerHTML = '';
	      document.getElementById('inter-results').style.display = 'block';
	     }
	  }
	}

	// Takes in the input from the form and makes a server request for the details from Google API
	function submitForm() {

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
	  	str_json = JSON.stringify(sjson);
	  	xhr= new XMLHttpRequest();
	  	xhr.open("POST", "index.php", true);
	  	xhr.setRequestHeader("Content-type", "application/json");
	  	xhr.send(str_json);

	  	xhr.onreadystatechange = function () {
	    	if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
	      	jsonDoc = xhr.responseText;
	      	try {
	     	 obj = JSON.parse(jsonDoc);
	     	}
	     	catch(err) {
	      		alert(err);
	      		return;
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
	      		pcoord = from;
	    	}

	    	html='';
	    	html+="<TABLE width='1000' BORDER='2'><tr>";
	    	html+="<th>Category</th>";
	    	html+="<th>Name</th>";
	    	html+="<th>Address</th>";
	    	for (i=0; i< obj.results.length; i++) {
	      		data = obj.results[i];
	      		html+="<tr>";
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
		          		html+="<td><a name='"+name+ "'class= 'reviews' onclick= 'placeDetails(this);' style='color: black; text-decoration:none;' href='javascript:;' place_id=" + data[key] + '>' + name + "</a></td>";
		        	}
	        		if (key == 'vicinity') {
		           		html+= "<td style='position: relative;'><a lat="+dlat+" lang="+dlang+" divid=map_id_"+i+" mapId = map-"+i+" onclick= 'initMap(this);' style='color: black; text-decoration:none;' href='javascript:;'>" + data[key] + "</a>";
		           		html+="<div style='position: absolute; display: none' id=map_id_"+i+" width='400px'>";
		           		html+="<table class= 'float-panel' cellpadding='0' cellspacing='0' border='0'>";
		           		html+="<tr>";
		           		html+="<td style='background-color: rgb(169, 169, 169); text-align: center;'>";
		           		html+="<a data-lat="+dlat+" data-lang="+dlang+" data-coord="+pcoord+" id= WALKING-"+i+" style='color: black; text-decoration:none;' href='javascript:;'>Walk there</a></td></tr>";
		           		html+="<tr><td style='background-color: rgb(169, 169, 169); text-align: center;'><a data-lat="+dlat+" data-lang="+dlang+" data-coord="+pcoord+" id=BICYCLING-"+i+" style='color: black; text-decoration:none;' href='javascript:;'>Bike there</a></td></tr>";
		           		html+="<td style='background-color: rgb(169, 169, 169); text-align: center;'><a data-lat="+dlat+" data-lang="+dlang+" data-coord="+pcoord+" id= DRIVING-"+i+"style='color: black; text-decoration:none;' href='javascript:;'>Drive there</a></td>";
		           		html+= "</tr></table><div class='maps' id=map-"+i+"></div></div>";
		           		html+="</td>";
	        		}
	      		}
	      	html+="<tr>";
	      	document.getElementById("results").innerHTML = '';
	      	document.getElementById("results").innerHTML = html;
	    	}
	    }
	  };

	}

	// Function to initialize the map
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

	// Function to calculate and display routes.
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
	      // Note that Javascript allows us to access the constant
	      // using square brackets and a string value as its
	      // "property."
	      travelMode: google.maps.TravelMode[selectedMode]
	    }, function(response, status) {
	      if (status == 'OK') {
	        directionsDisplay.setDirections(response);
	      } else {
	        window.alert('Directions request failed due to ' + status);
	      }
	    });
	}

	// Function to handle the different arrow functions
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

	// Function to clear results from the result area
	function clearResults() {

	  document.getElementById('results').innerHTML = '';
	  document.getElementById('inter-results').style.display = 'none';
	  document.getElementById("locationName").disabled =true;

	}

	// Function to enable radio button
	function enableRadioButton() {

	  document.getElementById("locationName").disabled = false;

	}

	// Function to disable radio button
	function disableRadioButton() {

	  document.getElementById("locationName").disabled =true;

	}
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEp9G4hsG0TQnBrcuzNCsPW0e64LAYMoA">
 </script>
<HTML><HEAD><TITLE>Travel and Entertainment Search</TITLE></HEAD><BODY>
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
</style>
<div class="FinalResults" style="max-width: 800px; margin: auto; background-color: rgb(242, 242, 242)">
<div class=formForThis style="border-style: groove;">
<H1 style='text-align: center;'><i>Travel and Entertainment Search</i></H1>
<hr>
<FORM METHOD="POST" onsubmit=" submitForm(); return false;" ACTION="">
<b>Keyword</b>  <INPUT id=keyword required></INPUT><BR>
<b>Category</b> <SELECT id= 'selectcategory' name=state>
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
</SELECT><br>
<b>Distance(miles)</b><INPUT id='distance' name=name placeholder="10"></INPUT>

<b>from</b><INPUT id='radio1' checked  onclick="disableRadioButton();" type=radio name=radio value='' required>Here<br>
<INPUT style='margin-left: 299px;' id='radio2' onclick="enableRadioButton();" type=radio name=radio  value=location><INPUT disabled required id='locationName' placeholder="location" name=LocationName><BR>
<INPUT type=submit name="submit" id="submit" value="Search"/> <INPUT type=reset value="clear" onclick="clearResults();"/>
</FORM>
</BODY>
</div>

</div>
<br>
<br>
<div class="FinalResults" style="max-width: 1000px; margin: auto;">
<div id="results">
</div>
</div>
<div class="FinalResults" style="max-width: 800px; margin: auto;">
<div id = 'inter-results' style="display: none;">
<div   style="text-align: center;" id="heading">
</div>
<br>
<div style="text-align: center;">
<p id='reviewtext'>click to show reviews</p>
<img id='upreview' hieght='20' type='up' onclick="arrowFunction(this);" style="visibility: hidden;" width='20' src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png'>
<br>
<img id='downreview'  hieght='20' type='down' onclick="arrowFunction(this);" width='20' src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png'>
</div>
<br>
<br>
<div  style="text-align: center; display: none;" id ="reviews1">
</div>
<br>
<div style="text-align: center;">
<p id='phototext'>click to show photos</p>
<img id='upphoto' hieght='20' type='up' onclick="arrowFunction(this);" style="visibility: hidden;" width='20' src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png'>
<br>
<img id='downphoto' hieght='20' type='down'onclick="arrowFunction(this);" width='20' src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png'>
</div>
<br>
<br>
<div style="text-align: center; display: none;" id = "photos1">
</div>
</div>
</div>
</HTML>
