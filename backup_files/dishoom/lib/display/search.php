<?php
//config: http://www.brandspankingnew.net/specials/ajax_autosuggest/ajax_autosuggest_autocomplete.html

function render_search_form() {
  return
    '
    <input type="text" id="searchBox" size="30" value="" placeholder="Search for Movies"/>
    <script type="text/javascript">
      $("#searchBox").smartSuggest({
        noResultsText: "No Results Found.",
        src: "'.BASE_URL.'ajax/search.php",
        fillBox: true,
        executeCode: false,
      });
    </script>';
}

function get_search_header() {
  return
		'<script type="text/javascript" src="'.BASE_URL.'js/jquery.smartsuggest.js" charset="utf-8"></script>
		<link rel="stylesheet" href="'.BASE_URL.'css/jquery.smartsuggest.css" type="text/css" media="screen" charset="utf-8" />';
}


?>