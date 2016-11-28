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


  var initialPosition = $('#mainNav').offset().top,
    positionTop = false;

  new WOW().init();

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
