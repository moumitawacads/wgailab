const sidebar = document.querySelector("#sidebar");
const main = document.querySelector(".main");
const toggleBtn = document.querySelector(".js-sidebar-toggle");

// smooth transition
main.style.transition = "width 0.3s ease";

toggleBtn.addEventListener("click", function () {

    setTimeout(() => {

        if (sidebar.classList.contains("collapsed")) {
            main.style.width = "100%";
        } else {
            main.style.width = "calc(100% - 278px)";
        }

    }, 0);

});