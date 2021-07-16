
//slider
$(document).ready(function(e){
	$('.product-slider').owlCarousel({
    loop:false,
    margin:30,
	nav:true,
	autoplay:true,
	autoplayTimeout:3000,
	navText: [
		'<i class="fa fa-angle-left" aria-hidden="true"></i>',
		'<i class="fa fa-angle-right" aria-hidden="true"></i>'
	],
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:4
        }
    }
	});
});

// tabs
$(document).ready(function(){ 
    $('.tab-a').click(function(){  
    $(".tab").removeClass('tab-active');
    $(".tab[data-id='"+$(this).attr('data-id')+"']").addClass("tab-active");
    $(".tab-a").removeClass('active-a');
    $(this).parent().find(".tab-a").addClass('active-a');
    });
});