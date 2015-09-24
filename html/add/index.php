<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';

$num_spots_to_show = ListTypeConfig::NUM_PER_LIST;

$list_id = idx($_GET, 'l');
$type_id = null;
$city_id = null;

$user = FacebookUtils::getUser();
if (!$user) {
  $page = new Page();
  $page
    ->setType(PageTypes::INFO)
    ->setContent(
      RenderUtils::renderMessage(
        'Sign in to add new lists.'
      )
    )->render();
  exit(1);
}

$entries = null;
$spots = null;
$existing_list = $list_id ?  get_object($list_id, 'lists') : null;

// By now we have either enough params to create/fetch a new list
// or an existing list
if (!$existing_list) {
  $type_id = idx($_GET, 't');
  $city_id = $_GET['c'];
}
if (!$type_id && !$city_id && !$existing_list) {
  RenderUtils::go404();
}

if (!$existing_list) {
  $existing_list =
    DataReadUtils::getListForCreator(
      $type_id,
      $city_id,
      $user['id']
    );
}

$entries_keyed_on_position = null;
if ($existing_list && $existing_list['creator_id'] == $user['id']) {
  $type_id = $existing_list['type'];
  $city_id = $existing_list['city'];
  $entries = DataReadUtils::getEntriesForList($existing_list);
  if ($entries) {
    foreach ($entries as $entry) {
      $entries_keyed_on_position[$entry['position']] = $entry;
    }
  }
  $spots =
    $entries
    ? get_objects(array_pull($entries, 'spot_id'), 'spots')
    : null;
}

$query = new ListQuery($type_id, $city_id);
$query->setUser($user);
$creator_id = $user['id'];

$type = $type_id ? ListTypes::getName($type_id) : null;
$city = Cities::getName($city_id);


// NOW WE RENDER
$close_icon = '<a class="x-out" href="#">x</a>';
$search_script =
  "<script>
  jQuery(function(){
    for (var ii = 1; ii <= ".$num_spots_to_show."; ii++) {
    (function() {
      var i = ii;
      var containerName = '#spot_' + i + '_container';
      $('#spot_query_' + i).autocomplete({
        serviceUrl: '/add/source.php?c=".$city_id."',
        noCache: true,
        autoSelectFirst: true,
        triggerSelectOnValidInput: false,
        params: {add_url: '".BASE_URL.'add/new_spot.php?c='.$city_id."&t=".$type_id."'},
        onSelect:
          function(value, data){
            this.value = '';
            $('#spot_' + i).val(value.data);
            $.get(
              '/add/spot.php?s=' + value.data +'&p=' + i,
              function(data) {
                $('#add_spot_' + i).hide();
                var previewContainer = '#preview_spot_' + i;
                $(previewContainer).html(data);
                $(previewContainer).fadeIn('slow');
                var closeIcon = $('<a />', {
                  class: 'x-out',
                });
                closeIcon.click(function() {
                  $('#preview_spot_' + i).hide();
                  $('#add_spot_' + i).show();
                  $('#spot_' + i).val('');
                });
                $(previewContainer).find('.item-title').find('h4')
                  .append(closeIcon);
              });
          },
        });
})();
      }
  });
</script>";

$form_rows = array();
for ($i = 1; $i <= $num_spots_to_show; $i++) {
  $search_input =
    '<input type="text" id="spot_query_'.$i.'" '
    .'placeholder="'. ($i == 1 ? 'Best' : '#'.$i)
    .($type ? ' '.$type : null)
    .' spot?" '
    .'/>';


  $real_entry = idx($entries_keyed_on_position, $i);
  $real_spot = $real_entry ? $spots[$real_entry['spot_id']] : null;
  $preview_display = $real_spot ? 'block' : 'none';
  $add_display = $real_spot ? 'none' : 'block';
  $real_list_item =
    $real_spot
    ? Modules::listItem(
      $real_entry,
      $real_spot,
      $placeholder = false,
      $editable = true
    )
    ."<script>
      var closeIcon = $('<a />', {
        class: 'x-out',
      });
      closeIcon.click(function() {
        $('#preview_spot_".$i."').hide();
        $('#add_spot_".$i."').show();
        $('#spot_".$i."').val('');
      });
      $('#preview_spot_".$i."').find('.item-title').find('h4')
        .append(closeIcon);
      </script>"
    : null;

  $fake_entry = array('position' => $i);
  $fake_spot = array('name' => $search_input);

  $form_rows[] =
    '<div id="preview_spot_'.$i.'" style="display:'.$preview_display.';" >'
    .$real_list_item
    .'</div>'
    .'<div id="add_spot_'.$i.'" style="display:'.$add_display.';">'
    .Modules::listItem($fake_entry, $fake_spot, $placeholder = true)
    .'<input type="hidden" name="spot_'.$i.'" id="spot_'.$i.'" '
    .' value="'.idx($real_entry, 'spot_id').'" '
    .'/>'
    .'</div>';
}

$yelp_attribution =
  '<div align="center">
  <table class="attribution-container">
  <tr>
  <td class="attribution-text">powered by</td>
  <td><img src="images/from-yelp.png" /></td>
  </tr>
  </table>
  </div>';

$yelp_url = 'http://www.yelp.com/search?find_desc='.$type.'&find_loc='.Cities::getName($city_id);
$chow_url = 'http://www.chow.com/search?q='.$type.'+'.$city;
$tip_lists =
'<h3><span>Need </span> Tips?</h3>
<ul class="profile-list">
<li>'
  .RenderUtils::renderExternalLink(
    '<img class="round-profile list-profile" src="'.BASE_URL.'images/yelp-small-logo.png"/>',
    $yelp_url
  )
  .RenderUtils::renderExternalLink(
    'Search Yelp',
    $yelp_url
  )
  .' <span class="user-meta">
     for '.$type.' spots in '.$city
  .'</span>
 </li>
<li>'
  .RenderUtils::renderExternalLink(
    '<img class="round-profile list-profile" src="'.BASE_URL.'images/chow-small-logo.png"/>',
    $chow_url
  )
  .RenderUtils::renderExternalLink(
    'Search Chow',
    $chow_url
  )
  .' <span class="user-meta">
     for "'.$city .' '.$type.'"'
  .'</span>
 </li>';
$tip_lists .= '</ul>';


if ($existing_list) {
  $hidden_fields =
    '<input type="hidden" name="list_id" value="'.$existing_list['id'].'" />';
} else {
$hidden_fields =
  '<input type="hidden" name="type_id" value="'.$type_id.'" />'
  .'<input type="hidden" name="city_id" value="'.$city_id.'" />';
}
$submit_script =
"<script>
$(function() {
    $('#add-spots-submit').click(function() {
        var postData = $('#add-spots-form').serialize();
        $('#form-response').html('<img src=\'".BASE_URL."images/spinner.gif\' />');
        var formURL = '".BASE_URL."add/process_list.php';
        $.ajax({
          type: 'POST',
          url: formURL,
          data: postData,
          success: function(data) {
            $('#form-response').html(data);
          }
        });
        return false;
    });
  });
</script>";

$content =
 '<div class="twelve columns">'
    .'<form id="add-spots-form" action="">'
  .$hidden_fields
    .'<input type="hidden" name="creator_id" value="'.$creator_id.'" />'
     .implode($form_rows, ' ')
     .$search_script
  .'</form>'
 .'</div>'
 .'<div class="four columns sidebar">'
  .'<div id="form-response" class="form-response"></div>'
  .'<input type="submit" value="Save" class="button form-submit-button" id="add-spots-submit">'
  .$submit_script
  .$yelp_attribution
  .$tip_lists
  .RenderUtils::renderContactForm()
 .'</div>'
.'</form>';


$page = new Page();
$page
  ->addHeadContent(

        '<link rel="stylesheet" href="'.BASE_URL.'css/add.css">'
  .'<link rel="stylesheet" href="'.BASE_URL.'css/autocomplete.css">'
  .'<script src="'.BASE_URL.'js/jquery.autocomplete.js"></script>'

  )
  ->setType(PageTypes::ADD)
  ->setQuery($query)
  ->setContent($content)
  ->render();
