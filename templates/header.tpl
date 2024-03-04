<header id="header" class="sticky" role="banner">
    <div id="header_main" class="container_wrap">
        <div class="container">
            <nav class="main-header navbar navbar-expand navbar-dark">
                <ul id="avia-menu-left" class="menu av-main-nav" role="menu">
                    <li id="menu-item-1" class="menu-item menu-item-home menu-item-top-level menu-item-top-level-1 nav-item" role="menuitem" >
                        <a href="/" class="brand-link">
                            <img src="%1$scops_logo.png" alt="COPS" class="brand-image" style="opacity: .8">
                        </a>
                    </li>
                </ul>
                <ul id="avia-menu-center" class="menu av-main-nav nav notify-row justify-content-end" role="menu">%5$s</ul>
                <ul id="avia-menu-right" class="menu av-main-nav nav justify-content-end" role="menu">
                    <li class="nav-item dropdown">
                        <img class="img-xs rounded-circle mask" src="%1$smasks/%3$s.jpg" alt="Masque de %2$s">
                        <button class="btn text-white dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">%2$s</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item %4$s" href="/settings"><i class="fa-solid fa-gear"></i> Settings</a></li>
                            <li><a class="dropdown-item %4$s" href="/profile"><i class="fa-solid fa-user"></i> Profil</a></li>
                            <li><a class="dropdown-item %4$s" href="#"><i class="fa-solid fa-envelope"></i> Mes Messages</a></li>
                            <li><a class="dropdown-item %4$s" href="#"><i class="fa-solid fa-lock"></i> Verrouiller</a></li>
                            <li><a class="dropdown-item" href="?logout=logout"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
