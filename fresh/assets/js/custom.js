
    
    
    
    // Preloader 
    jQuery(window).on('load', function() {
        // jQuery("#status").fadeOut();
        jQuery(".page-loader").delay(3500).fadeOut("slow");
    });
    
    // $(window).on('load',function(){
    //     setTimeout(function(){ // allowing 3 secs to fade out loader
    //     $('.page-loader').fadeOut('slow');
    //     },3500);
    // });
    
    // ===== Scroll to Top ==== //
    $(window).scroll(function() {
        if ($(this).scrollTop() >= 100) {
            $('#return-to-top').fadeIn(200);
        } else {
            $('#return-to-top').fadeOut(200);
        }
    });
    $('#return-to-top').click(function() {
        $('body,html').animate({
            scrollTop: 0
        }, 500);
    });	


    // index1 profile

    $(document).ready(function(){
      $(".login-btn").click(function(){
        $(".user-text").slideToggle();
      });
      $('body').on('click', function (e) {
            if (!$('.login-btn').is(e.target)
                && $('.login-btn').has(e.target).length === 0
                && $('.open').has(e.target).length === 0
            ) {
                $('.user-text').slideUp();
            }
        });
    });

    // post job


    $(document).ready(function(){
      $(".post-drop").click(function(){
        $(".post-page-wrapper").slideToggle();
      });
      $('body').on('click', function (e) {
            if (!$('.post-drop').is(e.target)
                && $('.post-drop').has(e.target).length === 0
                && $('.open').has(e.target).length === 0
            ) {
                $('.post-page-wrapper').slideUp();
            }
        });
    });

    //  wow

new WOW().init();
  

// index1 memnu

    $(document).ready(function(){
      $(".menu-click1").click(function(){
        $(".menu-open1").slideToggle();
      });
        $('body').on('click', function (e) {
            if (!$('.menu-click1').is(e.target)
                && $('.menu-click1').has(e.target).length === 0
                && $('.open').has(e.target).length === 0
            ) {
                $('.menu-open1').slideUp();
            }
        });
    });

    $('.dropdown').on('show.bs.dropdown', function(e){
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
        });

        $('.dropdown').on('hide.bs.dropdown', function(e){
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp(300);
        });
    // $(document).ready(function(){
    //   $(".menu-click1").click(function(){
    //     $(".menu-open1").slideToggle();
    //   });
    //     $('body').on('click', function (e) {
    //         if (!$('.menu-click1').is(e.target)
    //             && $('.menu-click1').has(e.target).length === 0
    //             && $('.open').has(e.target).length === 0
    //         ) {
    //             $('.menu-open1').slideUp();
    //         }
    //     });
    // });
    $(function () {
        //adding a new class on button element
       
    
        
        //removing a existing class from button element
        $('.menu-click1 a').on('click',function () {
            $('.menu-click1').toggleClass('clicked');
        });
    
        
      
    });

// 

$('.blog-slider .owl-carousel').owlCarousel({
    loop:true,
    margin:20,
    dots: true,
    nav:false,
    
    autoplay: false,
    // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
    smartSpeed: 1200,
    responsive:{
       0:{
            items:1
        },
        350:{
            items:1
        },
        600:{
            items:1
        },
        767:{
            items:2
        },
        1200:{
            items:2
        },
        1300:{
            items:2
        }
    }

})

    // 

    $('.service-slider-wrapper .owl-carousel').owlCarousel({
        loop:true,
        margin:20,
        dots: true,
        nav:false,
        
        autoplay: false,
        // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
        smartSpeed: 1200,
        responsive:{
           0:{
                items:1
            },
            350:{
                items:1
            },
            600:{
                items:2
            },
            767:{
                items:2
            },
            1200:{
                items:3
            },
            1300:{
                items:3
            }
        }

    })

    // 


    $('.upcoming-slider-wrapper .owl-carousel').owlCarousel({
        loop:true,
        margin: 12,
        dots: false,
        nav:true,
        center: true,
        autoplay: false,
        navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
        smartSpeed: 1200,
        responsive:{
           0:{
                items:1
            },
            350:{
                items:1
            },
            600:{
                items:2
            },
            767:{
                items:2
            },
            1200:{
                items:3
            },
            1300:{
                items:3
            }
        }

    })

        // testimonials

        $('.testimonial-slider .owl-carousel').owlCarousel({
            loop:true,
            margin:20,
            dots: true,
            nav:false,
            center: true,
            autoplay: false,
            // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
            smartSpeed: 1200,
            responsive:{
               0:{
                    items:1
                },
                350:{
                    items:1
                },
                600:{
                    items:1
                },
                767:{
                    items:1
                },
                1200:{
                    items:1
                }
              
            }
    
        })


        $('.blog-single-slider .owl-carousel').owlCarousel({
            loop:true,
            margin:20,
            dots: true,
            nav:false,
            autoplay: true,
            // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
            smartSpeed: 1200,
            responsive:{
               0:{
                    items:1
                },
                350:{
                    items:1
                },
                600:{
                    items:1
                },
                767:{
                    items:1
                },
                1200:{
                    items:1
                },
                1300:{
                    items:1
                }
            }
    
        })


$('.home-slider .owl-carousel').owlCarousel({
        loop:true,
        margin:20,
        dots: true,
        nav:true,
        autoplay: true,
        // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
        smartSpeed: 1200,
        responsive:{
           0:{
                items:1
            },
            350:{
                items:1
            },
            600:{
                items:1
            },
            767:{
                items:1
            },
            1200:{
                items:1
            },
            1300:{
                items:1
            }
        }

    })
    // $( ".home-slider .owl-prev").html('<i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Previous</span>');
    // $( ".home-slider .owl-next").html('<span>Next</span>  &nbsp; <i class="fa fa-chevron-right" aria-hidden="true"></i>');

        // home-sec-slider

    $('.home-sec-slider .owl-carousel').owlCarousel({
        loop:true,
        margin:20,
        dots: false,
        nav:true,
        autoplay: true,
        // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
        smartSpeed: 1200,
        responsive:{
           0:{
                items:1
            },
            350:{
                items:1
            },
            600:{
                items:1
            },
            767:{
                items:1
            },
            1200:{
                items:1
            },
            1300:{
                items:1
            }
        }

    })
    $( ".home-sec-slider .owl-prev").html('<i class="fa fa-chevron-left" aria-hidden="true"></i>');
    $( ".home-sec-slider .owl-next").html('<i class="fa fa-chevron-right" aria-hidden="true"></i>');


      // 

      $('.home-sec-up-slider-wrapper .owl-carousel').owlCarousel({
        loop:true,
        margin:20,
        dots: false,
        nav:true,
        autoplay: true,
        // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
        smartSpeed: 1200,
        responsive:{
           0:{
                items:1
            },
            350:{
                items:1
            },
            600:{
                items:1
            },
            767:{
                items:1
            },
            1200:{
                items:1
            },
            1300:{
                items:1
            }
        }

    })
    // $( ".home-sec-up-slider-wrapper .owl-prev").html('<i class="fa fa-chevron-left" aria-hidden="true"></i>');
    // $( ".home-sec-up-slider-wrapper .owl-next").html('<i class="fa fa-chevron-right" aria-hidden="true"></i>');


    // 

    // home-sec-charity

    $('.charity-sldier-wrapper .owl-carousel').owlCarousel({
        loop:true,
        margin:0,
        dots: false,
        nav:true,
        autoplay: true,
        // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
        smartSpeed: 1200,
        responsive:{
           0:{
                items:1
            },
            350:{
                items:1
            },
            600:{
                items:1
            },
            767:{
                items:1
            },
            1200:{
                items:1
            },
            1300:{
                items:1
            }
        }

    })
    $( ".charity-sldier-wrapper .owl-prev").html('<i class="fa fa-chevron-left" aria-hidden="true"></i>');
    $( ".charity-sldier-wrapper .owl-next").html('<i class="fa fa-chevron-right" aria-hidden="true"></i>');


     // home-sec-testi

     $('.test-slider-wrapper .owl-carousel').owlCarousel({
        loop:true,
        margin:20,
        dots: false,
        nav:true,
        autoplay: true,
        // navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
        smartSpeed: 1200,
        responsive:{
           0:{
                items:1
            },
            350:{
                items:1
            },
            600:{
                items:1
            },
            767:{
                items:1
            },
            1200:{
                items:1
            },
            1300:{
                items:1
            }
        }

    })
    $( ".test-slider-wrapper .owl-prev").html('<i class="fa fa-chevron-left" aria-hidden="true"></i>');
    $( ".test-slider-wrapper .owl-next").html('<i class="fa fa-chevron-right" aria-hidden="true"></i>');


// index1 toggle


$(document).ready(function () {
    $(".sidebar-toggle , .sidebar-close").click(function () {
        $("#right-sidebar").toggleClass("open")
    });
});




		(function ($) {
            $(document).ready(function () {

                $('#cssmenu li.active').addClass('open').children('ul').show();
                $('#cssmenu li.has-sub>a').on('click', function () {
                    $(this).removeAttr('href');
                    var element = $(this).parent('li');
                    if (element.hasClass('open')) {
                        element.removeClass('open');
                        element.find('li').removeClass('open');
                        element.find('ul').slideUp(200);
                    }
                    else {
                        element.addClass('open');
                        element.children('ul').slideDown(200);
                        element.siblings('li').children('ul').slideUp(200);
                        element.siblings('li').removeClass('open');
                        element.siblings('li').find('li').removeClass('open');
                        element.siblings('li').find('ul').slideUp(200);
                    }
                });

            });
        })(jQuery);

        // menu fixed
        $(window).scroll(function () {
            var window_top = $(window).scrollTop() + 1;
            if (window_top > 100) {
            $('.menu-items-wrapper').addClass('menu-fixed animated fadeInDown');
            } else {
            $('.menu-items-wrapper').removeClass('menu-fixed animated fadeInDown');
            }
        });
        
        // toggle cross btn js
        $(".toggle-main-wrapper , #toggle_close").on("click", function () {
            $("#sidebar").toggleClass("open")
        });


        // vedio popupe


    $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: true,
        fixedContentPos: false
    });
    $('.image-popup-vertical-fit').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        }
    });








// share fixed button js

function actionToggleOne() {
    let action = document.querySelector('.contact-action');
    action.classList.toggle('open1');
}
function actionToggleTwo() {
    let action = document.querySelector('.action-1');
    action.classList.toggle('open2');
}
function actionToggleThree() {
    let action = document.querySelector('.action-2');
    action.classList.toggle('open3');
}
function actionToggleFour() {
    let action = document.querySelector('.action-3');
    action.classList.toggle('open4');
}


$(document).ready(function() {
    $(".click-toggle").on('click', function() {
        $(".click-toggle").toggleClass("main");
    });
});






// search 

$('#search_button').on("click", function(e) {
    $('#search_open').slideToggle();
    e.stopPropagation(); 
   });
   
   $(document).on("click", function(e){
    if(!(e.target.closest('#search_open'))){  
       $("#search_open").slideUp();        
    }
   });

//    mobile search

$('#search_button1').on("click", function(e) {
    $('#search_open1').slideToggle();
    e.stopPropagation(); 
   });
   
   $(document).on("click", function(e){
    if(!(e.target.closest('#search_open1'))){  
       $("#search_open1").slideUp();        
    }
   });



//  gallery

$('.portfolio_img_text').magnificPopup({
    delegate: '.img-link',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0,1]
    },
    image: {
      tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
      titleSrc: function(item) {
        return item.el.attr('title') + '<small></small>';
      }
    }
  });
      $('.portfolio_img_icon').magnificPopup({
    delegate: '.img-link',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0,1]
    },
    image: {
      tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
      titleSrc: function(item) {
        return item.el.attr('title') + '<small></small>';
      }
    }
  });
