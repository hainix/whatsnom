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


		function animate_singular_text(elem) {
			elem.animate({
	                opacity: 1,
	                left: elem.attr('data-final-left')+'px',
	                top: elem.attr('data-final-top')+'px'
	              }, elem.attr('data-duration')*1000, function() {
	                //alert (elem.attr('data-initial-left'));
	              });
		};




		function animate_texts(current_obj,allinone_bannerRotator_the,bannerControls) {
			//alert (current_obj.currentImg.attr('data-text-id'))
			//jQuery(current_obj.currentImg.attr('data-text-id')).css("opacity","1");
			jQuery(current_obj.currentImg.attr('data-text-id')).css("display","block");
			var texts = jQuery(current_obj.currentImg.attr('data-text-id')).children();
			jQuery(current_obj.currentImg.attr('data-text-id')).css('width',allinone_bannerRotator_the.width()+'px');
			jQuery(current_obj.currentImg.attr('data-text-id')).css('left',bannerControls.css('left'));//alert (allinone_bannerRotator_the.width());
			jQuery(current_obj.currentImg.attr('data-text-id')).css('top',bannerControls.css('top'));

			var i=0;
			currentText_arr=Array();
			texts.each(function() {
				currentText_arr[i] = jQuery(this);
	            //alert (currentText_arr[i].attr('data-initial-left'));
	            //currentText_arr[i].css("display","block");
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
        function allinone_bannerRotator_generate_stripes(allinone_bannerRotator_container,options,current_obj) {
			jQuery('.stripe', allinone_bannerRotator_container).remove();
			jQuery('.block', allinone_bannerRotator_container).remove();
            stripe_width = Math.round(allinone_bannerRotator_container.width()/options.numberOfStripes);
			new_stripe_width=stripe_width;
			//alert(allinone_bannerRotator_container.width()+' - '+stripe_width);
        	for(var i = 0; i < options.numberOfStripes; i++){
				if (i == options.numberOfStripes-1) {
					new_stripe_width=allinone_bannerRotator_container.width()-stripe_width*(options.numberOfStripes-1);
					//alert (stripe_width+'  -  '+new_stripe_width);
				}
				allinone_bannerRotator_container.append(
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
        function allinone_bannerRotator_generate_blocks(allinone_bannerRotator_container,options,current_obj) {
			jQuery('.stripe', allinone_bannerRotator_container).remove();
			jQuery('.block', allinone_bannerRotator_container).remove();
			var block_width = Math.round(allinone_bannerRotator_container.width()/options.numberOfColumns);
			var block_height = Math.round(allinone_bannerRotator_container.height()/options.numberOfRows);

            for(var i = 0; i < options.numberOfRows; i++){
            	for(var j = 0; j < options.numberOfColumns; j++){
            		allinone_bannerRotator_container.append(
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


		function animate_block(block,i,j,options,allinone_bannerRotator_container){
            var w = block.width();
            var h = block.height();
            block.css({'width':'0'});
            block.css({'height':'0'});
            if (i==options.numberOfRows-1 && j==options.numberOfColumns-1)
            	setTimeout(function(){
					block.animate({ opacity:'1.0', width:w, height:h }, (options.effectDuration*1000)/3 , '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
				}, (delay_time));
            else
				setTimeout(function(){
					block.animate({ opacity:'1.0', width:w, height:h }, (options.effectDuration*1000)/3 );
				}, (delay_time));
			delay_time += delay_time_blocks_step;
		};


        // navigation
		function allinone_bannerRotator_navigation(direction,options,current_obj,total_images,current_effect,allinone_bannerRotator_container,bottomNavButs,imgs){
			var navigateAllowed=true;
			if ((!options.loop && current_obj.current_img_no+direction>=total_images) || (!options.loop && current_obj.current_img_no+direction<0))
				navigateAllowed=false;

			if (navigateAllowed) {
				//hide previous texts
				//jQuery(current_obj.currentImg.attr('data-text-id')).css('opacity','0');
				jQuery(current_obj.currentImg.attr('data-text-id')).css("display","none");

				//deactivate previous
				jQuery(bottomNavButs[current_obj.current_img_no]).removeClass('bottomNavButtonON');

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
				jQuery(bottomNavButs[current_obj.current_img_no]).addClass('bottomNavButtonON');

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
					//alert ("fade");
					allinone_bannerRotator_generate_stripes(allinone_bannerRotator_container,options,current_obj);
					var first_stripe = jQuery('.stripe:first', allinone_bannerRotator_container);

					if (current_effect == 'fade') {
						first_stripe.css({
		                    'height': '100%',
		                    'width': allinone_bannerRotator_container.width() + 'px'
		                });
						first_stripe.animate({ opacity:'1.0' }, (options.effectDuration*2000), '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
					}

					if (current_effect == 'slideFromLeft') {
						first_stripe.css({
		                    'height': '100%',
		                    'width': '0'
		                });
						first_stripe.animate({ opacity:'1.0', width:allinone_bannerRotator_container.width() + 'px' }, (options.effectDuration*1000), '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
					}

					if (current_effect == 'slideFromRight') {
						first_stripe.css({
		                    'height': '100%',
		                    'width':  '0',
		                    'left': allinone_bannerRotator_container.width()+5 + 'px'
		                });
						first_stripe.animate({ opacity:'1.0', left:'0', 'width':  allinone_bannerRotator_container.width() + 'px' }, (options.effectDuration*1000), '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
					}

					if (current_effect == 'slideFromTop') {
						first_stripe.css({
		                    'height': '0',
		                    'width': allinone_bannerRotator_container.width() + 'px'
		                });
						first_stripe.animate({ opacity:'1.0', height:allinone_bannerRotator_container.height() + 'px' }, (options.effectDuration*1000), '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
					}

					if (current_effect == 'slideFromBottom') {
						first_stripe.css({
		                    'height': '0',
		                    'width': allinone_bannerRotator_container.width() + 'px',
		                    'top': allinone_bannerRotator_container.height() + 'px'
		                });
						first_stripe.animate({ opacity:'1.0', top:0, height:allinone_bannerRotator_container.height() + 'px' }, (options.effectDuration*1000), '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
					}

				}

				if(current_effect.indexOf('Stripes')>=0) {
					allinone_bannerRotator_generate_stripes(allinone_bannerRotator_container,options,current_obj);
					if (current_effect.indexOf('Reverse')>=0){
						var stripes = jQuery('.stripe', allinone_bannerRotator_container).myReverse();
					} else {
						var stripes = jQuery('.stripe', allinone_bannerRotator_container);
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
									stripe.animate({ width:aux_stripe_width+'px', opacity:'1.0' }, (options.effectDuration*800), '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
								}, (delay_time));
							} else {
								setTimeout(function(){
									stripe.animate({ width:aux_stripe_width+'px', opacity:'1.0' }, (options.effectDuration*800) );
								}, (delay_time));
							}
						} else {
								if(i == options.numberOfStripes-1){
									setTimeout(function(){
										stripe.animate({ height:'100%', opacity:'1.0' }, (options.effectDuration*1000), '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
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
					allinone_bannerRotator_generate_blocks(allinone_bannerRotator_container,options,current_obj);
					if (current_effect.indexOf('Reverse')>=0){
						var blocks = jQuery('.block', allinone_bannerRotator_container).myReverse();
					} else if (current_effect=='randomBlocks') {
						var blocks = shuffle(jQuery('.block', allinone_bannerRotator_container));
					} else {
						var blocks = jQuery('.block', allinone_bannerRotator_container);
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
									block.animate({ opacity:'1.0', width:w, height:h }, (options.effectDuration*1000)/3 , '', function(){ allinone_bannerRotator_container.trigger('effectComplete'); });
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
							animate_block(block,0,0,options,allinone_bannerRotator_container);
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
									animate_block(block,i,j,options,allinone_bannerRotator_container);
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
							//alert (limit_j)
							//alert (row_i+'  -  '+col_i+' - '+limit_i+' - '+limit_j)
							while (limit_i<row_i && limit_j<col_i) {
								i=row_i+1; //options.numberOfRows-1;
								j=limit_j;
								while (i>limit_i && j<col_i) {
									i=i-1;
									j=j+1;
									var block = jQuery(blocks_arr[i][j]);
									animate_block(block,i,j,options,allinone_bannerRotator_container);
									//alert (i+'-'+j);
								}
								limit_i++;
								limit_j++;
							}

					} // else randomBlocks
				} // if blocks

			} // if navigateAllowed

		};









	$.fn.allinone_bannerRotator = function(options) {

		var options = $.extend({},$.fn.allinone_bannerRotator.defaults, options);

		return this.each(function() {
			var allinone_bannerRotator_the = jQuery(this);
			allinone_bannerRotator_the.css("display","block");

			//the controllers
			var allinone_bannerRotator_wrap = jQuery('<div></div>').addClass('allinone_bannerRotator').addClass(options.skin);
			var bannerControls = jQuery('<div class="bannerControls">   <div class="leftNav"></div>   <div class="rightNav"></div>      </div>');
			allinone_bannerRotator_the.wrap(allinone_bannerRotator_wrap);
			allinone_bannerRotator_the.after(bannerControls);



			//the elements
			var allinone_bannerRotator_container = allinone_bannerRotator_the.parent('.allinone_bannerRotator');
			var bannerControls = jQuery('.bannerControls', allinone_bannerRotator_container);

			var bottomNavLeft_aux=jQuery('<div class="bottomNavLeft"></div>');
			var bottomNav_aux=jQuery('<div class="bottomNav"></div>');
			var bottomNavRight_aux=jQuery('<div class="bottomNavRight"></div>');

			allinone_bannerRotator_the.after(bottomNavLeft_aux);
			allinone_bannerRotator_the.after(bottomNav_aux);
			allinone_bannerRotator_the.after(bottomNavRight_aux);

			if (!options.showAllControllers)
				bannerControls.css("display","none");


			var allinone_bannerRotator_leftNav = jQuery('.leftNav', allinone_bannerRotator_container);
			var allinone_bannerRotator_rightNav = jQuery('.rightNav', allinone_bannerRotator_container);
			allinone_bannerRotator_leftNav.css("display","none");
			allinone_bannerRotator_rightNav.css("display","none");
			if (options.showNavArrows) {
				if (options.showOnInitNavArrows) {
					allinone_bannerRotator_leftNav.css("display","block");
					allinone_bannerRotator_rightNav.css("display","block");
				}
			}

			var allinone_bannerRotator_bottomNav = jQuery('.bottomNav', allinone_bannerRotator_container);
			var allinone_bannerRotator_bottomNavLeft = jQuery('.bottomNavLeft', allinone_bannerRotator_container);
			var allinone_bannerRotator_bottomNavRight = jQuery('.bottomNavRight', allinone_bannerRotator_container);
			var allinone_bannerRotator_bottomOverThumb;
			allinone_bannerRotator_bottomNav.css("display","block");
			allinone_bannerRotator_bottomNavLeft.css("display","block");
			allinone_bannerRotator_bottomNavRight.css("display","block");
			if (!options.showBottomNav) {
				allinone_bannerRotator_bottomNav.css("display","none");
				allinone_bannerRotator_bottomNavLeft.css("display","none");
				allinone_bannerRotator_bottomNavRight.css("display","none");
			}
			if (!options.showOnInitBottomNav) {
				allinone_bannerRotator_bottomNav.css("left","-5000px");
				allinone_bannerRotator_bottomNavLeft.css("left","-5000px");
				allinone_bannerRotator_bottomNavRight.css("left","-5000px");
			}



			//the vars
			var current_effect=options.defaultEffect;
			var total_images=0;
			var current_obj = {
				current_img_no:0,
				currentImg:0,
				bottomNavClicked:false,
				effectIsRunning:false
			};
			var timeoutID; // the autoplay timeout ID
			var mouseOverBanner=false;


			var i = 0;



			//set banner size
			allinone_bannerRotator_container.width(options.width);
			allinone_bannerRotator_container.height(options.height);

			bannerControls.width('100%');
			bannerControls.height('100%');

			//get images
			var imgs = allinone_bannerRotator_the.children();
			var bottomNavBut;
			var bottomNavWidth=0;
			var bottomNavMarginTop=0;
			imgs.each(function() {
	            current_obj.currentImg = jQuery(this);
	            if(!current_obj.currentImg.is('img')){
	            	current_obj.currentImg = current_obj.currentImg.find('img:first');
	            }

	            if(current_obj.currentImg.is('img')){
	            	current_obj.currentImg.css('display','none');
	            	total_images++;


		            //generate bottomNav
		            bottomNavBut = jQuery('<div class="bottomNavButtonOFF" rel="'+ (total_images-1) +'"></div>');
		            allinone_bannerRotator_bottomNav.append(bottomNavBut);


		            bottomNavWidth+=parseInt(bottomNavBut.css('padding-left').substring(0, bottomNavBut.css('padding-left').length-2))+bottomNavBut.width();
		            bottomNavMarginTop=parseInt((allinone_bannerRotator_bottomNav.height()-parseInt(bottomNavBut.css('height').substring(0, bottomNavBut.css('height').length-2)))/2);
		            //alert (bottomNavMarginTop);
		            bottomNavBut.css('margin-top',bottomNavMarginTop+'px');
	            }
	            //alert (bottomNavWidth)
	        });
			//bottomNavWidth+=parseInt(bottomNavBut.css('padding-left').substring(0, bottomNavBut.css('padding-left').length-2));
			allinone_bannerRotator_bottomNav.width(bottomNavWidth);
			if (options.showOnInitBottomNav) {
				allinone_bannerRotator_bottomNav.css("left",parseInt((allinone_bannerRotator_container.width()-bottomNavWidth)/2)+'px');
				allinone_bannerRotator_bottomNavLeft.css("left",parseInt(allinone_bannerRotator_bottomNav.css('left').substring(0, allinone_bannerRotator_bottomNav.css('left').length-2))-allinone_bannerRotator_bottomNavLeft.width()+'px');
				allinone_bannerRotator_bottomNavRight.css("left",parseInt(allinone_bannerRotator_bottomNav.css('left').substring(0, allinone_bannerRotator_bottomNav.css('left').length-2))+allinone_bannerRotator_bottomNav.width()+parseInt(bottomNavBut.css('padding-left').substring(0, bottomNavBut.css('padding-left').length-2))+'px');
			}


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
	        allinone_bannerRotator_container.css('background','url("'+ current_obj.currentImg.attr('src') +'") no-repeat');



			if (options.enableTouchScreen) {
				var randomNo=Math.floor(Math.random()*100000);

				allinone_bannerRotator_container.wrap('<div id="bannerRotatorParent_'+randomNo+'" style="position:relative;" />');
				jQuery('#bannerRotatorParent_'+randomNo).width(options.width+1);
				jQuery('#bannerRotatorParent_'+randomNo).height(options.height);
				//jQuery('#bannerRotatorParent_'+randomNo).css('overflow','hidden');
				//jQuery('#bannerRotatorParent_'+randomNo).css('border','1px solid #ff0000');


				allinone_bannerRotator_container.css('cursor','url(skins/hand.cur),url(skins/hand.cur),move');
				allinone_bannerRotator_container.css('left','0px');
				allinone_bannerRotator_container.css('position','absolute');

				rightVal=parseInt(allinone_bannerRotator_rightNav.css('right').substring(0, allinone_bannerRotator_rightNav.css('right').length-2));

				//alert(allinone_bannerRotator_container.parent().attr('id'));

				//jQuery("body").css("overflow", "hidden");

				allinone_bannerRotator_container.mousedown(function() {
					rightVal=parseInt(allinone_bannerRotator_rightNav.css('right').substring(0, allinone_bannerRotator_rightNav.css('right').length-2));
					if (rightVal<0 && !arrowClicked) {
						allinone_bannerRotator_rightNav.css('visibility','hidden');
						allinone_bannerRotator_leftNav.css('visibility','hidden');
						allinone_bannerRotator_rightNav.css('right','0');
					}
				});
				allinone_bannerRotator_container.mouseup(function() {
					arrowClicked=false;
					if (rightVal<0) {
						allinone_bannerRotator_rightNav.css('right',rightVal+'px');
						allinone_bannerRotator_rightNav.css('visibility','visible');
						allinone_bannerRotator_leftNav.css('visibility','visible');
					}
				});

				allinone_bannerRotator_container.draggable({
					axis: 'x',
					containment: 'parent',
					//scroll:false,
					//revert:true,
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
							allinone_bannerRotator_navigation(direction,options,current_obj,total_images,current_effect,allinone_bannerRotator_container,bottomNavButs,imgs);
						}
						if (rightVal<0) {
							allinone_bannerRotator_rightNav.css('right',rightVal+'px');
							allinone_bannerRotator_rightNav.css('visibility','visible');
							allinone_bannerRotator_leftNav.css('visibility','visible');
						}
						jQuery(this).css('left','0px');
					}
				});
			}




	        //generate the text for first image
			animate_texts(current_obj,allinone_bannerRotator_the,bannerControls);



	        //Event when Animation finishes
			allinone_bannerRotator_container.bind('effectComplete', function(){
				current_obj.effectIsRunning=false;
				allinone_bannerRotator_container.css('background','url("'+ current_obj.currentImg.attr('src') +'") no-repeat');



				//alert (current_obj.currentImg.attr('data-text-id'));
				animate_texts(current_obj,allinone_bannerRotator_the,bannerControls);

				if (options.autoPlay>0 && total_images>1 && !mouseOverBanner) {
					clearTimeout(timeoutID);
					timeoutID=setTimeout(function(){ allinone_bannerRotator_navigation(1,options,current_obj,total_images,current_effect,allinone_bannerRotator_container,bottomNavButs,imgs)},options.autoPlay*1000);
				}
	        }); //bind







			//pause on hover
			allinone_bannerRotator_container.mouseenter(function() {
				mouseOverBanner=true;
				clearTimeout(timeoutID);
				if (options.autoHideNavArrows && options.showNavArrows) {
					allinone_bannerRotator_leftNav.css("display","block");
					allinone_bannerRotator_rightNav.css("display","block");
				}
				if (options.autoHideBottomNav && options.showBottomNav) {
					allinone_bannerRotator_bottomNav.css("display","block");
					allinone_bannerRotator_bottomNav.css("left",parseInt((allinone_bannerRotator_container.width()-bottomNavWidth)/2)+'px');

					allinone_bannerRotator_bottomNavLeft.css("display","block");
					allinone_bannerRotator_bottomNavLeft.css("left",parseInt(allinone_bannerRotator_bottomNav.css('left').substring(0, allinone_bannerRotator_bottomNav.css('left').length-2))-allinone_bannerRotator_bottomNavLeft.width()+'px');

					allinone_bannerRotator_bottomNavRight.css("display","block");
					allinone_bannerRotator_bottomNavRight.css("left",parseInt(allinone_bannerRotator_bottomNav.css('left').substring(0, allinone_bannerRotator_bottomNav.css('left').length-2))+allinone_bannerRotator_bottomNav.width()+parseInt(bottomNavBut.css('padding-left').substring(0, bottomNavBut.css('padding-left').length-2))+'px');

				}
			});

			allinone_bannerRotator_container.mouseleave(function() {
				mouseOverBanner=false;
				if (options.autoHideNavArrows && options.showNavArrows) {
					allinone_bannerRotator_leftNav.css("display","none");
					allinone_bannerRotator_rightNav.css("display","none");
				}
				if (options.autoHideBottomNav && options.showBottomNav) {
					allinone_bannerRotator_bottomNav.css("display","none");
					allinone_bannerRotator_bottomNavLeft.css("display","none");
					allinone_bannerRotator_bottomNavRight.css("display","none");
				}
				if (options.autoPlay>0 && total_images>1) {
					clearTimeout(timeoutID);
					timeoutID=setTimeout(function(){ allinone_bannerRotator_navigation(1,options,current_obj,total_images,current_effect,allinone_bannerRotator_container,bottomNavButs,imgs)},options.autoPlay*1000);
				}
			});

			/*//a href
			allinone_bannerRotator_container.click(function() {
				alert("a");

			});*/





			//controllers
			allinone_bannerRotator_leftNav.mousedown(function() {
				arrowClicked=true;
				if (!current_obj.effectIsRunning) {
					//mouseOverBanner=false;
					clearTimeout(timeoutID);
					allinone_bannerRotator_navigation(-1,options,current_obj,total_images,current_effect,allinone_bannerRotator_container,bottomNavButs,imgs);
				}
			});
			allinone_bannerRotator_leftNav.mouseup(function() {
				arrowClicked=false;
			});

			allinone_bannerRotator_rightNav.mousedown(function() {
				arrowClicked=true;
				if (!current_obj.effectIsRunning) {
					//mouseOverBanner=false;
					clearTimeout(timeoutID);
					allinone_bannerRotator_navigation(1,options,current_obj,total_images,current_effect,allinone_bannerRotator_container,bottomNavButs,imgs);
				}
			});
			allinone_bannerRotator_rightNav.mouseup(function() {
				arrowClicked=false;
			});





			//bottom nav
			var bottomNavButs=jQuery(".bottomNavButtonOFF", allinone_bannerRotator_container);
			bottomNavButs.mousedown(function() {
				arrowClicked=true;
				if (!current_obj.effectIsRunning) {
					var currentBut=jQuery(this);
					var i=currentBut.attr('rel');
					//deactivate previous
					jQuery(bottomNavButs[current_obj.current_img_no]).removeClass('bottomNavButtonON');

					current_obj.bottomNavClicked=true;
					current_obj.current_img_no=i-1;
					allinone_bannerRotator_navigation(1,options,current_obj,total_images,current_effect,allinone_bannerRotator_container,bottomNavButs,imgs);
					//alert (i+'  --  '+current_obj.current_img_no+'  --  '+total_images);
				}
			});
			bottomNavButs.mouseup(function() {
				arrowClicked=false;
			});


			bottomNavButs.mouseenter(function() {
				var currentBut=jQuery(this);
				var i=currentBut.attr('rel');


				if (options.showPreviewThumbs) {
					allinone_bannerRotator_bottomOverThumb = jQuery('<div class="bottomOverThumb"></div>');
					currentBut.append(allinone_bannerRotator_bottomOverThumb);
					var image_name = jQuery(imgs[i]);
          if (!image_name.is('img')) {
              image_name = image_name.find('img:first');
          }
          var thumb_src = image_name.attr('data-thumb-src');
					var image_name = image_name.attr('src').substr(image_name.attr('src').lastIndexOf('/'),image_name.attr('src').length);
					allinone_bannerRotator_bottomOverThumb.html('<img src="'+ thumb_src + '">');
				}

				currentBut.addClass('bottomNavButtonON');
			});

			bottomNavButs.mouseleave(function() {
				var currentBut=jQuery(this);
				var i=currentBut.attr('rel');

				if (options.showPreviewThumbs) {
					allinone_bannerRotator_bottomOverThumb.remove();
				}

				if (current_obj.current_img_no!=i)
					currentBut.removeClass('bottomNavButtonON');
			});












			//first start autoplay
			jQuery(bottomNavButs[current_obj.current_img_no]).addClass('bottomNavButtonON');
			if (options.autoPlay>0 && total_images>1) {
				timeoutID=setTimeout(function(){ allinone_bannerRotator_navigation(1,options,current_obj,total_images,current_effect,allinone_bannerRotator_container,bottomNavButs,imgs)},options.autoPlay*1000);
			};


		});
	};

	//reverse effect
	$.fn.myReverse = [].reverse;

	//
	// plugin skins
	//
	$.fn.allinone_bannerRotator.defaults = {
			skin: 'classic',
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
			showBottomNav:true,
			showOnInitBottomNav:true, // o2
			autoHideBottomNav:true, // o2
			showPreviewThumbs:true,
			enableTouchScreen:true
	};

})(jQuery);