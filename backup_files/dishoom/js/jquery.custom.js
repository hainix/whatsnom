jQuery(document).ready(function($){

	$('body').removeClass('no_js').addClass('yes_js');

	$('a.no-link').click(function(){return false;});

	$('.canvas-tabs').yiw_tabs({
    tabNav  : 'ul.tabs',
    tabDivs : '.border-box'
  });


  $('.home-sections .section').each(function(){
    if ( $('.section-content', this).height() < $('.section-title', this).height() )
      $(this).css('min-height', $('.section-title', this).height() );
    });
  });

// tabs plugin
(function($) {
    $.fn.yiw_tabs = function(options) {
        var config = {
            'tabNav': 'ul.tabs',
            'tabDivs': '.containers',
            'currentClass': 'current'
        };

        if (options) $.extend(config, options);

    	this.each(function() {
        	var tabNav = $(config.tabNav, this);
        	var tabDivs = $(config.tabDivs, this);
        	var activeTab;
        	var maxHeight = 0;


            tabDivs.children('div').hide();

    	    if ( $('li.'+config.currentClass+' a', tabNav).length > 0 )
               activeTab = '#' + $('li.'+config.currentClass+' a', tabNav).attr('href').split('#')[1];
        	else
        	   activeTab = '#' + $('li:first-child a', tabNav).attr('href').split('#')[1];

        	$(activeTab).show().addClass('showing');
            $('li:first-child a', tabNav).parents('li').addClass(config.currentClass);

            var change_tab = function(el, id) {
        		$('li.'+config.currentClass, tabNav).removeClass(config.currentClass);
        		el.parents('li').addClass(config.currentClass);

        		$('.showing', tabDivs).fadeOut(200, function(){
        			el.removeClass('showing');
        			$(id).fadeIn(200).addClass('showing');
        		});
            }

        	$('a', tabNav).click(function(){
        		var id = '#' + $(this).attr('href').split('#')[1];
        		var thisLink = $(this);

        		change_tab(thisLink, id);

        		return false;
        	});

        	$('a[href^="#"]', tabDivs).click(function(){
                var hash = $(this).attr('href');

                if ( $(hash, tabDivs).length == 0 )
                    return true;

                change_tab( $('a[href="'+hash+'"]'), hash );

                return false;
            });
        });
    }
})(jQuery);

function getImgHeight(imgSrc) {
    var newImg = new Image();

    newImg.onload = function() {
        var height = newImg.height;
    }

    newImg.src = imgSrc; // this must be done AFTER setting onload

    return height;
}

function getImgWidth(imgSrc) {
    var newImg = new Image();
    var width = 0;

    newImg.onload = function() {
        var width = newImg.width;
    }

    newImg.src = imgSrc; // this must be done AFTER setting onload

    return width;
}


