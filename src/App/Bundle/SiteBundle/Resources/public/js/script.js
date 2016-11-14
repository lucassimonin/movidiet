(function($) {
  'use strict'; // Start of use strict

  // jQuery for page scrolling feature - requires jQuery Easing plugin
  $('a.page-scroll').bind('click', function(event) {
    var $anchor = $(this);
    $('html, body').stop().animate({
      scrollTop: ($($anchor.attr('href')).offset().top - 50)
    }, 1250, 'easeInOutExpo');
    event.preventDefault();
  });


  var support = { transitions: Modernizr.csstransitions },
  // transition end event name
    transEndEventNames = { 'WebkitTransition': 'webkitTransitionEnd', 'MozTransition': 'transitionend', 'OTransition': 'oTransitionEnd', 'msTransition': 'MSTransitionEnd', 'transition': 'transitionend' },
    transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
    onEndTransition = function( el, callback ) {
      var onEndCallbackFn = function( ev ) {
        if( support.transitions ) {
          if( ev.target != this ) return;
          this.removeEventListener( transEndEventName, onEndCallbackFn );
        }
        if( callback && typeof callback === 'function' ) { callback.call(this); }
      };
      if( support.transitions ) {
        el.addEventListener( transEndEventName, onEndCallbackFn );
      }
      else {
        onEndCallbackFn();
      }
    };

  new GridFx(document.querySelector('.grid'), {
    imgPosition : {
      x : -0.5,
      y : 1
    },
    onOpenItem : function(instance, item) {
      instance.items.forEach(function(el) {
        if(item != el) {
          var delay = Math.floor(Math.random() * 50);
          el.style.WebkitTransition = 'opacity .5s ' + delay + 'ms cubic-bezier(.7,0,.3,1), -webkit-transform .5s ' + delay + 'ms cubic-bezier(.7,0,.3,1)';
          el.style.transition = 'opacity .5s ' + delay + 'ms cubic-bezier(.7,0,.3,1), transform .5s ' + delay + 'ms cubic-bezier(.7,0,.3,1)';
          el.style.WebkitTransform = 'scale3d(0.1,0.1,1)';
          el.style.transform = 'scale3d(0.1,0.1,1)';
          el.style.opacity = 0;
        }
      });
    },
    onCloseItem : function(instance, item) {
      instance.items.forEach(function(el) {
        if(item != el) {
          el.style.WebkitTransition = 'opacity .4s, -webkit-transform .4s';
          el.style.transition = 'opacity .4s, transform .4s';
          el.style.WebkitTransform = 'scale3d(1,1,1)';
          el.style.transform = 'scale3d(1,1,1)';
          el.style.opacity = 1;

          onEndTransition(el, function() {
            el.style.transition = 'none';
            el.style.WebkitTransform = 'none';
          });
        }
      });
    }
  });

  // Highlight the top nav as scrolling occurs

  // Closes the Responsive Menu on Menu Item Click
  $('.navbar-collapse ul li a').click(function() {
    $('.navbar-toggle:visible').click();
  });

  // Initialize and Configure Scroll Reveal Animation
  window.sr = ScrollReveal();
  sr.reveal('.sr-icons', {
    duration: 600,
    scale: 0.3,
    distance: '0px'
  }, 200);
  sr.reveal('.sr-button', {
    duration: 1000,
    delay: 200
  });
  sr.reveal('.sr-contact', {
    duration: 600,
    scale: 0.3,
    distance: '0px'
  }, 300);



})(jQuery); // End of use strict

$( document ).ready(function() {
  /*var picture = $('.img-banner').attr('src');
  if($( window ).width() < 500) {
    picture = $('.img-banner-mobile').attr('src');
  }
  $('#homepage').backstretch(picture);*/
  $(document).on('click', '.filter', function() {
    $('.grid .all').each(function(i, elt) {
      $(elt).appendTo('.grid-filter');
    });
    $('.filter').removeClass('active');
    $(this).addClass('active');
    $('.grid-filter ' + $(this).data('filter')).each(function(i, elt) {
      $(elt).appendTo('.grid');
    });
    new Masonry(document.querySelector('.grid'), {
      itemSelector: '.grid__item',
      isFitWidth : true
    });
  })

  /*$( '#myCarousel' ).on( 'swipeleft', function() {
    $('#myCarousel').carousel('next');
  });

  $( '#myCarousel' ).on( 'swiperight', function() {
    $('#myCarousel').carousel('prev');
  });*/

  var initialPosition = $('#mainNav').offset().top,
    positionTop = false;

  new WOW().init();

  $(document).on('click', '.img-wrap',function() {
    $('#mainNav').addClass('hide');
  })

  $(window).scroll(function() {
    var height = $(window).scrollTop();
    if(!positionTop) {
      initialPosition = $('#mainNav').offset().top;
    }
    if(height  > $('#mainNav').offset().top && !positionTop) {
      positionTop = true;
      $('#mainNav').addClass('navbar-fixed-top');
    }
    if(height  <= initialPosition) {
      $('#mainNav').removeClass('navbar-fixed-top');
      positionTop = false;
    }
  });
});
