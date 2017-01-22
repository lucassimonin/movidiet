$( document ).ready(function() {
    $(document).on("click", "#menu-toggle, .mask, #close, #menu-toggle-mobile", function() {
        $("#wrapper").toggleClass("toggled");
        if($("#wrapper").hasClass("toggled")) {
            $(".mask").hide();
        } else {
            $(".mask").show();
        }
    });
    $(document).on("click", ".redirectToFollow", function() {
        window.location.href = $(this).data("url");
    });

    $(document).on("keyup", "#search", function() {
        // Declare variables
        var input, filter, tr, td, i;
        input = $(this);
        filter = input.val().toUpperCase();
        tr = $("#patients tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < $("#patients tr").length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });

});