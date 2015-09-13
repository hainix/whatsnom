jQuery(document).ready(function($){

	$('body').removeClass('no_js').addClass('yes_js');

	$('a.no-link').click(function(){return false;});

    $('#nav li > ul.sub-menu li').each(function(){
        n = $('ul.sub-menu', this).length;

        if(n) $(this).addClass('sub');
    });

    var show_dropdown = function()
    {
        var options;

        containerWidth = $('#header').width();
        marginRight = $('#nav ul.level-1 > li').css('margin-right');
        submenuWidth = $('#nav ul.sub-menu').width();
        offsetMenuRight = $(this).position().left + submenuWidth;
        leftPos = -18;

        if ( offsetMenuRight > containerWidth )
            options = { left:leftPos - ( offsetMenuRight - containerWidth ) };
        else
            options = {};

        $('ul.sub-menu:not(ul.sub-menu li > ul.sub-menu), ul.children:not(ul.children li > ul.children)', this).css(options).stop(true, true).fadeIn(300);
    }

    var hide_dropdown = function()
    {
        $('ul.sub-menu:not(ul.sub-menu li > ul.sub-menu), ul.children:not(ul.children li > ul.children)', this).fadeOut(300);
    }

    $('#nav ul > li').hover( show_dropdown, hide_dropdown );

    $('#nav ul > li').each(function(){
        if( $('ul', this).length > 0 )
            $(this).children('a').append('<span class="sf-sub-indicator"> &raquo;</span>')
    });

    $('#nav li ul.sub-menu li, #nav li ul.children li').hover(
        function()
        {
            var options;

            containerWidth = $('#header').width();
            containerOffsetRight = $('#header').offset().left + containerWidth;
            submenuWidth = $('ul.sub-menu, ul.children', this).parent().width();
            offsetMenuRight = $(this).offset().left + submenuWidth * 2;
            leftPos = -10;

            if ( offsetMenuRight > containerOffsetRight )
                $(this).addClass('left');

            $('ul.sub-menu, ul.children', this).stop(true, true).fadeIn(300);
        },

        function()
        {
            $('ul.sub-menu, ul.children', this).fadeOut(300);
        }
    );

    $('#slider.cycle').hover(
        function()
        {
            $('.next, .prev', this).stop(true, true).fadeIn(300);
        },

        function()
        {
            $('.next, .prev', this).fadeOut(300);
        }
    );

    //yiw_lightbox();

	// slider
	if( typeof(yiw_slider_type) != 'undefined' ) {
	   if( yiw_slider_type == 'elegant' ) {
    		$("#slider ul").cycle({
    			easing 	: yiw_slider_elegant_easing,
    	    	fx 		: yiw_slider_elegant_fx,
    			speed 	: yiw_slider_elegant_speed,
    			timeout : yiw_slider_elegant_timeout,
    			before	: function(currSlideElement, nextSlideElement, options, forwardFlag) {
    				var width = parseInt( $('.slider-caption', currSlideElement).outerWidth() );
    				var height = parseInt( $('.slider-caption', currSlideElement).outerHeight() );

    				$('.caption-top', currSlideElement).animate({top:height*-1}, yiw_slider_elegant_caption_speed);
    				$('.caption-bottom', currSlideElement).animate({bottom:height*-1}, yiw_slider_elegant_caption_speed);
    				$('.caption-left', currSlideElement).animate({left:width*-1}, yiw_slider_elegant_caption_speed);
    				$('.caption-right', currSlideElement).animate({right:width*-1}, yiw_slider_elegant_caption_speed);
    			},
    			after	: function(currSlideElement, nextSlideElement, options, forwardFlag) {
    				$('.caption-top', nextSlideElement).animate({top:0}, yiw_slider_elegant_caption_speed);
    				$('.caption-bottom', nextSlideElement).animate({bottom:0}, yiw_slider_elegant_caption_speed);
    				$('.caption-left', nextSlideElement).animate({left:0}, yiw_slider_elegant_caption_speed);
    				$('.caption-right', nextSlideElement).animate({right:0}, yiw_slider_elegant_caption_speed);
    			}
    	    });
        }
    	else if ( yiw_slider_type == 'thumbnails' ) {
    		$("#slider .showcase").awShowcase(
    	    {
    	        content_width: 			960,
    	        content_height: 		308,
    			show_caption:			'show', /* onload/onhover/show */
			    continuous:				true,
    			buttons:				false,
    			auto:                   true,
    			autoHeight:             true,
    			thumbnails:				true,
    			transition:				yiw_slider_thumbnails_fx, /* hslide / vslide / fade */
    			interval:		        yiw_slider_thumbnails_timeout,
    			transition_speed:		yiw_slider_thumbnails_speed,
    			thumbnails_position:	'outside-last', /* outside-last/outside-first/inside-last/inside-first */
    			thumbnails_direction:	'horizontal', /* vertical/horizontal */
    			thumbnails_slidex:		1 /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
    	    });
    	} else if( yiw_slider_type == 'nivo' ) {
            $('#slider.nivo').nivoSlider({
                effect           : yiw_slider_nivo_fx,
                animSpeed        : yiw_slider_nivo_speed,
                pauseTime        : yiw_slider_nivo_timeout,
                directionNav     : yiw_slider_nivo_directionNav,
                directionNavHide : yiw_slider_nivo_directionNavHide,
                controlNav       : yiw_slider_nivo_controlNav
            });
        } else if ( yiw_slider_type == 'cycle' ) {
    		$('#slider.cycle').css({ display : 'block' });
    		$('#slider.cycle .slide').css({ width : $('#slider.cycle').width() });

            yiw_slider_cycle_timeout = yiw_slider_cycle_autoplay ? yiw_slider_cycle_timeout : 0;

            var adjust_height = function (current) {
                var total = $('#slider .slides_control').children().size();
                var next = current + 1;
                // if last slide, set next to first slide
				next = total === next ? 0 : next;

                var nextSlide = $('#slider .slides_control .slide:eq('+ current +')');

				var slideHeight = nextSlide.height();
				var height = slideHeight;
				//var contentHeight = 0;

				var changeHeight = function( contentHeight, slideHeight ) {
                    if ( contentHeight > slideHeight )
				       nextSlide.height( contentHeight );
				}

				if ( $('.featured-image', nextSlide).length > 0 ) {

				    var newImg = new Image();
				    var height;

                    newImg.onload = function() {
                        if ( newImg.width > 500 )
                            height = ( 500 * newImg.height ) / newImg.width;
                        else
                            height = newImg.height;

                        changeHeight( height, slideHeight );
                    }

                    newImg.src = $('.featured-image img', nextSlide).attr('src'); // this must be done AFTER setting onload

				} else if ( $('.video-container', nextSlide).length > 0 ) {
				    var height = $('.video-container', nextSlide).height();
				    changeHeight( height, slideHeight );
				}

				//console.log( current + ' -> ' + ( current + 1 ) + ' Total: ' + total );
				//console.log( slideHeight + ' -> ' + height );
            };

    		$("#slider.cycle").slides({
    			play: yiw_slider_cycle_timeout,
    			width: 960,
    			effect: yiw_slider_cycle_fx,
    			generatePagination: false,
    			slideSpeed: yiw_slider_cycle_speed,
    			fadeSpeed: yiw_slider_cycle_speed,
    			autoHeight: true,
                slidesLoaded: function () {
                    adjust_height(0);
//                     var nextSlide = $('#slider .slides_control .slide.first');
//                     var slideHeight = nextSlide.height();
//
//                     var changeHeight = function( contentHeight, slideHeight ) {
//                         if ( contentHeight > slideHeight )
// 					       $('#slider .slides_control, #slider .slides_control .slide.first').height( contentHeight );
// 					}
//
//                     if ( $('.video-container', nextSlide).length > 0 )
// 					   changeHeight( $('.video-container', nextSlide).height(), slideHeight );
                },
                animationStart: adjust_height,
    			generateNextPrev: true
    		});
        } else if ( yiw_slider_type == 'elastic' ) {
    		$('#slider.elastic').eislideshow({
				easing		: 'easeOutExpo',
				titleeasing	: 'easeOutExpo',
				titlespeed	: 1200,
				autoplay	: yiw_slider_elastic_autoplay,
				slideshow_interval : yiw_slider_elastic_timeout,
				speed       : yiw_slider_elastic_speed,
				animation   : yiw_slider_elastic_animation
// 				slidesLoaded: function() {
//                     $('.ei-slider .ei-slider-loading').hide();
//                 }
            });
        }
    }

	// searchform on header    // autoclean labels
	$elements = $('#header #s, .autoclear');

	$elements.each(function(){
        if( $(this).val() != '' )
			$(this).prev().css('display', 'none');
    });
    $elements.focus(function(){
        if( $(this).val() == '' )
			$(this).prev().css('display', 'none');
    });
    $elements.blur(function(){
        if( $(this).val() == '' )
        	$(this).prev().css('display', 'block');
    });

    //$('a.socials, a.socials-small').tipsy({fade:true, gravity:'s'});

    $('.toggle-content:not(.opened), .content-tab:not(.opened)').hide();
    $('.tab-index a').click(function(){
        $(this).parent().next().slideToggle(300, 'easeOutExpo');
        $(this).parent().toggleClass('tab-opened tab-closed');
        $(this).attr('title', ($(this).attr('title') == 'Close') ? 'Open' : 'Close');
        return false;
    });

    // tabs

    //$('.tabs-container').yiw_tabs({
	$('.canvas-tabs').yiw_tabs({
        tabNav  : 'ul.tabs',
        tabDivs : '.border-box'
    });

	$('.testimonials-list').yiw_tabs({
        tabNav  : 'ul.tabs',
        tabDivs : '.border-box',
        currentClass : 'active'
    });

    $('#slideshow images img').show();

    $('.shipping-calculator-form').show();

    // gallery hover
    $(".gallery-wrap .internal_page_item .overlay").css({opacity:0});
	$(".gallery-wrap .internal_page_item").live( 'mouseover mouseout', function(event){
		if ( event.type == 'mouseover' ) $('.overlay', this).show().stop(true,false).animate({ opacity: 1 }, "fast");
		if ( event.type == 'mouseout' )  $('.overlay', this).animate({ opacity: 0 }, "fast", function(){ $(this).hide() });
	});

	if ( $('body').hasClass('isMobile') && ! $('body').hasClass('iphone') && ! $('body').hasClass('ipad') )
        $('.sf-sub-indicator').parent().click(function(){
            $(this).paretn().toggle( show_dropdown, function(){ document.location = $(this).children('a').attr('href') } )
        });

	// map tab
	$('.header-map .tab-label').click(function(){
        var mapWrap = $('#map-wrap');
        var text = $(this).text();
        var label = $(this);
        var height = $('#map').height();

        if ( $(window).height() - 100 < height )
            height = $(window).height() - 100;

        //console.log( text + ' - ' + header_map.tab_open + ' - ' + header_map.tab_close );

        if ( $(this).hasClass('closed') ) {
            mapWrap.show().animate({height:height}, 500, function(){
                label.removeClass('closed').addClass('opened').text(header_map.tab_close);
            });

        } else if ( $(this).hasClass('opened') ) {
            mapWrap.animate({height:0}, 500, function(){
                $(this).hide();
                label.removeClass('opened').addClass('closed').text(header_map.tab_open);
            });
        }

        return false;
    });

    $('.home-sections .section').each(function(){
        if ( $('.section-content', this).height() < $('.section-title', this).height() )
            $(this).css('min-height', $('.section-title', this).height() );
    });

    $(window).resize(function(){
//         $('#twitter-slider .tweets-list li').each( function() {
//             var width = $(this).width() / $('#twitter-slider').width();
//             $(this).width( $('#twitter-slider').width() * width );
//         } );
    });
});
/*
function yiw_lightbox()
{
    if (typeof jQuery.fn.prettyPhoto != "function")
        return;

    jQuery('a.thumb').hover(

        function()
        {
            jQuery('<a class="zoom">zoom</a>').appendTo(this).css({
				dispay:'block',
				opacity:0,
				height:jQuery(this).children('img').height(),
				width:jQuery(this).children('img').width(),
				'top':jQuery(this).css('padding-top'),
				'left':jQuery(this).css('padding-left'),
				padding:0}).animate({opacity:0.4}, 500);
        },

        function()
        {
            jQuery('.zoom').fadeOut(500, function(){jQuery(this).remove()});
        }
    );
	jQuery("a[rel^='prettyPhoto']").prettyPhoto({
        slideshow:5000,
        theme: yiw_prettyphoto_style,
        autoplay_slideshow:false,
        deeplinking: false,
        show_title:false
    });
}
*/

// tabs plugin
(function($) {
    $.fn.yiw_tabs = function(options) {
        // valori di default
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

        	// height of tabs
//         	$('li', tabNav).each(function(){
//                 var tabHeight = $(this).height();
//                 if ( tabHeight > maxHeight )
//                     maxHeight = tabHeight;
//             });
//             $('li h4', tabNav).each(function(){
//                 $(this).height(maxHeight-40);
//             });

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

(function($) {

    $.fn.sorted = function(customOptions) {
        var options = {
            reversed: false,
            by: function(a) {
                return a.text();
            }
        };

        $.extend(options, customOptions);

        $data = jQuery(this);
        arr = $data.get();
        arr.sort(function(a, b) {

            var valA = options.by($(a));
            var valB = options.by($(b));

            if (options.reversed) {
                return (valA < valB) ? 1 : (valA > valB) ? -1 : 0;
            } else {
                return (valA < valB) ? -1 : (valA > valB) ? 1 : 0;
            }

        });

        return $(arr);

    };

})(jQuery);

jQuery(function($) {

        //yiw_lightbox();
    var read_button = function(class_names) {

        var r = {
            selected: false,
            type: 0
        };

        for (var i=0; i < class_names.length; i++) {

            if (class_names[i].indexOf('selected-') == 0) {
                r.selected = true;
            }

            if (class_names[i].indexOf('segment-') == 0) {
                r.segment = class_names[i].split('-')[1];
            }
        };

        return r;

    };

    var determine_sort = function($buttons) {
        var $selected = $buttons.parent().filter('[class*="selected-"]');
        return $selected.find('a').attr('data-value');
    };

    var determine_kind = function($buttons) {
        var $selected = $buttons.parent().filter('[class*="selected-"]');
        return $selected.find('a').attr('data-value');
    };

    var $preferences = {
        duration: 500,
        adjustHeight: 'auto'
    }

    var $list = jQuery('.gallery-wrap');
    var $data = $list.clone();

    var $controls = jQuery('.portfolio-categories, .gallery-categories');

    $controls.each(function(i) {

        var $control = jQuery(this);
        var $buttons = $control.find('a');
        var height_list = $list.height();

        $buttons.bind('click', function(e) {

            var $button = jQuery(this);
            var $button_container = $button.parent();
            var button_properties = read_button($button_container.attr('class').split(' '));
            var selected = button_properties.selected;
            var button_segment = button_properties.segment;

            if (!selected) {

                $buttons.parent().removeClass();
                $button_container.addClass('selected-' + button_segment);

                var sorting_type = determine_sort($controls.eq(1).find('a'));
                var sorting_kind = determine_kind($controls.eq(0).find('a'));

                if (sorting_kind == 'all') {
                    var $filtered_data = $data.find('li');
                } else {
                    var $filtered_data = $data.find('li.' + sorting_kind);
                }

                var $sorted_data = $filtered_data.sorted({
                    by: function(v) {
                        return $(v).find('strong').text().toLowerCase();
                    }
                });

                $list.quicksand($sorted_data, $preferences, function () {
                        //yiw_lightbox();
                        //Cufon.replace('#portfolio-gallery h6');

                        var current_height = $list.height();
                        $('.hentry-post').animate( { 'min-height':$list.height() }, 300 );



                        var postsPerRow = ( $('.layout-sidebar-right').length > 0 || $('.layout-sidebar-left').length > 0 ) ? 3 : 4;

                        $('.gallery-wrap li')
                            .removeClass('group')
                            .each(function(i){
                                $(this).find('div')
                                    //.removeClass('internal_page_item_first')
                                    .removeClass('internal_page_item_last');

                                if( (i % postsPerRow) == 0 ) {
                                    //$(this).addClass('group');
                                    //$(this).find('div').addClass('internal_page_item_first');
                                } else if((i % postsPerRow) == 2) {
                                    $(this).find('div').addClass('internal_page_item_last');
                                }
                            });

                        $('.gallery-wrap:first').css('height',0);

                });

            }

            e.preventDefault();

        });

    });

});