$(document).ready(function () {
	DS.momentum.init();
});

var DS = window.DS || {};

DS.momentum = {
	init : function(){
		if($('html').hasClass('splash')) {
			$('#sharebar').hide();
		} else {
			DS.momentum.Pager.init();
			$('#sharebar').show();
			$('.main_nav').children('li').bind('click', function(e) {
				setTimeout(DS.momentum.set_header,1);
				$('.main_nav').children('li').removeClass('active');
				$(this).addClass('active');
				setTimeout(function(){
					DS.momentum.scrollable = true;
				},100);
			});
	    $('#share-button').mouseover(function(e){
	      $('#share-button').css('height','190px');
	      $('#social-btns').css('zIndex', '1000');
	      $('#social-btns').show();
	      $('#social-btns').css('opacity', '1');
	      e.stopPropagation();
	    });
	    $('#share-button').mouseout(function(e){
	      $('#share-button').css('height','23px');
	      $('#social-btns').css('zIndex', '0');
	      $('#social-btns').show();
	      $('#social-btns').css('opacity', '0');
	    });

	    $('.l18nav li a').bind('click', function(e){
				$('.l18nav li').removeClass('active');
				$('.l18nav li').addClass('inactive');
				$(this).parent('li').removeClass('inactive');
				$(this).parent('li').addClass('active');
			});

	    DS.momentum.reset_all();
	    $(window).resize(function(e){
	      DS.momentum.reset_all();
	    });

	    DS.momentum.set_header();
	    $(window).scroll(function(e) {
	    	DS.momentum.set_header();
	    });

	    $.each([
				'zach-nelson-modal',
				'keith-krach-modal',
				'tom-gonser-modal',
				'john-hinshaw-modal',
				'roger-erickson-modal'
			], DS.momentum.speakerModal); 

			DS.momentum.videoModal('#video1'); 

			DS.momentum.feeds.init();
    }
	},
	set_header: function(){
  	var top = $(document).scrollTop(),
  			header = $('#main_header'),
  			htop = header.offset().top,
  			sharebar = $('#sharebar');

  	sharebar.css('top',((140 + top) + "px"));
  	
    if(top >= 100) {
    	header.css('top',$(document).scrollTop()+"px");  
    	sharebar.css('top',((150 + top) + "px"));   
    } else {
    	header.css('top',0);   
    }
    
    var eventbrite_insert_url  = typeof(eventbrite_url) === 'undefined' ? '' : eventbrite_url;
  	$('.anchor').each(function(i,v){ 
    	var anchor = $(this).offset().top,
    			navitem = $(this).next().attr('id'),
    			reg_pre = '<div id="floating-registerbtn" class="registerbtn" ',
    			reg_style = 'style="bottom:-25px;z-index:1;height:90px"';
    			reg_post = '><a class="eventbrite-register-href" href="http://momentum2014.eventbrite.com/' + eventbrite_insert_url + '"><span>Register</span></a></div>';
    	if( htop >= anchor-200 && htop <= anchor + 200  && $('html').hasClass('sf')) {
    		$('.main_nav').children('li').removeClass('active');
				$('.'+navitem).addClass('active');
				$('#floating-registerbtn').remove();
				if(navitem=='register') {
					reg_style = 'style="bottom:-80px;z-index:1;height:90px"';
				}
				$('#'+navitem).append(reg_pre + reg_style + reg_post);

    		DS.momentum.currentSection = navitem;
    	}
    });
  },
  reset_all: function() {
    $('#main_header').css(('top',$(document).scrollTop()) + "px"); 
    $('#sharebar').css('top',( (140 + $(document).scrollTop()) + "px"));
  },
	speakerModal:  function(i,v) {
    $('.'+v).bind('click', function(e) {
      e.preventDefault();
      $('#'+v).modal({
      	autoResize: true,
      	focus: false,
      	modal: true,
      	onOpen: function(d) {
          d.overlay.fadeIn();
          d.container.fadeIn();
          d.data.fadeIn();
        }
      });
    });
  },
  videoModal: function(vidid) {
	  $(vidid).bind('click', function(e) {
	    e.preventDefault();
	    $.modal(
	    	'<iframe width="560" height="315" src="//www.youtube.com/embed/6zPaHPMfNBk" frameborder="0" allowfullscreen></iframe>',
	    	{
		    	autoResize: true,
		    	focus: true,
		    	modal: true,
		    	onOpen: function(d) {
		        d.overlay.fadeIn();
		        d.container.fadeIn();
		        d.data.fadeIn();
		      },
		      containerCss: {
		      	maxHeight: '415px',
		      	width: '660px',
		      	backgroundColor: '#fff',
		      	textAlign: 'center',
		      	padding: '40px 40px 0px 40px'
		      }
	    });
	  });
	}
};

DS.momentum.feeds = (function() {
	var feedurl = 'http://docusign.com/blog/rss',

	parseRSS = function (url, callback){
		$.ajax({
	        url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=1000&callback=?&q=' + encodeURIComponent(url),
	        dataType: 'json',
	        error: function(m){
	            $('#rssfeed').empty();
	        },
					success: function(data) {
						callback(data.responseData.feed);
				}
			});
	},

	callback = function(feed){
		var keywords = ['Momentum'],
				tempdate, postdate, html;
		$('#rssfeed').empty();
		$.each(feed.entries, function(i,v) {
			if(v.author !== "Anonymous" && v.link && v.publishedDate && v.title) {
				$.each(keywords, function(i,keyword) {
					if( v.title.indexOf(keyword) != -1 || v.contentSnippet.indexOf(keyword) !== -1) {
						tempdate = ((v.publishedDate.split(', ')[1]).split(' -')[0]).split(' ');
						postdate = tempdate[1]+' '+tempdate[0]+', '+tempdate[2];
						html = "<div class='entry'>";
						html += "<h7 class='date'>Posted by " + v.author  + '<span>//</span>' + postdate + "</h7>";
						html += "<p class='snippet'>"+v.contentSnippet +"</p>";
						html += "<p><a href='" + v.link + "' target='_blank'>"+v.title +"</a></p></div>";

						$('#rssfeed').append($(html));  
					}
				});
			}
		});
	},

	styleTwitterWidget = function() {
		$('#twitter-widget-0').contents().find('head').append(
			"<style>border:1px solid red; ::-webkit-scrollbar-track{	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.2);	border-radius: 7px;	background-color: #F5F5F5;} ::-webkit-scrollbar {	width: 7px;	background-color: #F5F5F5;} ::-webkit-scrollbar-thumb {	border-radius: 7px;	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.2);	background-color: #d0d0d0;}</style>"
		);
	};

	return {
		init: function() {
			if($('#twitter-widget-0').length === 0) {
				window.DS.momentum.feeds.waitForTwitter = window.setInterval(function(){
					if($('#twitter-widget-0').length !== 0) {
						DS.momentum.feeds.loaded();
						window.clearInterval(window.DS.momentum.feeds.waitForTwitter);		
					}
				},500)
			} else {
				styleTwitterWidget();
			}
		},
		loaded: function() {
			styleTwitterWidget();
			parseRSS(feedurl, callback);
		}
	}
}());

DS.momentum.Pager = (function() {
	var options,

	page = function(e) {
		var self = $(this),
				delta = $(this).hasClass('right') ? -1 : 1,
				dotnav = $(this).hasClass('navdot') ? $(this).data('page') : false,
				pager = $(this).hasClass('navdot') ? 
					$(this).parent().parent() : $(this).parent('.pager'),
				navdots = pager.children('.navdots'),
				pages = pager.children('.wrap').children('.pages'),
				pagelist = pages.children('.page'),
				paging = pager.children('.paging');

		if(pager.hasClass('hero')) options.wrap = true;
		else options.wrap = false

		if(options.wrap) {
			pager = $(this).hasClass('navdot') ? $(this).parent().parent() : $(this).parent('.pager');
			pages = pager.children('.pages'),
			pagelist = pages.children('.page'),
			paging = pager.children('.paging');
		}
		e.preventDefault();
		paging.off('click');
		pagelist.each(function(i,j) {
			if(dotnav) {
				if(i == dotnav) {
					pager.children('.navdots').children('.navdot').removeClass('active');
					self.addClass('active');
					delta = (i)*100;
					setnav($(pagelist[i]), pager, dotnav);
		
					return false;
				}
			} else if($(this).hasClass('active')) { 
				if(delta == 1 && i > 0 ) {
					$(this).prev().addClass('active');
					$(this).removeClass('active');
					setnav($(this).prev(), pager, i-1);
				} else if(delta == -1 && i < pagelist.length - 1) {
					$(this).next().addClass('active');
					$(this).removeClass('active');
					setnav($(this).next(), pager, i+1);
				} else if(i == 0 && delta == 1) {
					delta = 0;
					setnav(null, pager, 0);
				} else if(i == pagelist.length - 1 && delta == -1) {
					i = pagelist.length-2;
					etnav(pagelist[i], pager, i);
				}
				delta = Math.abs(((i - delta)*delta)*100);
				
				return false;
			}
		});
		pages
			.stop()
			.animate(
				{ 
					left: -delta + '%'
				},
				{
					duration: 1000,
					complete: function() { 
						if(options.wrap) {
							DS.momentum.Pager.page($(this).parent('.pager').children('.paging')); 
						} else {
							DS.momentum.Pager.page($(this).parent('.wrap').parent('.pager').children('.paging')); 
						}	
					}
				}
			);
	},
	setnav = function(page, pager, index) {
		var state,
				navdots = pager.children('.navdots');

		if(index !== undefined) {
			navdots.children('.navdot').removeClass('active');
			navdots.children('#navdot_'+index).addClass('active');
		} 
		if(page === null) state = -1
		else if(page.is(':first-child') || page.hasClass('first')) state = -1;
		else if(page.is(':last-child') || page.hasClass('last')) state = 1;
		else state = 0;

		if(state==-1) {
			pager.children('.left').hide();
			pager.children('.right').show();
		} else if(state == 1) {
			pager.children('.right').hide();
			pager.children('.left').show();
		} else if(state == 0) {
			pager.children('.paging').show();
		} 
	};

	return {
		init: function(_options) {
			options = _options || { wrap: false };
			$('.pager').each(function(k,v){
				var pagerdiv = $(v);
				var boolhero = $(this).hasClass('hero');
				if(pagerdiv.hasClass('hero')) {
					options.wrap = true;
				} else {
					options.wrap = false;
				}

				if(options.wrap) {
					pagelist = pagerdiv.children('.pages').children('.page');
					pagerdiv.children('.pages').css({width: pagelist.length*100 + '%'})
				} else {
					pagelist = pagerdiv.children('.wrap').children('.pages').children('.page')
					pagerdiv.children('.wrap').children('.pages').css({width: pagelist.length*100 + '%'});
				}
				pagelist.each(function(i,j){
					var _page = $(j);
					if(boolhero){
						_page.css({width: ((1 / pagelist.length )*100) + 0.1 + '%'});
					} else {
						_page.css({width: (1 / pagelist.length )*100 + '%'});
					}
					
					var navdot = $('<a/>',{
						class: 'navdot',
						id: 'navdot_'+i,
						href: '#'
					});
					if(_page.is(':first-child')) {
						_page.addClass('active first');
						navdot.addClass('active');
					} else if(_page.is(':last-child')) {
						_page.addClass('last');
					}
					navdot.data("page", i);
					navdot.appendTo(pagerdiv.children('.navdots'));
				});
				pagerdiv.children('.paging').on('click', page);
				pagerdiv.children('.navdots').children('.navdot').each(function(i,j){
					$(j).on('click',page);
				});
				setnav(null, pagerdiv);
			});
		},
		page: function(nl) {
			if(nl) {
				$(nl).on('click', page);
			}
		}
	};
})();

$('#qsubmitbtn').bind('click', function(e) {
  e.preventDefault();
  var targetUrl = 'http://s566810826.t.eloqua.com/e/f2';
  $('#questions-form').validationEngine({promptPosition: 'topLeft', scroll: false});
  if ($('#questions-form').validationEngine('validate')) {
    var data = $('#questions-form').serialize();
    var posting = $.post(targetUrl, data,function(data){},"jsonp");
    posting.always(function(data){
        $('#evform').addClass('submit');
    });
  }
});


