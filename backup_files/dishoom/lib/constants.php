<?php
function get_author_data() {
  return array(
    7931125 =>
    array(
      'name' => 'roontang',
        'external' => false
    ),
    7946279 =>
    array(
      'name' => 'fuzzcho',
        'external' => false
    ),
    7906796 =>
    array(
      'name' => 'nixy',
        'external' => false
    ),
    3 =>
    array(
      'name' => 'varunimal',
      'external' => true,
      'source_name' => 'Varun Shah, Special Contributor, Bollywood to the Point',
      'source_link' => 'http://bollywoodtothepoint.blogspot.com',
    ),
    4 => array(
      'name' => 'hina',
      'external' => true,
      'source_name' => 'Hina Adnan',
    ),
    5 => array(
      'name' => 'sana',
      'external' => true,
      'source_name' => 'Sana Anam Anwar',
    ),
  );
}


$admin_users = array(7906796 => 'nix', 7946279 => 'farraz', 7931125 => 'arun');

function is_admin() {
  global $admin_users;
  $user = get_fb_user();
  $uid = idx($user, 'id');
  return $uid && idx($admin_users, $uid);
}

function get_admin_name($uid = null) {
  if (!$uid) {
    $user = get_fb_user();
    $uid = idx($user, 'id');
  }
  global $admin_users;
  return idx($admin_users, $uid);
}

