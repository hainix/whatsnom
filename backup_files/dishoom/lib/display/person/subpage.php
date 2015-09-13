<?php

final class StarSubpageRenderer {

  public static function renderWebContent($interview_videos,
                                          $top_tagged_celebs) {
    $sliders = array();
    foreach ($top_tagged_celebs as $celeb_list) {
      // Shuffle the top 5, so it seems more dynamic
      $celeb_list['data'] = array_merge(
        shuffle_assoc(array_slice($celeb_list['data'], 0, 5, true)),
        array_slice($celeb_list['data'], 5));

      $section_header = ' Top <span>'.$celeb_list['name'].'</span>';
      $sliders[] = render_carousel($celeb_list['data'],
                                     $section_header,
                                     'person');
    }

    $tweets = get_recent_tweets();
    $tweets_render =
      render_header(
        'Chatter<span> Box</span>',
        'Stars In Their Own Words')
      .render_tweets($tweets, /* limit */ 2, /*show name*/ true);


    $trending_celeb_data = get_trending_celeb_data();
    $trending_celebs = get_objects(array_keys($trending_celeb_data), 'person');

    $trending_celeb = get_object(array_rand($trending_celeb_data), 'person');
    $trending_celeb['oneliner'] = $trending_celeb_data[$trending_celeb['id']];

    /*
    $top_slider = render_banner_rotator(
      $trending_celebs,
      384,
      350,
      'person');
    */
    $top_slider = render_video_unit(array(head($interview_videos)), 380);
    $trending_celebs_render =
      render_header('Shooting <span>Stars</span>',
                    "Current Trending Celebrities")
      .render_highlighted_object_unit($trending_celeb);

    $top =
    '<ul id="three-col" class="top-module short-top-module">
       <li class="first">'
      . $trending_celebs_render
     .'</li>
       <li class="wide">'
        . $top_slider
     .'</li>
       <li class="last">'
        . $tweets_render
     .'</li>'
   .'</ul>';

  return
    $top
    .'<br/>'
    . implode('', $sliders);

  }

  public static function renderMobileContent($interview_videos,
                                             $top_tagged_celebs) {

    $tagged_celebs_render = '';
    foreach ($top_tagged_celebs as $data) {
      $type = $data['name'];
      $top_list = $data['data'];
      shuffle($top_list);
      $unit = render_bubbles(array_slice($top_list, 0, 6, true));
      $tagged_celebs_render .= unit('<h3>Top <span>'.$type.'</span></h3>'.$unit.'<br/>');
    }


    $news = '<h3>The <span>Dish</span></h3>'
      .render_buzz_box('Bollywood Gossip', 'Asking around for juicy gossip...',
                       true);

    $trending_celeb_data = get_trending_celeb_data();
    $trending_celeb = get_object(array_rand($trending_celeb_data), 'person');
    $trending_celeb['oneliner'] = $trending_celeb_data[$trending_celeb['id']];

    $trending_render =
      '<h3>Trending <span>Now</span></h3>'
      .render_highlighted_object_unit($trending_celeb);

    $html =
      '<div class="home-sections">
         <div id="summary" class="group" >
           <div class="group">
             <div class="page group"></div>
             <ul id="portfolio">
               <li class="portfolio hentry first">'
                .$news
             .'</li>
               <li class="portfolio last group">'
                 .$trending_render
                 .$tagged_celebs_render
             .'</li>
             </ul>
           </div>
         </div>
       </div>';
      return $html;
  }
  }
