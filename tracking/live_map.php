<!DOCTYPE html>
<html>
<head>
<title>Live Tracking</title>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
</head>

<body>

<h2>Parcel Live Tracking</h2>
<div id="map" style="height:500px;width:100%;"></div>

<script>
function initMap(){

    var loc = {lat:27.7172,lng:85.3240};

    var map = new google.maps.Map(
        document.getElementById("map"),
        {zoom:10, center:loc}
    );

    new google.maps.Marker({
        position:loc,
        map:map
    });
}

initMap();
</script>

</body>
</html>
