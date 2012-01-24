{extends tplextends('webpage/webpage_main')}

{block name='webpage_head' append}
	{js file="/mod/cssjs/js/mootools.js"}
	{js file="/mod/cssjs/js/mootools.more.js"}
	{js file="/mod/map/js/MooGooMaps/Source/Class.SubObjectMapping.js"}
	{js file="/mod/map/js/MooGooMaps/Source/Map.js"}
	{js file="/mod/map/js/MooGooMaps/Source/Map.Extras.js"}
	{js file="/mod/map/js/MooGooMaps/Source/Map.Marker.js"}
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false" ></script>
{/block}

{block name='webpage_body'}
	{* Simple example, override it to fit your needs *}
  <style type="text/css">
     #map_canvas { width: 500px; height: 400px; border:1px solid #000}
  </style>
	<div id="map_canvas"></div>
	<script type="text/javascript">
		document.addEvent('domready', function() {
			var map = new Map('map_canvas', [43.60,3.88], { zoom: 12 });
		});
	</script>
{/block}
