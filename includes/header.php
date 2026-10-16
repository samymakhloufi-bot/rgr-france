<header class="site-header"> 
    
            <div class="nav-bar">
            
                <div id="logo">
                    <a href="./index.php"> 
                        <img src='assets/images/icons/logo-rgr-vector.svg' class="logo-img" alt='RGR France Logo' title='RGR France Logo' />
                    </a>
                </div>
                
                <nav class="nav-menu">
                    <button type ="button"aria-label="Ouvrir le menu"  class="burger-btn" aria-expanded="false" aria-controls="nav-list"> ☰ </button>
                    <ul class="nav-list" id="nav-list"> 
                        <li> <a href="<?= htmlspecialchars(BASE_URL . '/index.php', ENT_QUOTES, 'UTF-8') ?>" class="<?php echo $activePage ==='Accueil' ? 'nav-active' : '' ;?>" >Accueil</a></li>
                        <li> <a href="<?= htmlspecialchars(BASE_URL . '/importation.php', ENT_QUOTES, 'UTF-8') ?>" class="<?php echo $activePage ==='Importation' ? 'nav-active' : '' ;?>">Importation</a></li>
                        <li> <a href="<?= htmlspecialchars(BASE_URL . '/homologation.php', ENT_QUOTES, 'UTF-8') ?>" class="<?php echo $activePage ==='Homologation' ? 'nav-active' : '' ;?>" >Homologation</a></li>
                        <li> <a href="<?= htmlspecialchars(BASE_URL . '/vehicles.php', ENT_QUOTES, 'UTF-8') ?>" class="<?php echo $activePage ==='Nos véhicules' ? 'nav-active' : '' ;?>" >Nos véhicules</a></li>
                        <li class="dropdown">
                            <button type="button" class="dropbtn" aria-expanded="false" aria-controls="drop-menu">Plus <span class="arrow">▼</span></button>
                            <ul class="drop-menu" id="drop-menu">
                                <li> <a href="<?= htmlspecialchars(BASE_URL . '/nos-produits.php', ENT_QUOTES, 'UTF-8') ?>" class="<?php echo $activePage ==='Nos produits' ? 'nav-active' : '' ;?>" >Nos produits</a></li>
                                <li> <a href="<?= htmlspecialchars(BASE_URL . '/avis.php', ENT_QUOTES, 'UTF-8') ?>" class="<?php echo $activePage ==='Nos avis' ? 'nav-active' : '' ;?>" >Nos avis</a></li>
                            </ul>
                        </li>
                    </ul>
                        <a href="<?= htmlspecialchars(BASE_URL . '/contact.php', ENT_QUOTES, 'UTF-8') ?>" class="contact <?php echo $activePage ==='Nous contacter' ? 'nav-active' : '' ;?>" >Nous contacter</a>

                </nav>
            
            </div>
            <script src="./assets/js/main.js"></script>
        </header>
        