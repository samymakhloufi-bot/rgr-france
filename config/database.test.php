<?php

$adresse = '19, rue des Fours Banaux, 89690 Chéroy';
$siren = '999227572';
$telephone = '07 49 84 80 45';
$email = 'contact@rgrfrance.fr';

$telephoneLink = preg_replace(
    '/[^0-9+]/',
    '',
    $telephone
);

$horairesFooter = [
    [
        'jour' => 'Lundi',
        'ouverture_matin' => '09:00:00',
        'fermeture_apm' => '18:00:00',
        'ferme' => false
    ],
    [
        'jour' => 'Mardi',
        'ouverture_matin' => '09:00:00',
        'fermeture_apm' => '18:00:00',
        'ferme' => false
    ],
    [
        'jour' => 'Mercredi',
        'ouverture_matin' => '09:00:00',
        'fermeture_apm' => '18:00:00',
        'ferme' => false
    ],
    [
        'jour' => 'Jeudi',
        'ouverture_matin' => '09:00:00',
        'fermeture_apm' => '18:00:00',
        'ferme' => false
    ],
    [
        'jour' => 'Vendredi',
        'ouverture_matin' => '09:00:00',
        'fermeture_apm' => '18:00:00',
        'ferme' => false
    ],
    [
        'jour' => 'Samedi',
        'ouverture_matin' => null,
        'fermeture_apm' => null,
        'ferme' => true
    ],
    [
        'jour' => 'Dimanche',
        'ouverture_matin' => null,
        'fermeture_apm' => null,
        'ferme' => true
    ]
];