document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector(".sidebar");
    const closeBtn = document.querySelector("#btn");
    const searchBtn = document.querySelector(".bx-search");
    const navbarBtn = document.querySelector("button[aria-label='Toggle sidebar']"); // Selector más adecuado
    const overlay = document.createElement("div");

    overlay.classList.add("overlay");
    document.body.appendChild(overlay);

    if (closeBtn) {
        closeBtn.addEventListener("click", function() {
            sidebar.classList.toggle("open");
            toggleOverlay();
            menuBtnChange();
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener("click", function() {
            sidebar.classList.toggle("open");
            toggleOverlay();
            menuBtnChange();
        });
    }

    if (navbarBtn) {
        navbarBtn.addEventListener("click", function() {
            sidebar.classList.toggle("open");
            toggleOverlay();
        });
    }

    function toggleOverlay() {
        if (window.innerWidth <= 1024) {
            overlay.classList.toggle("show");
        }
    }

    function menuBtnChange() {
        if (sidebar.classList.contains("open")) {
            closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else {
            closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        }
    }

    // Para cerrar el sidebar en versión móvil
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 1024) {
            const target = e.target;
            if (!sidebar.contains(target) && !navbarBtn.contains(target) && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                overlay.classList.remove("show");
            }
        }
    });
});
