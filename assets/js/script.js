document.addEventListener("DOMContentLoaded", function () {

    const menuLinks = document.querySelectorAll(".menu a");

    menuLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            menuLinks.forEach(function (item) {
                item.classList.remove("active");
            });

            this.classList.add("active");

        });

    });

});