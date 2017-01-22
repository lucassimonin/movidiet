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
var isEmail = function(myVar){
  var regEmail = new RegExp('^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$','i');
  return regEmail.test(myVar);
}

var resetForm = function() {
  $('.form-control').val('');
}

$( document ).ready(function() {

  $(document).on('click', '.go-contact', function(){
    $("#contact_type_subject").val($(this).data('id'));

    return true;
  });

  new WOW().init();
  if($('#mainNav').length) {
    var initialPosition = $('#mainNav').offset().top,
        positionTop = false;

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
  }


  $("form#form-contact").submit(function (e) {
    e.preventDefault();

    var errors = 0;
    // Cheking licenceId input content
    if (!$("#contact_type_name").val()) {
      $("#contact_type_name").addClass("has-error");
      errors++;
    }

    if ($("#contact_type_email").val() && !isEmail($("#contact_type_email").val())) {
      $("#contact_type_email").addClass("has-error");
      errors++;
    }

    // Cheking expirationDate input content
    if (!$("#contact_type_message").val()) {
      $("#contact_type_message").addClass("has-error");
      errors++;
    }

    // if there are no errors we can post the form
    if (errors === 0) {
      var formData = new FormData($(this)[0]);
      // Starting spinner
      $("#btn-contact").attr("disabled", true);
      // Posting ajax + form with documents
      $.ajax({
        url: "/add-contact",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false
      }).done(function (data) {
        // Handling return (error_code = 0 means no errors)
        if (data.error_code === 0) {
          $('.msg').html('Message envoyé !');
          resetForm();
        }
        return false;
      });
    }
  });
});
