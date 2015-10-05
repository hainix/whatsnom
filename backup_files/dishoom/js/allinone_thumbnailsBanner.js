(function($) {

	var effects_arr=new Array(
			'fade',

			'slideFromLeft',
			'slideFromRight',
			'slideFromTop',
			'slideFromBottom',

			'topBottomDroppingStripes',
			'topBottomDroppingReverseStripes',

			'bottomTopDroppingStripes',
			'bottomTopDroppingReverseStripes',

			'asynchronousDroppingStripes',

			'leftRightFadingStripes',
			'leftRightFadingReverseStripes',

			'topBottomDiagonalBlocks',
			'topBottomDiagonalReverseBlocks',

			'randomBlocks'

	);

	var stripe_width;
	var new_stripe_width;
	var delay_time = 100;
	var delay_time_stripes_step=50;
	var delay_time_blocks_step=25;

	var arrowClicked=false;

	var currentCarouselLeft=0;




	function animate_singular_text(elem) {
		elem.animate({
                opacity: 1,
                left: elem.attr('data-final-left')+'px',
                top: elem.attr('data-final-top')+'px'
              }, elem.attr('data-duration')*1000, function() {
              });
	};




	function animate_texts(current_obj,allinone_thumbnailsBanner_the,bannerControls) {
		jQuery(current_obj.currentImg.attr('data-text-id')).css("display","block");
		var texts = jQuery(current_obj.currentImg.attr('data-text-id')).children();
		jQuery(current_obj.currentImg.attr('data-text-id')).css('width',allinone_thumbnailsBanner_the.width()+'px');
		jQuery(current_obj.currentImg.attr('data-text-id')).css('left',bannerControls.css('left'));//alert (allinone_thumbnailsBanner_the.width());
		jQuery(current_obj.currentImg.attr('data-text-id')).css('top',bannerControls.css('top'));

		var i=0;
		currentText_arr=Array();
		texts.each(function() {
			currentText_arr[i] = jQuery(this);
            currentText_arr[i].css("left",currentText_arr[i].attr('data-initial-left')+'px');
            currentText_arr[i].css("top",currentText_arr[i].attr('data-initial-top')+'px');
            currentText_arr[i].css("opacity",parseInt(currentText_arr[i].attr('data-fade-start'))/100);

            var currentText=currentText_arr[i];
            setTimeout(function() { animate_singular_text(currentText);}, (currentText_arr[i].attr('data-delay')*1000));

            i++;
        });
	};


	function shuffle(o){
		for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
		return o;
	};


    // generate the stripes
	function allinone_thumbnailsBanner_generate_stripes(allinone_thumbnailsBanner_container,options,current_obj){
		jQuery('.stripe', allinone_thumbnailsBanner_container).remove();
    	jQuery('.block', allinone_thumbnailsBanner_container).remove();
        stripe_width = Math.round(allinone_thumbnailsBanner_container.width()/options.numberOfStripes);
		new_stripe_width=stripe_width;
    	for(var i = 0; i < options.numberOfStripes; i++){
			if (i == options.numberOfStripes-1) {
				new_stripe_width=allinone_thumbnailsBanner_container.width()-stripe_width*(options.numberOfStripes-1);
				//alert (stripe_width+'  -  '+new_stripe_width);
			}
			allinone_thumbnailsBanner_container.append(
				jQuery('<div class="stripe"></div>').css({
					opacity:'0',
					left:(stripe_width*i)+'px',
					width:new_stripe_width+'px',
					height:'0px',
					background: 'url("'+ current_obj.currentImg.attr('src') +'") no-repeat -'+ ((stripe_width + (i * stripe_width)) - stripe_width) +'px 0%'
				})
			);
		}
    };



    // generate the blocks
    function allinone_thumbnailsBanner_generate_blocks(allinone_thumbnailsBanner_container,options,current_obj){
		jQuery('.stripe', allinone_thumbnailsBanner_container).remove();
    	jQuery('.block', allinone_thumbnailsBanner_container).remove();
		var block_width = Math.round(allinone_thumbnailsBanner_container.width()/options.numberOfColumns);
		var block_height = Math.round(allinone_thumbnailsBanner_container.height()/options.numberOfRows);

        for(var i = 0; i < options.numberOfRows; i++){
        	for(var j = 0; j < options.numberOfColumns; j++){
        		allinone_thumbnailsBanner_container.append(
					jQuery('<div class="block"></div>').css({
						opacity:'0',
						left:(block_width*j)+'px',
						top:(block_height*i)+'px',
						width:block_width+'px',
						height:block_height+'px',
						background: 'url("'+ current_obj.currentImg.attr('src') +'") no-repeat -'+ ((block_width + (j * block_width)) - block_width) +'px -'+ ((block_height + (i * block_height)) - block_height) +'px'
					})
				);
        	}
		}
    };



	function animate_block(block,i,j,options,allinone_thumbnailsBanner_container){
        var w = block.width();
        var h = block.height();
        block.css({'width':'0'});
        block.css({'height':'0'});
        if (i==options.numberOfRows-1 && j==options.numberOfColumns-1)
        	setTimeout(function(){
				block.animate({ opacity:'1.0', width:w, height:h }, (options.effectDuration*1000)/3 , '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
			}, (delay_time));
        else
			setTimeout(function(){
				block.animate({ opacity:'1.0', width:w, height:h }, (options.effectDuration*1000)/3 );
			}, (delay_time));
		delay_time += delay_time_blocks_step;
	};



    function carouselScroll(direction,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb,current_obj) {
		currentCarouselLeft=allinone_thumbnailsBanner_thumbsHolder.css('left').substr(0,allinone_thumbnailsBanner_thumbsHolder.css('left').lastIndexOf('px'));
		if (direction===1 || direction===-1) {
			current_obj.isCarouselScrolling=true;
			allinone_thumbnailsBanner_thumbsHolder.css('opacity','0.5');
			allinone_thumbnailsBanner_thumbsHolder.animate({
			    opacity: 1,
			    left: '+='+direction*carouselStep
			  }, 500, 'easeOutCubic', function() {
			      // Animation complete.
				  disableCarouselNav(allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb);
				  current_obj.isCarouselScrolling=false;
			});
		} else {
				if ( currentCarouselLeft != (-1) * Math.floor( current_obj.current_img_no/options.numberOfThumbsPerScreen )*carouselStep) {
					current_obj.isCarouselScrolling=true;
					allinone_thumbnailsBanner_thumbsHolder.css('opacity','0.5');
					allinone_thumbnailsBanner_thumbsHolder.animate({
					    opacity: 1,
					    left: (-1) * Math.floor( current_obj.current_img_no/options.numberOfThumbsPerScreen )*carouselStep
					  }, 500, 'easeOutCubic', function() {
					      // Animation complete.
						  disableCarouselNav(allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb);
						  current_obj.isCarouselScrolling=false;
					});
				}
		}


	};

	function disableCarouselNav(allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb) {
		currentCarouselLeft=allinone_thumbnailsBanner_thumbsHolder.css('left').substr(0,allinone_thumbnailsBanner_thumbsHolder.css('left').lastIndexOf('px'));
		//alert (currentCarouselLeft)
		if (currentCarouselLeft <0 ) {
			if (allinone_thumbnailsBanner_carouselLeftNav.hasClass('carouselLeftNavDisabled'))
				allinone_thumbnailsBanner_carouselLeftNav.removeClass('carouselLeftNavDisabled');
		} else {
			allinone_thumbnailsBanner_carouselLeftNav.addClass('carouselLeftNavDisabled');
		}

		if (Math.abs(currentCarouselLeft-carouselStep)<(thumbsHolder_Thumb.width()+thumbMarginLeft)*total_images) {
			if (allinone_thumbnailsBanner_carouselRightNav.hasClass('carouselRightNavDisabled'))
				allinone_thumbnailsBanner_carouselRightNav.removeClass('carouselRightNavDisabled');
		} else {
			allinone_thumbnailsBanner_carouselRightNav.addClass('carouselRightNavDisabled');
		}
	};




    // navigation
	function allinone_thumbnailsBanner_navigation(direction,current_obj,current_effect,allinone_thumbnailsBanner_container,thumbsHolder_Thumbs,imgs,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb){
		var navigateAllowed=true;
		if ((!options.loop && current_obj.current_img_no+direction>=total_images) || (!options.loop && current_obj.current_img_no+direction<0))
			navigateAllowed=false;

		if (navigateAllowed) {
			//hide previous texts
			jQuery(current_obj.currentImg.attr('data-text-id')).css("display","none");

			//deactivate previous
			jQuery(thumbsHolder_Thumbs[current_obj.current_img_no]).removeClass('thumbsHolder_ThumbON');

			//set current img
			if (options.randomizeImages && !current_obj.bottomNavClicked) {
				var rand_no=Math.floor(Math.random() * total_images);
				if (current_obj.current_img_no===rand_no)
					current_obj.current_img_no=Math.floor(Math.random() * total_images);
				else
					current_obj.current_img_no=rand_no;
			} else {
				if (current_obj.current_img_no+direction>=total_images) {
					current_obj.current_img_no=0;
				} else if (current_obj.current_img_no+direction<0) {
					current_obj.current_img_no=total_images-1;
				} else {
					current_obj.current_img_no+=direction;
				}
			}
			current_obj.bottomNavClicked=false;
			//activate current
			jQuery(thumbsHolder_Thumbs[current_obj.current_img_no]).addClass('thumbsHolder_ThumbON');
			//auto scroll carousel if needed
			currentCarouselLeft=allinone_thumbnailsBanner_thumbsHolder.css('left').substr(0,allinone_thumbnailsBanner_thumbsHolder.css('left').lastIndexOf('px'));
			if (current_obj.current_img_no===0 || current_obj.current_img_no===total_images-1) {
				carouselScroll(0,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb,current_obj);
			} else {
				carouselScroll(1001,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb,current_obj);
			}

			current_obj.currentImg = jQuery(imgs[current_obj.current_img_no]);
            if(!current_obj.currentImg.is('img')){
            	current_obj.currentImg = current_obj.currentImg.find('img:first');
            }

			//set current_effect
			if(current_obj.currentImg.attr('data-transition')){
					current_effect = current_obj.currentImg.attr('data-transition');
					if (current_effect=='random') {
								current_effect=effects_arr[Math.floor(Math.random()*(effects_arr.length))];
					}
      } else if (options.defaultEffect!='random') {
            	current_effect=options.defaultEffect;
      } else {
            	current_effect=effects_arr[Math.floor(Math.random()*(effects_arr.length))];
      }

			//alert(current_obj.current_img_no);
			current_obj.effectIsRunning=true;
			if(current_effect == 'fade' || current_effect == 'slideFromLeft' || current_effect == 'slideFromRight' || current_effect == 'slideFromTop' || current_effect == 'slideFromBottom'){
				allinone_thumbnailsBanner_generate_stripes(allinone_thumbnailsBanner_container,options,current_obj);
				var first_stripe = jQuery('.stripe:first', allinone_thumbnailsBanner_container);

				if (current_effect == 'fade') {
					first_stripe.css({
	                    'height': '100%',
	                    'width': allinone_thumbnailsBanner_container.width() + 'px'
	                });
					first_stripe.animate({ opacity:'1.0' }, (options.effectDuration*2000), '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
				}

				if (current_effect == 'slideFromLeft') {
					first_stripe.css({
	                    'height': '100%',
	                    'width': '0'
	                });
					first_stripe.animate({ opacity:'1.0', width:allinone_thumbnailsBanner_container.width() + 'px' }, (options.effectDuration*1000), '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
				}

				if (current_effect == 'slideFromRight') {
					first_stripe.css({
	                    'height': '100%',
	                    'width':  '0',
	                    'left': allinone_thumbnailsBanner_container.width()+5 + 'px'
	                });
					first_stripe.animate({ opacity:'1.0', left:'0', 'width':  allinone_thumbnailsBanner_container.width() + 'px' }, (options.effectDuration*1000), '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
				}

				if (current_effect == 'slideFromTop') {
					first_stripe.css({
	                    'height': '0',
	                    'width': allinone_thumbnailsBanner_container.width() + 'px'
	                });
					first_stripe.animate({ opacity:'1.0', height:allinone_thumbnailsBanner_container.height() + 'px' }, (options.effectDuration*1000), '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
				}

				if (current_effect == 'slideFromBottom') {
					first_stripe.css({
	                    'height': '0',
	                    'width': allinone_thumbnailsBanner_container.width() + 'px',
	                    'top': allinone_thumbnailsBanner_container.height() + 'px'
	                });
					first_stripe.animate({ opacity:'1.0', top:0, height:allinone_thumbnailsBanner_container.height() + 'px' }, (options.effectDuration*1000), '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
				}

			}

			if(current_effect.indexOf('Stripes')>=0) {
				allinone_thumbnailsBanner_generate_stripes(allinone_thumbnailsBanner_container,options,current_obj);
				if (current_effect.indexOf('Reverse')>=0){
					var stripes = jQuery('.stripe', allinone_thumbnailsBanner_container).myReverse();
				} else {
					var stripes = jQuery('.stripe', allinone_thumbnailsBanner_container);
				}
				delay_time = 100;
				i = 0;
				stripes.each(function(){
					var stripe = jQuery(this);
					//setting the css for stripes according to current effect
					if(current_effect=='topBottomDroppingStripes' || current_effect=='topBottomDroppingReverseStripes')
						stripe.css({ 'top': '0px' });
					if(current_effect=='bottomTopDroppingStripes' || current_effect=='bottomTopDroppingReverseStripes')
						stripe.css({ 'bottom': '0px' });
					if(current_effect=='leftRightFadingStripes' || current_effect=='leftRightFadingReverseStripes')
						stripe.css({ 'top': '0px', 'height':'100%', 'width':'0' });
					if (current_effect=='asynchronousDroppingStripes') {
						if (i % 2)
							stripe.css({ 'top': '0px' });
						else
							stripe.css({ 'bottom': '0px' });
					}

					if(current_effect=='leftRightFadingStripes' || current_effect=='leftRightFadingReverseStripes') {
						var aux_stripe_width=stripe_width;
						if ( (current_effect=='leftRightFadingStripes' && i == options.numberOfStripes-1) || (current_effect=='leftRightFadingReverseStripes' && i==0) )
							aux_stripe_width=new_stripe_width;

						if(i == options.numberOfStripes-1){
							setTimeout(function(){
								stripe.animate({ width:aux_stripe_width+'px', opacity:'1.0' }, (options.effectDuration*800), '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
							}, (delay_time));
						} else {
							setTimeout(function(){
								stripe.animate({ width:aux_stripe_width+'px', opacity:'1.0' }, (options.effectDuration*800) );
							}, (delay_time));
						}
					} else {
							if(i == options.numberOfStripes-1){
								setTimeout(function(){
									stripe.animate({ height:'100%', opacity:'1.0' }, (options.effectDuration*1000), '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
								}, (delay_time));
							} else {
								setTimeout(function(){
									stripe.animate({ height:'100%', opacity:'1.0' }, (options.effectDuration*1000) );
								}, (delay_time));
							}
					}
					delay_time += delay_time_stripes_step;
					i++;
				});
			} //if stripes


			if(current_effect.indexOf('Blocks')>=0) {
				allinone_thumbnailsBanner_generate_blocks(allinone_thumbnailsBanner_container,options,current_obj);
				if (current_effect.indexOf('Reverse')>=0){
					var blocks = jQuery('.block', allinone_thumbnailsBanner_container).myReverse();
				} else if (current_effect=='randomBlocks') {
					var blocks = shuffle(jQuery('.block', allinone_thumbnailsBanner_container));
				} else {
					var blocks = jQuery('.block', allinone_thumbnailsBanner_container);
				}
				delay_time = 100;

				if (current_effect=='randomBlocks') {
					i=0;
					var total_blocks = options.numberOfRows*options.numberOfColumns;
					blocks.each(function(){
						var block = jQuery(this);
		                var w = block.width();
		                var h = block.height();
		                block.css({'width':'0'});
		                block.css({'height':'0'});
						if(i == total_blocks-1){
		                	setTimeout(function(){
								block.animate({ opacity:'1.0', width:w, height:h }, (options.effectDuration*1000)/3 , '', function(){ allinone_thumbnailsBanner_container.trigger('effectComplete'); });
							}, (delay_time));
						} else {
							setTimeout(function(){
								block.animate({ opacity:'1.0', width:w, height:h }, (options.effectDuration*1000)/3 );
							}, (delay_time));
						}
						delay_time += delay_time_blocks_step;
						i++;
					});
				} else {

						var row_i=0;
						var col_i=0;
						var blocks_arr=new Array();
						blocks_arr[row_i] = new Array();
						blocks.each(function(){
								blocks_arr[row_i][col_i] = jQuery(this);
								col_i++;
								if(col_i == options.numberOfColumns){
									row_i++;
									col_i = 0;
									blocks_arr[row_i] = new Array();
								}
						});


						//first part
						row_i=0;
						col_i=0;
						delay_time = 100;
						var block = jQuery(blocks_arr[row_i][col_i]);
						animate_block(block,0,0,options,allinone_thumbnailsBanner_container);
						while (row_i<options.numberOfRows-1 || col_i<options.numberOfColumns-1) {
							if (row_i<options.numberOfRows-1)
								row_i++;
							if (col_i<options.numberOfColumns-1)
								col_i++;

							i=row_i;
							if (col_i<row_i && options.numberOfRows>options.numberOfColumns)
								i=row_i-col_i;
							j=0;
							if (row_i<col_i && options.numberOfRows<options.numberOfColumns)
								j=col_i-row_i;
							while (i>=0 && j<=col_i) {
								var block = jQuery(blocks_arr[i--][j++]);
								animate_block(block,i,j,options,allinone_thumbnailsBanner_container);
								//alert (i+' - '+j);
							}

						}


						//last part
						if (options.numberOfRows<options.numberOfColumns)
							delay_time-=(options.numberOfRows-1)*delay_time_blocks_step;
						else
							delay_time-=(options.numberOfColumns-1)*delay_time_blocks_step;

						limit_i=0;
						limit_j=col_i-row_i;

						while (limit_i<row_i && limit_j<col_i) {
							i=row_i+1; //options.numberOfRows-1;
							j=limit_j;
							while (i>limit_i && j<col_i) {
								i=i-1;
								j=j+1;
								var block = jQuery(blocks_arr[i][j]);
								animate_block(block,i,j,options,allinone_thumbnailsBanner_container);
							}
							limit_i++;
							limit_j++;
						}

				} // else randomBlocks
			} // if blocks

		} // if navigateAllowed

	};













	$.fn.allinone_thumbnailsBanner = function(options) {

		var options = $.extend({},$.fn.allinone_thumbnailsBanner.defaults, options);

		return this.each(function() {
			var allinone_thumbnailsBanner_the = jQuery(this);
			allinone_thumbnailsBanner_the.css("display","block");

			//the controllers
			var allinone_thumbnailsBanner_wrap = jQuery('<div></div>').addClass('allinone_thumbnailsBanner').addClass(options.skin);
			var bannerControls = jQuery('<div class="bannerControls">   <div class="leftNav"></div>   <div class="rightNav"></div>    <div class="thumbsHolderWrapper"><div class="thumbsHolderVisibleWrapper"><div class="thumbsHolder"></div></div></div>    </div>');
			allinone_thumbnailsBanner_the.wrap(allinone_thumbnailsBanner_wrap);
			allinone_thumbnailsBanner_the.after(bannerControls);

			if (!options.showAllControllers)
				bannerControls.css("display","none");

			//the elements
			var allinone_thumbnailsBanner_container = allinone_thumbnailsBanner_the.parent('.allinone_thumbnailsBanner');
			var bannerControls = jQuery('.bannerControls', allinone_thumbnailsBanner_container);

			var allinone_thumbnailsBanner_leftNav = jQuery('.leftNav', allinone_thumbnailsBanner_container);
			var allinone_thumbnailsBanner_rightNav = jQuery('.rightNav', allinone_thumbnailsBanner_container);
			allinone_thumbnailsBanner_leftNav.css("display","none");
			allinone_thumbnailsBanner_rightNav.css("display","none");
			if (options.showNavArrows) {
				if (options.showOnInitNavArrows) {
					allinone_thumbnailsBanner_leftNav.css("display","block");
					allinone_thumbnailsBanner_rightNav.css("display","block");
				}
			}


			var carouselStep=0;

			var allinone_thumbnailsBanner_thumbsHolderWrapper = jQuery('.thumbsHolderWrapper', allinone_thumbnailsBanner_container);
			var allinone_thumbnailsBanner_thumbsHolderVisibleWrapper = jQuery('.thumbsHolderVisibleWrapper', allinone_thumbnailsBanner_container);
			var allinone_thumbnailsBanner_thumbsHolder = jQuery('.thumbsHolder', allinone_thumbnailsBanner_container);

			var allinone_thumbnailsBanner_carouselLeftNav;
			var allinone_thumbnailsBanner_carouselRightNav;
			allinone_thumbnailsBanner_carouselLeftNav=jQuery('<div class="carouselLeftNav"></div>');
			allinone_thumbnailsBanner_carouselRightNav=jQuery('<div class="carouselRightNav"></div>');
			allinone_thumbnailsBanner_thumbsHolderWrapper.append(allinone_thumbnailsBanner_carouselLeftNav);
			allinone_thumbnailsBanner_thumbsHolderWrapper.append(allinone_thumbnailsBanner_carouselRightNav);
			allinone_thumbnailsBanner_carouselRightNav.css('right','0');

			allinone_thumbnailsBanner_thumbsHolder.css('width',allinone_thumbnailsBanner_carouselLeftNav.width()+'px');

			var thumbMarginLeft=0;

			if (!options.showThumbs || !options.showOnInitThumbs)
				allinone_thumbnailsBanner_thumbsHolderWrapper.css("display","none");

			//the vars


			var current_effect=options.defaultEffect;
			var total_images=0;
			var current_obj = {
					current_img_no:0,
					currentImg:0,
					isCarouselScrolling:false,
					bottomNavClicked:false,
					effectIsRunning:false
				};
			var timeoutID; // the autoplay timeout ID
			var mouseOverBanner=false;


			var i = 0;



			//set banner size
			allinone_thumbnailsBanner_container.width(options.width);
			allinone_thumbnailsBanner_container.height(options.height);

			bannerControls.width('100%');
			bannerControls.height('100%');

			//get images
			var imgs = allinone_thumbnailsBanner_the.children();
			var thumbsHolder_Thumb;
			var thumbsHolder_MarginTop=0;
			imgs.each(function() {
	            current_obj.currentImg = jQuery(this);
	            if(!current_obj.currentImg.is('img')){
	            	current_obj.currentImg = current_obj.currentImg.find('img:first');
	            }

	            if(current_obj.currentImg.is('img')){
	            	current_obj.currentImg.css('display','none');
	            	total_images++;


		            //generate thumbsHolder
					var image_name = current_obj.currentImg.attr('src').substr(current_obj.currentImg.attr('src').lastIndexOf('/'),current_obj.currentImg.attr('src').length);
          var thumb_src = current_obj.currentImg.attr('data-thumb-src');
					thumbsHolder_Thumb = jQuery('<div class="thumbsHolder_ThumbOFF" rel="'+ (total_images-1) +'"><img src="'+ thumb_src + '"></div>');
		            allinone_thumbnailsBanner_thumbsHolder.append(thumbsHolder_Thumb);

		            thumbMarginLeft=Math.floor( (allinone_thumbnailsBanner_thumbsHolderWrapper.width()-allinone_thumbnailsBanner_carouselLeftNav.width()-allinone_thumbnailsBanner_carouselRightNav.width()-thumbsHolder_Thumb.width()*options.numberOfThumbsPerScreen)/(options.numberOfThumbsPerScreen-1) );
		            //alert (thumbMarginLeft);
		            allinone_thumbnailsBanner_thumbsHolder.css('width',allinone_thumbnailsBanner_thumbsHolder.width()+thumbMarginLeft+thumbsHolder_Thumb.width()+'px');
		            //alert (thumbMarginLeft+' - '+allinone_thumbnailsBanner_thumbsHolderWrapper.width()+' - '+allinone_thumbnailsBanner_carouselLeftNav.width()+' - '+allinone_thumbnailsBanner_carouselRightNav.width()+' - '+thumbsHolder_Thumb.width()+' - '+options.numberOfThumbsPerScreen);
		            if ( total_images<=1 ) {
		            	thumbsHolder_Thumb.css('margin-left',Math.floor( ( allinone_thumbnailsBanner_thumbsHolderWrapper.width()-allinone_thumbnailsBanner_carouselLeftNav.width()-allinone_thumbnailsBanner_carouselRightNav.width()-(thumbMarginLeft+thumbsHolder_Thumb.width())*(options.numberOfThumbsPerScreen-1) - thumbsHolder_Thumb.width() )/2 )+'px');
		            } else {
		            	thumbsHolder_Thumb.css('margin-left',thumbMarginLeft+'px');
		            }


		            thumbsHolder_MarginTop=parseInt((allinone_thumbnailsBanner_thumbsHolderWrapper.height()-parseInt(thumbsHolder_Thumb.css('height').substring(0, thumbsHolder_Thumb.css('height').length-2)))/2);
		            //alert (thumbsHolder_MarginTop);
		            //thumbsHolder_Thumb.css('margin-top',thumbsHolder_MarginTop+'px');
	            }
	            //alert (thumbsHolder_Width)
	        });

			//alert (allinone_thumbnailsBanner_thumbsHolderVisibleWrapper.width());
			allinone_thumbnailsBanner_thumbsHolderVisibleWrapper.css('width',allinone_thumbnailsBanner_thumbsHolderVisibleWrapper.width()-allinone_thumbnailsBanner_carouselLeftNav.width()-allinone_thumbnailsBanner_carouselRightNav.width());
			allinone_thumbnailsBanner_thumbsHolderVisibleWrapper.css('left',allinone_thumbnailsBanner_carouselLeftNav.width());

			carouselStep=(thumbsHolder_Thumb.width()+thumbMarginLeft)*options.numberOfThumbsPerScreen;
			//disable left nav
			if (Math.floor( current_obj.current_img_no/options.numberOfThumbsPerScreen ) === 0)
				allinone_thumbnailsBanner_carouselLeftNav.addClass('carouselLeftNavDisabled');

			//disable right nav
			if (Math.floor( current_obj.current_img_no/options.numberOfThumbsPerScreen ) == Math.floor( total_images/options.numberOfThumbsPerScreen ))
				allinone_thumbnailsBanner_carouselRightNav.addClass('carouselRightNavDisabled');

			allinone_thumbnailsBanner_thumbsHolderWrapper.css("top",options.height+'px');

			if (options.thumbsReflection>0)
				thumbsHolder_MarginTop=thumbsHolder_MarginTop-7;
			var img_inside = jQuery('.thumbsHolder_ThumbOFF', allinone_thumbnailsBanner_container).find('img:first');
			img_inside.css("margin-top",thumbsHolder_MarginTop+"px");

			//create reflection
			var op = { opacity: options.thumbsReflection/100 };
			img_inside.reflect(op);




	        //initialize first number image
			current_obj.current_img_no = options.firstImg;
			if (options.firstImg>total_images)
				current_obj.current_img_no=total_images;
			if (options.firstImg<0)
				current_obj.current_img_no=0;


			//initialize first image number if randomize option is set
			if(options.randomizeImages){
	        	current_obj.current_img_no = Math.floor(Math.random() * total_images);
	        }



	        //Get first image (using initialized above current_obj.current_img_no) and init first bg
	        if(jQuery(imgs[current_obj.current_img_no]).is('img')){
	            current_obj.currentImg = jQuery(imgs[current_obj.current_img_no]);
	        } else {
	            current_obj.currentImg = jQuery(imgs[current_obj.current_img_no]).find('img:first');
	        }
	        allinone_thumbnailsBanner_container.css('background','url("'+ current_obj.currentImg.attr('src') +'") no-repeat');




			if (options.enableTouchScreen) {
				var randomNo=Math.floor(Math.random()*100000);

				allinone_thumbnailsBanner_container.wrap('<div id="bannerWithThumbnailsParent_'+randomNo+'" style="position:relative;" />');
				jQuery('#bannerWithThumbnailsParent_'+randomNo).width(options.width+1);
				jQuery('#bannerWithThumbnailsParent_'+randomNo).height(options.height);
				//jQuery('#bannerWithThumbnailsParent_'+randomNo).css('overflow','hidden');
				//jQuery('#bannerWithThumbnailsParent_'+randomNo).css('border','1px solid #ff0000');

				allinone_thumbnailsBanner_container.css('cursor','url(skins/hand.cur),url(skins/hand.cur),move');
				allinone_thumbnailsBanner_container.css('left','0px');
				allinone_thumbnailsBanner_container.css('position','absolute');

				rightVal=parseInt(allinone_thumbnailsBanner_rightNav.css('right').substring(0, allinone_thumbnailsBanner_rightNav.css('right').length-2));

				//alert(allinone_thumbnailsBanner_container.parent().attr('id'));

				//jQuery("body").css("overflow", "hidden");

				allinone_thumbnailsBanner_container.mousedown(function() {
					rightVal=parseInt(allinone_thumbnailsBanner_rightNav.css('right').substring(0, allinone_thumbnailsBanner_rightNav.css('right').length-2));
					if (rightVal<0 && !arrowClicked) {
						allinone_thumbnailsBanner_rightNav.css('visibility','hidden');
						allinone_thumbnailsBanner_leftNav.css('visibility','hidden');
						allinone_thumbnailsBanner_rightNav.css('right','0');
					}
				});

				allinone_thumbnailsBanner_container.mouseup(function() {
					arrowClicked=false;
					if (rightVal<0) {
						allinone_thumbnailsBanner_rightNav.css('right',rightVal+'px');
						allinone_thumbnailsBanner_rightNav.css('visibility','visible');
						allinone_thumbnailsBanner_leftNav.css('visibility','visible');
					}
				});

				allinone_thumbnailsBanner_container.draggable({
					axis: 'x',
					containment: 'parent',
					start: function(event, ui) {
						origLeft=jQuery(this).css('left');
					},
					stop: function(event, ui) {
						if (!current_obj.effectIsRunning) {
							finalLeft=jQuery(this).css('left');
							direction=1;
							if (origLeft<finalLeft) {
								direction=-1;
							}
							//alert (origLeft+'<'+finalLeft+'-'+direction);
							allinone_thumbnailsBanner_navigation(direction,current_obj,current_effect,allinone_thumbnailsBanner_container,thumbsHolder_Thumbs,imgs,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb);
						}
						if (rightVal<0) {
							allinone_thumbnailsBanner_rightNav.css('right',rightVal+'px');
							allinone_thumbnailsBanner_rightNav.css('visibility','visible');
							allinone_thumbnailsBanner_leftNav.css('visibility','visible');
						}
						jQuery(this).css('left','0px');
					}
				});
			}



	        //generate the text for first image
			animate_texts(current_obj,allinone_thumbnailsBanner_the,bannerControls);












	        //Event when Animation finishes
			allinone_thumbnailsBanner_container.bind('effectComplete', function(){
				current_obj.effectIsRunning=false;
				allinone_thumbnailsBanner_container.css('background','url("'+ current_obj.currentImg.attr('src') +'") no-repeat');

				animate_texts(current_obj,allinone_thumbnailsBanner_the,bannerControls);

				if (options.autoPlay>0 && total_images>1 && !mouseOverBanner) {
					clearTimeout(timeoutID);
					timeoutID=setTimeout(function(){ allinone_thumbnailsBanner_navigation(1,current_obj,current_effect,allinone_thumbnailsBanner_container,thumbsHolder_Thumbs,imgs,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb)},options.autoPlay*1000);
				}
	        }); //bind







			//pause on hover
			allinone_thumbnailsBanner_container.mouseenter(function() {
				mouseOverBanner=true;
				clearTimeout(timeoutID);
				if (options.autoHideNavArrows && options.showNavArrows) {
					allinone_thumbnailsBanner_leftNav.css("display","block");
					allinone_thumbnailsBanner_rightNav.css("display","block");
				}

				//alert (options.autoHideThumbs+' && '+options.showThumbs);

				if (options.autoHideThumbs && options.showThumbs) {
					allinone_thumbnailsBanner_thumbsHolderWrapper.css("display","block");

				}
			});

			allinone_thumbnailsBanner_container.mouseleave(function() {
				mouseOverBanner=false;
				if (options.autoHideNavArrows && options.showNavArrows) {
					allinone_thumbnailsBanner_leftNav.css("display","none");
					allinone_thumbnailsBanner_rightNav.css("display","none");
				}
				if (options.autoHideThumbs && options.showThumbs) {
					allinone_thumbnailsBanner_thumbsHolderWrapper.css("display","none");
				}
				if (options.autoPlay>0 && total_images>1) {
					clearTimeout(timeoutID);
					timeoutID=setTimeout(function(){ allinone_thumbnailsBanner_navigation(1,current_obj,current_effect,allinone_thumbnailsBanner_container,thumbsHolder_Thumbs,imgs,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb)},options.autoPlay*1000);
				}
			});




			//controllers
			allinone_thumbnailsBanner_leftNav.mousedown(function() {
				arrowClicked=true;
				if (!current_obj.effectIsRunning) {
					clearTimeout(timeoutID);
					allinone_thumbnailsBanner_navigation(-1,current_obj,current_effect,allinone_thumbnailsBanner_container,thumbsHolder_Thumbs,imgs,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb);
				}
			});
			allinone_thumbnailsBanner_leftNav.mouseup(function() {
				arrowClicked=false;
			});
			allinone_thumbnailsBanner_rightNav.mousedown(function() {
				arrowClicked=true;
				if (!current_obj.effectIsRunning) {
					clearTimeout(timeoutID);
					allinone_thumbnailsBanner_navigation(1,current_obj,current_effect,allinone_thumbnailsBanner_container,thumbsHolder_Thumbs,imgs,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb);
				}
			});
			allinone_thumbnailsBanner_rightNav.mouseup(function() {
				arrowClicked=false;
			});





			//bottom nav
			var thumbsHolder_Thumbs=jQuery('.thumbsHolder_ThumbOFF', allinone_thumbnailsBanner_container);
			thumbsHolder_Thumbs.mousedown(function() {
				arrowClicked=true;
				if (!current_obj.effectIsRunning) {
					var currentBut=jQuery(this);
					var i=currentBut.attr('rel');
					//deactivate previous
					jQuery(thumbsHolder_Thumbs[current_obj.current_img_no]).removeClass('thumbsHolder_ThumbON');

					current_obj.bottomNavClicked=true;
					current_obj.current_img_no=i-1;
					allinone_thumbnailsBanner_navigation(1,current_obj,current_effect,allinone_thumbnailsBanner_container,thumbsHolder_Thumbs,imgs,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb);
				}
			});
			thumbsHolder_Thumbs.mouseup(function() {
				arrowClicked=false;
			});

			thumbsHolder_Thumbs.mouseenter(function() {
				var currentBut=jQuery(this);
				var i=currentBut.attr('rel');

				currentBut.addClass('thumbsHolder_ThumbON');
			});

			thumbsHolder_Thumbs.mouseleave(function() {
				var currentBut=jQuery(this);
				var i=currentBut.attr('rel');

				if (current_obj.current_img_no!=i)
					currentBut.removeClass('thumbsHolder_ThumbON');
			});


			//carousel controllers
			allinone_thumbnailsBanner_carouselLeftNav.click(function() {
				if (!current_obj.isCarouselScrolling) {
					currentCarouselLeft=allinone_thumbnailsBanner_thumbsHolder.css('left').substr(0,allinone_thumbnailsBanner_thumbsHolder.css('left').lastIndexOf('px'));

					if (currentCarouselLeft <0 )
						carouselScroll(1,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb,current_obj);
				}
			});


			allinone_thumbnailsBanner_carouselRightNav.click(function() {
				if (!current_obj.isCarouselScrolling) {
					currentCarouselLeft=allinone_thumbnailsBanner_thumbsHolder.css('left').substr(0,allinone_thumbnailsBanner_thumbsHolder.css('left').lastIndexOf('px'));
					if (Math.abs(currentCarouselLeft-carouselStep)<(thumbsHolder_Thumb.width()+thumbMarginLeft)*total_images)
						carouselScroll(-1,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb,current_obj);
				}
			});







			//first start autoplay
			jQuery(thumbsHolder_Thumbs[current_obj.current_img_no]).addClass('thumbsHolder_ThumbON');
			if (options.autoPlay>0 && total_images>1) {
				timeoutID=setTimeout(function(){ allinone_thumbnailsBanner_navigation(1,current_obj,current_effect,allinone_thumbnailsBanner_container,thumbsHolder_Thumbs,imgs,allinone_thumbnailsBanner_thumbsHolder,allinone_thumbnailsBanner_carouselLeftNav,allinone_thumbnailsBanner_carouselRightNav,options,thumbMarginLeft,carouselStep,total_images,thumbsHolder_Thumb)},options.autoPlay*1000);
			}


		});
	};

	//reverse effect
	$.fn.myReverse = [].reverse;

	//
	// plugin skins
	//
	$.fn.allinone_thumbnailsBanner.defaults = {
			skin: 'cool',
			width:960,
			height:384,
			randomizeImages: false,
			firstImg:0,
			numberOfStripes:20,
			numberOfRows:5,
			numberOfColumns:10,
			defaultEffect:'random',
			effectDuration:0.5,
			autoPlay:4,
			loop:true,
			showAllControllers:true,
			showNavArrows:true,
			showOnInitNavArrows:true, // o1
			autoHideNavArrows:true, // o1
			showThumbs:true,
			showOnInitThumbs:true,
			autoHideThumbs:false,
			numberOfThumbsPerScreen:6,
			thumbsReflection:50,
			enableTouchScreen:true
	};



})(jQuery);

