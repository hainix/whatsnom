google.load('search', '1', {'nocss' : true});

function searchComplete(search_target_id, is_homepage) {
  // Check that we got results
  var news_container = document.getElementById(search_target_id);
  if (is_homepage) {
      var max_articles = 4;
  } else {
      var max_articles = 5;
  }
  if (newsSearch.results && newsSearch.results.length > 0) {
      if (is_homepage) {
          buzz_list = document.createElement('ul');
          buzz_list.className = 'news_list';
          newsSearch.results = shuffle(newsSearch.results);
      } else {
          buzz_list = document.createElement('div');
          buzz_list.className = 'news';
      }

      var articles_rendered = 0;
      for (var i = 0; i < newsSearch.results.length; i++) {

          // Skip non-english and stories without images for filtering purposes on home
          if (newsSearch.results[i].language != 'en' ||
              articles_rendered == max_articles ||
              (is_homepage && newsSearch.results[i].image == undefined)) {
              continue;
          }
          articles_rendered++;

          if (is_homepage) {
              var news_item = document.createElement('li');
              //news_item.className = 'entry entry-without-border group';
          } else {
              var news_item = document.createElement('div');
              news_item.className = 'entry group';
          }

          // Create HTML elements for search results
          // To render dishoom banner on top
          //          var content_link ='http://www.dishoomfilms.com/ex/?s=' + newsSearch.results[i].unescapedUrl.toString();
          var content_link = newsSearch.results[i].unescapedUrl.toString();

          var a = document.createElement('a');
          a.href = content_link;
          a.target = '_blank';
          a.title = newsSearch.results[i].titleNoFormatting;
          a.innerHTML = newsSearch.results[i].titleNoFormatting;

          var a2 = document.createElement('h5');
          a2.appendChild(a);


          // This is all we need for home
          // Otherwise, keep constructing
          var author = document.createElement('span');
          author.className = 'author';
          author.innerHTML = newsSearch.results[i].publisher;
          //+ ' | ' + newsSearch.results[i].publishedDate;

          var snippet = document.createElement('p');
          snippet.className = 'article_summary';
          snippet.innerHTML = newsSearch.results[i].content.replace('...', '');
          snippet.innerHTML = newsSearch.results[i].content;


          var subtitle = document.createElement('p');
          subtitle.className = 'author_teaser';

          var posteddate = newsSearch.results[i].publishedDate;
          if (posteddate) {
              var posteddate_object = new Date(posteddate);
              author.innerHTML =
                  jQuery.timeago(posteddate_object) + ', ' + author.innerHTML;
          }
          subtitle.appendChild(author);


          // If we just want homepage feed, make it quick.
          if (is_homepage) {
              news_item.appendChild(a);
              news_item.appendChild(subtitle)
              buzz_list.appendChild(news_item);
              continue;
          }

          // Construct news item
          if (newsSearch.results[i].image != undefined) {
              var thmb = document.createElement('img');
              thmb.className = 'thumb_image';
              thmb.src = newsSearch.results[i].image.tbUrl;
              //thmb.align = 'right';
              var thmb_container = document.createElement('div');
              thmb_container.className = 'news_item_small_pic';
              thmb_container.appendChild(thmb);
              news_item.appendChild(thmb_container);
          }

          news_item.appendChild(a2);
          news_item.appendChild(subtitle);
          news_item.appendChild(snippet);
          buzz_list.appendChild(news_item);
      }
      news_container.appendChild(buzz_list);

      var loading_indicator = document.getElementById('news_loader');
      loading_indicator.style.visibility = 'hidden';
  }
}

function initializeDishSearch() {
	newsSearch = new google.search.NewsSearch();
	newsSearch.setResultSetSize(8);
	newsSearch.setSearchCompleteCallback(this, searchComplete, ['dish_container']);
	newsSearch.setRestriction(google.search.Search.RESTRICT_EXTENDED_ARGS,
	 {  'ned' : 'in'});

	// var search_term needs to have been declared globally before this
	if (search_term) {
		newsSearch.execute(search_term);
	}
}

function initializeNewsSearch() {
	newsSearch = new google.search.NewsSearch();
	newsSearch.setResultSetSize(8);
	newsSearch.setSearchCompleteCallback(this, searchComplete, ['dish_container']);
	newsSearch.execute('bollywood');
}

function initializeHomeNewsSearch() {
	newsSearch = new google.search.NewsSearch();
	newsSearch.setResultSetSize(8);
	newsSearch.setSearchCompleteCallback(this, searchComplete, ['dish_container', true]);
	newsSearch.execute('bollywood');
}


shuffle = function(o){
    for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
};

