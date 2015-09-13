<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/RenderUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';

final class Modules {

  public static function renderProfileList($lists, $show_city = false, $featured_list_id = null, $extra_list_items = array()) {
    $me = FacebookUtils::getUser();

    $ret = '<ul class="profile-list">';
    foreach ($lists as $list) {
      $user = ($me && $me['id'] == $list['creator_id'])
        ? $me
        : get_object($list['creator_id'], 'users');
      $profile_image = $user
        ? $user['profile_pic_url']
        : BASE_URL.'/images/no-image.png';
      $num_votes = (int)$list['upvotes'];
      $initial_upvote_render =
        '<strong>'.(sprintf("%d", $num_votes)).'</strong> ';

      $upvote_render = null;
      if ($me) {
        $list_id = $list['id'];
        $existing_assoc =
          DataReadUtils::getAssoc($me['id'], $list_id, 'votes');
        $already_voted = $existing_assoc && !$existing_assoc['deleted'];
        $vote_icon = $already_voted ? 'star-saved.png' : 'star.png';

        $list_unit_id = $list_id . rand(1000, 9999);
        $upvote_render =
        '<div id="add-list-vote-container-'.$list_unit_id.'" class="inline">'
        .'<img src="'.BASE_URL.'images/'.$vote_icon.'" class="inline small-icon" />'
        .$initial_upvote_render
        .'</div>'
      ."<script>
       $(function() {
         $('#add-list-vote-container-".$list_unit_id."').click(function() {
        var formURL = '".BASE_URL."ajax/add_assoc.php?type=votes&target_id=".$list_id."';
        $.ajax({
          type: 'POST',
          url: formURL,
          success: function(data) {
            $('#add-list-vote-container-".$list_unit_id."').html(data).fadeIn();
          }
        });
        return false;
    });
  });
</script>";
      } else {
        $upvote_render =
          '<img src="'.BASE_URL.'images/star.png" class="inline small-icon" />'
          .$initial_upvote_render;
      }
      $city_render =
        $show_city
        ? Cities::getName($list['city']).'<i> | </i>'
        : null;

      $ret .=
        '<li>'
        .RenderUtils::renderLink(
          '<img class="round-profile list-profile" src="'.$profile_image.'"/>',
          'me/?view='.$list['creator_id']
        )
        .'<span class="title-text">'
        .RenderUtils::renderLink(
          ((!$list['qualifier'] && !$list['type']) ? 'Top Overall' : null)
          .( ((!$list['qualifier'] && $list['type'])
              || (!$list['type'] && $list['qualifier']))
            ? ' Top ' : null)
          .ListQualifiers::getName($list['qualifier']).' '
          .ListTypes::getName($list['type']),
          '?l='.$list['id']
        )
        .'</span>'
        .'<span class="meta-text">'
        .($list['id'] == $featured_list_id
          ? '<strong>Featured List</strong> <i>|</i>'
          : null)
        .$city_render
        .$upvote_render
        .'</span>
     </li>';
    }
    foreach ($extra_list_items as $extra_list_item) {
      $ret .= '<li>'.$extra_list_item.'</li>';
    }
    $ret .= '</ul>';
    return $ret;
  }

  public static function renderDesktopSearchForm($query = null) {
    $current_qualifier = $query ? $query->getQualifier() : null;
    $pre_header = $query ? $query->getPreHeader() : null;
    $current_type = $query ? $query->getType() : null;
    $current_city = $query ? $query->getCity() : null;
    $qualifer_render = $current_qualifier ?
      '<div class="styled-select select-category table-form-column">
      <select id="search-qualifier" class="auto-submit-item" name="q">'
      .RenderUtils::renderSelectOptions(
        ListQualifiers::getConstants(),
        $current_qualifier,
        'Best'
      )
      .'</select>
      </div>'
      : null;

    $web_form =
      '<div class="title-form">
        <form class="search-form table-form">
           <div class="table-form-column-short">'.$pre_header.'</div>'
      .$qualifier_render
            .'<div class="styled-select select-cuisine table-form-column">
              <select id="search-type" class="auto-submit-item" name="t">'
      .RenderUtils::renderSelectOptions(
        ListTypes::getConstants(),
        $current_type
      )
      .'</select>
            </div>
            <div class="table-form-column-short">spots in</div>
            <div class="styled-select select-location table-form-column">
              <select id="search-location" class="auto-submit-item" name="c">';

    foreach (Cities::getConstants() as $value => $key) {
      $web_form .= '<option value="'.$key.'" ';
      if ($current_city && $current_city == $key) {
        $web_form .= 'selected="true" ';
      }
      $web_form .= '>'.Cities::getName($key).'</option>';
    }

    $web_form .= '</select>
            </div>
          </form></div>';
    $web_form_js =
      '<script>
      $(".auto-submit-item").change(function() {
          $(this).parents("form").submit();
        });
      </script>';
    return
        '<div class="twelve columns">'
      .'<div id="search-container" class="bubble-container search-container hide-on-mobile">'
      .$web_form . $web_form_js
      .'</div>'
      .'</div>';
  }

  public static function listItem($entry, $spot, $placeholder = false, $editable = false) {
    if ($spot['last_updated']
        && $spot['last_updated'] + (SEC_IN_DAY * 3) < time()) {
      DataWriteUtils::updateSpot($spot['id']);
    }

    $pic_url = ImageUtils::getPicURLforSpot($spot);
    $pic =
      '<img class="item-profile-pic" src="'.$pic_url.'" />';
    if (!$placeholder) {
      $pic =
      '<a itemprop="photo" class="lightbox" href="'.$spot['profile_pic'].'" '
        .'title="'.RenderUtils::noQuotes($spot['name']).'" >'
        .$pic
        .'</a>';
    }

    $maps_link = '';
    if (idx($spot, 'address')) {
      $maps_url = 'http://maps.google.com/?output=embed&f=q&'
        .'source=s_q&hl=en&geocode=&q='.$spot['address'];
      $maps_link =
        '<a itemprop="address" class="fancybox.iframe lightbox" href="'
        .$maps_url.'">'
        .clean_address($spot['address']).'</a>';
    }

    $tags = null;
    if (idx($spot, 'type')) {
      $render_type = ListTypes::getName($spot['type']);
      if (idx($spot, 'tags')) {
        // TODO add tags
        $render_type .= '';
      }
      if ($render_type) {
        $tags ='<span class="subtle">'.$render_type.'</span>';
      }
    }

    $primary_class = null;
    if ($entry['position'] == 1) {
      $primary_class = ' primary-container ';
    }

    $ret =
      '<div class="item-container bubble-container '.$primary_class.'" '
      .'itemscope itemtype="http://schema.org/Restaurant">'
      .$pic
      .'<div class="item-details">'
      .'<div class="right-actions">'
      .'<table class="right-actions-table"><tr><td>'
      .YelpUtils::renderYelpStars($spot);
    if (idx($spot, 'review_count')) {
      $ret .=
        '<div style="text-align: center">'
        .YelpUtils::renderYelpLink(
          $spot,
          idx($spot, 'review_count')
          ? $spot['review_count'].' reviews'
          : null
        )
        .'</div>';
    }
    $ret .= '</td>';

    $user = FacebookUtils::getUser();
    if ($user && !$placeholder && !$editable && $spot && $spot['name']) {
      $spot_id = $spot['id'];
      $existing_bookmark = DataReadUtils::getAssoc($user['id'], $spot_id, 'bookmarks');
      $bookmark_icon_type = $existing_bookmark ? 'heart-saved.png' : 'heart.png';
      $ret .=
      '<td>'
      .'<div title="Favorite '.$spot['name'].'" class="add-bookmark" '
        .'id="add-bookmark-container-'.$spot_id.'">'
      .'<form id="add-bookmark-form-'.$spot_id.'">'
      .'<img src="'.BASE_URL.'images/'.$bookmark_icon_type.'" class="add-bookmark-image"/>'
      .'</form>'
      .'</div>'
      .'</td>'
      ."<script>
       $(function() {
         $('#add-bookmark-container-".$spot_id."').click(function() {
        var formURL = '".BASE_URL."ajax/add_assoc.php?type=bookmarks&target_id=".$spot_id."';
        $.ajax({
          type: 'POST',
          url: formURL,
          success: function(data) {
            $('#add-bookmark-form-".$spot_id."').html(data).fadeIn();
          }
        });
        return false;
    });
  });
</script>";
    }

    $ret .= '</tr></table>';

    $phone = idx($spot, 'phone')
      ? ' · '.$spot['phone']
      : null;

    $spot_name = $placeholder
      ? $spot['name']
      : YelpUtils::renderYelpLink($spot);

    $ret .= '</div>'
      .'<div class="item-title">'.
      '<h4>'
      . ($placeholder
         ? $spot_name
         : ($entry['position'] ? $entry['position'].'. ' : null) .$spot_name)
      .'</h4>'
      .'</div>'
      .$tags
      .'<div class="item-location">'
      .$maps_link
      .$phone
      .'</div>';
    if ($editable) {
      $ret .=
        '<div class="tip-holder">'
        .'<input type="text" name="tip_'.$entry['position'].'" '
        .'value="'.idx($entry, 'tip').'" '
        .'placeholder="what do you order?"/>'
        .'</div>';
    } else {
      $ret .= idx($entry, 'tip')
        ? '<div class="item-tip"><span class="subtle">order:</span> '
        . $entry['tip'].'</div>'
        : '<div class="tip-holder"></div>';
    }
    $ret .= '</div></div>';
    return $ret;
  }
}