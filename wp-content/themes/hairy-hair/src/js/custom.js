/*===============================  
       Sticky 
  ================================*/
jQuery(window).on('scroll', function (event) {
    var scroll = jQuery(window).scrollTop();
    if (scroll < 30) {
        jQuery(".navbar").removeClass("sticky");
    } else {
        jQuery(".navbar").addClass("sticky");
    }
});

//COUNTER- Home page

let count = document.querySelectorAll("#counter h3");
let arr = Array.from(count);

arr.map(function(item){
    let startnumber = 0;
    let target = parseInt(item.dataset.number);
    let symbol = item.querySelector('span').innerHTML;

    function counterup() {
        startnumber++;
        item.innerHTML = `${startnumber}<span>${symbol}</span>`;

        if(startnumber == target){
            clearInterval(stop);
        }
    }

    let stop;
    setTimeout(function() {
        stop = setInterval(counterup, 10);
    }, 2000);
});



//PRODUCT SLIDER
$(document).ready(function () {

    $('.product-slider-wrapper').slick({
        dots: true,
        arrows: true,
        infinite: true,
        speed: 500,
        slidesToShow: 4,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 575,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.treatment-slider').slick({
        dots: false,
        arrows: true,
        infinite: true,
        speed: 500,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    //Loved by people 
    $('.loved-by-slider').slick({
        dots: false,
        arrows: true,
        infinite: true,
        autoHeight: true,
        adaptiveHeight: true,
        speed: 500,
        slidesToShow: 3,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 620,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    //The Ultimate Hair Care Resource Hub
    $('.ultimate-hair-care').slick({
        dots: false,
        arrows: false,
        infinite: true,
        speed: 500,
        slidesToShow: 3,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
    
    // PRP - Why choose us slider
    $('.prp-slider').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        dots: true,
        infinite: true,
        arrows: false,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    // PRP - Why choose us slider
    $('.resule-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: false,
        dots: false,
        infinite: true,
        arrows: true
    });

    // Blogs- Category slider
    $('.all-category').slick({
        slidesToShow: 6,
        slidesToScroll: 5,
        autoplay: false,
        dots: false,
        infinite: true,
        arrows: true,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 375,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2
                }
            }
        ]
    });

    // Blogs- Popular News & Articles
    $('.all-popular-article').slick({
        slidesToShow: 4,
        slidesToScroll: 4,
        autoplay: false,
        dots: false,
        infinite: true,
        arrows: true,
        responsive: [
            {
                breakpoint: 1560,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 575,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    centerMode: false
                }
            },
            {
                breakpoint: 540,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    centerMode: false
                }
            }
        ]
    });

    // Blogs- Popular News & Articles
    $('.ingredients').slick({
        slidesToShow: 2,
        slidesToScroll: 2,
        autoplay: false,
        dots: false,
        infinite: true,
        arrows: true,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
    
      // Run on load and on resize
    $(document).ready(function () {
        setEqualHeight();
        setTherapyHeight();
        setHaircareHeight();
    });
      
    $(window).on('resize', function () {
        setEqualHeight();
        setTherapyHeight();
        setHaircareHeight();
    });

    // 3 STEPS home page animation
    const $stepsSection = $('#stepsFlow');
    const $arrow = $('#flowArrow');
    const $steps = $('.step');
    const $timeline = $('.timeline');

    if (window.matchMedia("(min-width: 1280px)").matches) {
        let animated = false;

        function isInViewport($el) {
            const elementTop = $el.offset().top;
            const elementBottom = elementTop + $el.outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            return elementBottom > viewportTop + ($el.outerHeight() / 2) &&
                elementTop < viewportBottom - ($el.outerHeight() / 2);
        }

        $(window).on('scroll', function () {
            if (!animated && isInViewport($stepsSection)) {
                $arrow.css({
                    opacity: 1,
                    left: '98%'
                });

                $timeline.css({
                    width: '98%'
                });

                $steps.each(function (index) {
                    const $step = $(this);
                    setTimeout(() => {
                        $step.addClass('show');
                    }, index * 2000);
                });

                animated = true;
            }
        });
    } else {
        $steps.addClass('show');
    }

    // FAQ page tablinks

    






    
    $(document).on('click', '.tabs-container li', function(){
        $('.tabs-container li').removeClass('active');
        $('.tabs-container ul').toggleClass('expanded');
        $(this).addClass('active');
        var tab_id = $(this).attr('data-tab');
        $('.top-category .tab-content').removeClass('current');
        $(this).addClass('current');
        $('#'+tab_id).addClass('current');
    });      
      
});



function setEqualHeight() {
    let maxHeight = 0;
    $('.testi-slide').each(function () {
      $(this).css('height', 'auto'); // reset first
      const thisHeight = $(this).outerHeight();
      if (thisHeight > maxHeight) {
        maxHeight = thisHeight;
      }
    });
  
    $('.testi-slide').height(maxHeight);
}

function setTherapyHeight() {
    let maxHeight = 0;
    $('.prp-slide ').each(function () {
      $(this).css('height', 'auto'); // reset first
      const thisHeight = $(this).outerHeight();
      if (thisHeight > maxHeight) {
        maxHeight = thisHeight;
      }
    });
  
    $('.prp-slide ').height(maxHeight);
}


function setHaircareHeight() {
    let maxHeight = 0;
    $('.product-slide ').each(function () {
      $(this).css('height', 'auto'); // reset first
      const thisHeight = $(this).outerHeight();
      if (thisHeight > maxHeight) {
        maxHeight = thisHeight;
      }
    });
  
    $('.product-slide ').height(maxHeight);
}

