/* --------------------------------------------
   Filtre du catalogue de véhicules
--------------------------------------------- */

const formulaireFiltres = document.getElementById('catalog-filter-nav__link-form');
const rechercheVehicule = document.getElementById('vehicle-search');
const selectCarburant = document.getElementById('vehicle-fuel');
const selectKilometrage = document.getElementById('vehicle-mileage');
const selectPuissance = document.getElementById('vehicle-power');
const cartesVehicules = document.querySelectorAll('.catalog-card');
const compteurResultats = document.getElementById('vehicle-result-count');
const messageAucunResultat = document.getElementById('catalog-filter-nav__link-field-empty');

function filtrerVehicules() {
    const recherche = rechercheVehicule.value.trim().toLowerCase();
    const carburant = selectCarburant.value.trim().toLowerCase();
    const kilometrageMaximum = parseInt(selectKilometrage.value, 10);
    const puissanceMinimum = parseInt(selectPuissance.value, 10);
    let nombreVisible = 0;

    cartesVehicules.forEach(carte => {
        const texteVehicule = carte.dataset.search.toLowerCase();
        const carburantVehicule = carte.dataset.fuel.toLowerCase();
        const kilometrageVehicule = parseInt(carte.dataset.mileage, 10);
        const puissanceVehicule = parseInt(carte.dataset.power, 10);
        let visible = true;

        // Recherche par marque, modèle ou autre information de la carte.
        if (recherche && !texteVehicule.includes(recherche)) {
            visible = false;
        }

        // Filtre par carburant.
        if (carburant && carburantVehicule !== carburant) {
            visible = false;
        }

        // Filtre par kilométrage maximum.
        if (
            !Number.isNaN(kilometrageMaximum)
            && kilometrageVehicule > kilometrageMaximum
        ) {
            visible = false;
        }

        // Filtre par puissance minimum.
        if (
            !Number.isNaN(puissanceMinimum)
            && puissanceVehicule < puissanceMinimum
        ) {
            visible = false;
        }

        carte.style.display = visible ? '' : 'none';

        if (visible) {
            nombreVisible += 1;
        }
    });

    compteurResultats.textContent = nombreVisible === 1
        ? '1 véhicule disponible'
        : `${nombreVisible} véhicules disponibles`;

    if (messageAucunResultat) {
        messageAucunResultat.style.display = nombreVisible === 0
            ? ''
            : 'none';
    }
}

rechercheVehicule.addEventListener('input', filtrerVehicules);
selectCarburant.addEventListener('change', filtrerVehicules);
selectKilometrage.addEventListener('change', filtrerVehicules);
selectPuissance.addEventListener('change', filtrerVehicules);

formulaireFiltres.addEventListener('reset', () => {
    setTimeout(filtrerVehicules, 0);
});

filtrerVehicules();
