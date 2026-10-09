<header> 
    
            <div class="nav-bar">
            
                <div id="logo">
                    <a href="./index.php"> 
                        <img src='assets/images/icons/logo-rgr-vector.svg' class="logo-img" alt='RGR France Logo' title='RGR France Logo' />
                    </a>
                </div>
                
                <nav class="nav-menu">
                    <button aria-label="Ouvrir le menu"  class="burger-btn" aria-expanded="false" aria-controls="nav-list"> ☰ </button>
                    <ul class="nav-list"> 
                        <li> <a href="./index.php" class="<?php echo $activePage ==='Accueil' ? 'nav-active' : '' ;?>" >Accueil</a></li>
                        <li> <a href="./importation.php" class="<?php echo $activePage ==='Importation' ? 'nav-active' : '' ;?>">Importation</a></li>
                        <li> <a href="./homologation.php" class="<?php echo $activePage ==='Homologation' ? 'nav-active' : '' ;?>" >Homologation</a></li>
                        <li> <a href="./vehicules.php" class="<?php echo $activePage ==='Nos véhicules' ? 'nav-active' : '' ;?>" >Nos véhicules</a></li>
                        <li class="dropdown">
                            <button class="dropbtn" aria-expanded="false" aria-controls="drop-menu">Plus <span class="arrow">▼</span></button>
                            <ul class="drop-menu" id="drop-menu">
                                <li> <a href="./product.php" class="<?php echo $activePage ==='Nos produits' ? 'nav-active' : '' ;?>" >Nos produits</a></li>
                                <li> <a href="./team.php" class="<?php echo $activePage ==='Notre équipe' ? 'nav-active' : '' ;?>" >Notre équipe</a></li>
                                <li> <a href="./avis.php" class="<?php echo $activePage ==='Nos avis' ? 'nav-active' : '' ;?>" >Nos avis</a></li>
                            </ul>
                        </li>
                    </ul>
                    <button aria-label="Nous contacter" class="contact">
                        <a href="./contact.php" class="<?php echo $activePage ==='Nous contacter' ? 'nav-active' : '' ;?>" >Nous contacter</a>
                    </button>
                </nav>
            
            </div>
            <script src="./assets/js/main.js"></script>
        </header>
        