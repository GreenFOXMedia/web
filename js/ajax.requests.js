$(document).ready(function(){
	$("#slideshow_images").slidesjs({
		width: 1000,
        height: 270,
		effect: {
			slide: {
		        // Slide effect settings.
		        speed: 1000
		          // [number] Speed in milliseconds of the slide animation.
		    },
		    fade: {
				speed: 1200,
		          // [number] Speed in milliseconds of the fade animation.
		        crossfade: true
		          // [boolean] Cross-fade the transition.
		    }
		},
		pagination: {
	      	active: false,
	        // [boolean] Create pagination items.
	        // You cannot use your own pagination. Sorry.
	     	effect: "fade"
	        // [string] Can be either "slide" or "fade".
	    },
	    navigation: {
	      	active: true,
	        // [boolean] Create pagination items.
	        // You cannot use your own pagination. Sorry.
	     	effect: "fade"
	        // [string] Can be either "slide" or "fade".
	    },

	    play: {
			active: true,
			// [boolean] Generate the play and stop buttons.
			// You cannot use your own buttons. Sorry.
			effect: "fade",
			// [string] Can be either "slide" or "fade".
			interval: 5000,
			// [number] Time spent on each slide in milliseconds.
			auto: true,
			// [boolean] Start playing the slideshow on load.
			swap: true,
			// [boolean] show/hide stop and play buttons
			pauseOnHover: true,
			// [boolean] pause a playing slideshow on hover
			restartDelay: 2500
// [number] restart delay on inactive slideshow
    	}
	})
	
	jQuery.fn.extend(
	{
	  scrollTo : function(speed, easing)
	  {
	    return this.each(function()
	    {
	      var targetOffset = $(this).offset().top;
	      $('html,body').animate({scrollTop: targetOffset}, speed, easing);
	    });
	  }
	});

	$(".nav_container ul li").click(function(){
		activeClass = "active";

		$("."+activeClass).removeClass(activeClass);
		$(this).addClass(activeClass);
		target = $(this).attr("id");
		$("."+target).scrollTo(1000);
	});

	// Top Scrolling
	$("div[data-id='home_target']").each(function(){
		$(this).click(function(){
			activeClass = "active";

			$("."+activeClass).removeClass(activeClass);

			$(activeClass).removeClass(activeClass);

			$("#home").addClass(activeClass);
			target = $(this).attr("data-id");
			$("."+target).scrollTo(1000);
		});	
	})

	$(".show_maps").click(function(){
		$(".overlay").show();
		$(".block_maps").fadeIn(500);
	})

	$(".show_contact").click(function(){
		$(".overlay").show();
		$(".block_contact_form").fadeIn(500);
	})

	$(window).keydown(function(event){
		console.log(event.keyCode)
		if(event.keyCode == "27")
		{
			$(".block_maps").fadeOut(500);
			$(".block_contact_form").fadeOut(500);
			$(".image_lightbox").fadeOut(500);
			$(".overlay").fadeOut(200);
		}

	})

	$(".overlay").click(function(){
		$(".block_maps").fadeOut(500);
		$(".block_contact_form").fadeOut(500);
		$(".image_lightbox").fadeOut(500);
		$(this).fadeOut(200);
	});

	$(".image img").click(function(){
		$(".image_lightbox img").attr("src", $(this).attr("src"));
		$(".image_lightbox img").attr("width", "674");
		$(".image_lightbox img").attr("height", "500");
		$(".image_lightbox .image-title").html($(this).attr("data-title"))
		$(".image_lightbox .image-description").html($(this).attr("data-description"))
		setTimeout(function(){
			$(".overlay").show();
			$(".image_lightbox").fadeIn(500);
		});
	});

	
	
});