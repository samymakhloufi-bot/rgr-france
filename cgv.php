<?php
declare(strict_types=1);

$activePage = 'CGV';
$metaDescription = 'Conditions générales applicables aux prestations et ventes proposées par RGR France.';
$pageCss = '/assets/css/pages/legal.css';


    require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>
    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="legal-page">
            <header class="legal-hero">
                <div class="legal-container legal-hero__content">
                    <p class="legal-eyebrow">RGR France · Informations contractuelles</p>
                    <h1 class="legal-title">Conditions générales</h1>
                    <p class="legal-intro">Conditions applicables à l’utilisation du site, aux prestations automobiles et à la vente de pièces proposées par RGR France.</p>
                </div>
            </header>

            <div class="legal-container legal-layout">
                <nav class="legal-summary" aria-label="Sommaire des conditions générales">
                    <h2 class="legal-summary__title">Sommaire</h2>
                    <ol class="legal-summary__list">
                        <li><a class="legal-summary__link" href="#utilisation"><span>01</span>Utilisation du site</a></li>
                        <li><a class="legal-summary__link" href="#donnees"><span>02</span>Données personnelles</a></li>
                        <li><a class="legal-summary__link" href="#liens"><span>03</span>Liens hypertextes</a></li>
                        <li><a class="legal-summary__link" href="#cookies"><span>04</span>Cookies</a></li>
                        <li><a class="legal-summary__link" href="#contenu"><span>05</span>Contenu du site</a></li>
                        <li><a class="legal-summary__link" href="#illicite"><span>06</span>Contenu illicite</a></li>
                        <li><a class="legal-summary__link" href="#droit"><span>07</span>Droit applicable</a></li>
                        <li><a class="legal-summary__link" href="#programmation"><span>08</span>Programmation</a></li>
                        <li><a class="legal-summary__link" href="#paiement"><span>09</span>Défaut de paiement</a></li>
                        <li><a class="legal-summary__link" href="#gardiennage"><span>10</span>Gardiennage</a></li>
                        <li><a class="legal-summary__link" href="#importation"><span>11</span>Importation</a></li>
                        <li><a class="legal-summary__link" href="#homologation"><span>12</span>Homologation</a></li>
                        <li><a class="legal-summary__link" href="#protection"><span>13</span>Protection client</a></li>
                        <li><a class="legal-summary__link" href="#pieces"><span>14</span>Vente de pièces</a></li>
                    </ol>
                </nav>

                <article class="legal-content">
                    <section class="legal-section" id="utilisation">
                        <p class="legal-section__number">Article 01</p>
                        <h2>Conditions d’utilisation du site</h2>
                        <p>Toute personne accédant au site <a href="https://rgrfrance.fr">rgrfrance.fr</a> (ci-après « le Site ») s’engage à respecter les présentes Conditions Générales d’Utilisation. Ces conditions peuvent être complétées par des conditions particulières selon les services proposés.</p>
                        <p>RGR France, située au 19 rue des Fours Banaux, 89690 Chéroy, se réserve le droit de modifier à tout moment l’accès au Site ainsi que les présentes conditions. L’utilisateur est invité à consulter régulièrement cette rubrique pour prendre connaissance des mises à jour.</p>
                    </section>

                    <section class="legal-section" id="donnees">
                        <p class="legal-section__number">Article 02</p>
                        <h2>Données personnelles</h2>
                        <h3>2.1 Traitement des données</h3>
                        <p>Les données collectées sont destinées à RGR France, à ses filiales et à son réseau commercial, à des fins :</p>
                        <ul class="legal-list">
                            <li>d’enquêtes ;</li><li>d’analyses ;</li><li>de communications institutionnelles ;</li><li>de gestion clients ;</li><li>ou, avec votre accord, de prospection commerciale.</li>
                        </ul>
                        <p>Elles sont traitées conformément au <strong>RGPD</strong> et à la loi Informatique et Libertés modifiée.</p>
                        <p>Vous disposez d’un droit d’accès, de rectification, d’opposition, de suppression et de portabilité. Toute demande peut être adressée à : <strong>RGR France – 19 rue des Fours Banaux, 89690 Chéroy</strong>, accompagnée d’un justificatif d’identité.</p>
                        <h3>2.2 Données de connexion</h3>
                        <p>Conformément à la loi du 21 juin 2004, l’hébergeur conserve les données de connexion, couvertes par le secret professionnel.</p>
                    </section>

                    <section class="legal-section" id="liens">
                        <p class="legal-section__number">Article 03</p><h2>Liens hypertextes</h2>
                        <p>Toute création de lien vers le Site doit faire l’objet d’une demande préalable auprès du Responsable du Site. RGR France décline toute responsabilité concernant les contenus accessibles via des liens hypertextes externes.</p>
                    </section>

                    <section class="legal-section" id="cookies">
                        <p class="legal-section__number">Article 04</p><h2>Cookies</h2>
                        <p>Des cookies peuvent être installés lors de la navigation. Ils permettent d’enregistrer des informations relatives à votre navigation, sans permettre votre identification.</p>
                        <p>Le Site utilise Google Analytics (version conforme RGPD). Vous pouvez vous opposer à l’enregistrement des cookies via les paramètres de votre navigateur.</p>
                    </section>

                    <section class="legal-section" id="contenu">
                        <p class="legal-section__number">Article 05</p><h2>Contenu du site</h2>
                        <p>RGR France et ses prestataires s’efforcent d’assurer l’exactitude et la mise à jour des informations publiées. Ils se réservent le droit de modifier le contenu du Site à tout moment.</p>
                    </section>

                    <section class="legal-section" id="illicite">
                        <p class="legal-section__number">Article 06</p><h2>Contenu illicite</h2>
                        <p>Vous pouvez signaler tout contenu illicite à l’hébergeur via le formulaire prévu à cet effet.</p>
                        <p>Toute dénonciation abusive est passible d’un an d’emprisonnement et de 15 000 € d’amende.</p>
                    </section>

                    <section class="legal-section" id="droit">
                        <p class="legal-section__number">Article 07</p><h2>Droit applicable</h2>
                        <p>Les présentes CGU sont soumises au droit français.</p>
                    </section>

                    <section class="legal-section" id="programmation">
                        <p class="legal-section__number">Article 08 · Prestations RGR France</p>
                        <h2>Programmation des véhicules</h2>
                        <h3>8.1 Acceptation du devis</h3><p>Le devis est validé par signature, versement d’arrhes ou paiement complet. Un acompte de 50 % est exigé.</p>
                        <h3>8.2 Modification ou résiliation</h3><p>Toute modification doit être demandée par écrit avant intervention. En cas de résiliation acceptée, l’acheteur reste redevable du montant total du devis. Les arrhes ne sont jamais remboursées.</p>
                        <h3>8.3 Prix</h3><p>Les prix affichés sont indicatifs. Seul le prix figurant sur le devis signé fait foi.</p>
                        <h3>8.4 Installation</h3><p>Les installations sont réalisées selon l’ordre d’arrivée et la disponibilité des pièces. Les délais sont fournis à titre indicatif et ne constituent pas un engagement contractuel. Ils ne peuvent donner lieu à indemnisation.</p>
                        <p>En cas de panne bloquante, RGR France informe le client et propose une solution. Toute réparation non prévue au devis est facturée <strong>minimum 100 € TTC</strong>.</p>
                        <h3>8.5 Garantie</h3><p>La garantie porte exclusivement sur la reprogrammation. Une remise en configuration d’origine est possible gratuitement une fois dans les 5 ans. Au-delà : <strong>250 € TTC</strong>.</p><p>Aucune pièce moteur ni dommage indirect n’est couvert.</p>
                        <h3>8.6 Paiement</h3><p>Le paiement est exigible comptant sauf mention contraire. Les modalités de retard sont définies à l’article 9.</p>
                        <h3>8.7 Garantie constructeur</h3><p>Le constructeur peut refuser sa garantie après modification. RGR France ne peut être tenue responsable de ce refus.</p>
                        <h3>8.8 Assurance responsabilité civile</h3><p>Le client doit informer son assureur des modifications. RGR France ne peut être tenue responsable d’un refus d’indemnisation.</p><p>RGR France est couverte par une assurance RC professionnelle et multirisque garage.</p>
                        <h3>8.9 Responsabilité du vendeur</h3><p>Responsabilité engagée uniquement dans les conditions prévues (essais, déplacements, incendie, vol…). Les objets présents dans le véhicule ne sont jamais couverts.</p>
                        <h3>8.10 Obligations du client</h3><p>Le client est responsable :</p>
                        <ul class="legal-list"><li>du choix du produit ;</li><li>de l’entretien du véhicule ;</li><li>des déclarations réglementaires ;</li><li>de la coopération nécessaire à l’exécution des travaux.</li></ul>
                        <h3>8.11 Protection des données</h3><p>Les données peuvent être utilisées conformément aux lois en vigueur.</p>
                        <h3>8.12 Propriété intellectuelle</h3><p>Les logiciels restent la propriété exclusive de RGR France. Toute copie ou modification est interdite. Violation = pénalité de <strong>15× le montant facturé</strong>.</p>
                        <h3>8.13 Compétence territoriale</h3><p>Pour les professionnels : tribunaux compétents de <strong>Sens (89)</strong>. Pour les consommateurs : compétence du tribunal du domicile du client (obligation légale).</p>
                        <h3>8.14 Nullité d’une clause</h3><p>La nullité d’une clause n’affecte pas les autres dispositions.</p>
                    </section>

                    <section class="legal-section" id="paiement">
                        <p class="legal-section__number">Article 09</p><h2>Défaut de paiement</h2>
                        <p>En cas de non-paiement total ou partiel à échéance, le Client s’expose à :</p>
                        <ul class="legal-list"><li>suspension immédiate des prestations ;</li><li>pénalités de retard ;</li><li>indemnité forfaitaire de 40 € (professionnels) ;</li><li>refacturation des frais de recouvrement ;</li><li>procédure judiciaire ;</li><li>rétention du véhicule et des documents ;</li><li>réserve de propriété jusqu’au paiement intégral.</li></ul>
                    </section>

                    <section class="legal-section" id="gardiennage">
                        <p class="legal-section__number">Article 10</p><h2>Mise à disposition du véhicule et gardiennage</h2>
                        <p>Le client dispose de <strong>5 jours calendaires</strong> pour récupérer son véhicule. Au-delà : <strong>30 € TTC / jour</strong> de gardiennage. Les frais courent jusqu’au paiement intégral et enlèvement effectif.</p>
                    </section>

                    <section class="legal-section" id="importation">
                        <p class="legal-section__number">Article 11</p><h2>Consultant en importation de véhicules américains</h2>
                        <h3>Commandes</h3><p>Toute validation de projet implique l’acceptation des présentes CGV.</p>
                        <h3>Prix</h3><p>Les prix sont indiqués en USD. Le prix final dépend du taux de change au moment de la facturation.</p>
                        <h3>Paiements</h3><div class="legal-note"><p>Les cinq étapes de paiement doivent être replacées ici depuis le document commercial validé.</p></div>
                        <h3>Garantie</h3>
                        <p>Les véhicules américains importés bénéficient de la garantie légale de conformité et de la garantie des vices cachés, conformément aux dispositions légales en vigueur dans l’état où il se trouve.</p>
                        <p>Le consultant importateur s’engage à réduire les risques de tomber sur une anomalie, en utilisant leur savoir-faire et le matériel concessionnaire.</p>
                        <p>Le consultant importateur sélectionne principalement des véhicules de concession Ford ou Lincoln avec une garantie d’entretien et de contrôle. À défaut, d’autres concessions peuvent être proposées sans garantie d’entretien avant exportation.</p>
                        <p>Le consultant importateur sera force de proposition si des difficultés sont rencontrées sur le véhicule importé. Le consultant importateur est entouré de plusieurs partenaires afin de pallier aux difficultés :</p>
                        <ul class="legal-list"><li>Ford MB auto ;</li><li>Al-performance ;</li><li>Carrosserie Braytoise AD ;</li><li>Sellerie du Lys ;</li><li>Easy import auto (USA).</li></ul>
                        <p>Le consultant importateur s’engage à faire preuve de transparence sur l’importation, sur les risques et sur l’état du véhicule.</p>
                        <p>Le consultant importateur vous traduira le Carfax, les campagnes de rappel et l’historique du véhicule avant l’achat.</p>
                        <p>Le consultant importateur se réserve le droit de ne pas accepter un « deal », sur un véhicule ne correspondant pas à ses exigences.</p>
                        <h3>Responsabilité</h3><p>RGR France n’est pas responsable :</p>
                        <ul class="legal-list"><li>des dommages liés au transport ;</li><li>de l’état mécanique avant réception ;</li><li>des dommages indirects.</li></ul>
                        <p>Deux assurances transport sont souscrites via le partenaire (3,02 % du montant du véhicule).</p>
                        <h3>Litiges</h3><p>Tentative amiable obligatoire. À défaut : tribunaux du siège social (professionnels) ou tribunal du domicile du consommateur.</p>
                    </section>

                    <section class="legal-section" id="homologation">
                        <p class="legal-section__number">Article 12</p><h2>Homologation</h2>
                        <div class="legal-note"><p>Le document source ne contient pas le texte juridique de cet article. Il doit être ajouté ici après validation.</p></div>
                    </section>

                    <section class="legal-section" id="protection">
                        <p class="legal-section__number">Article 13</p><h2>Protection client</h2>
                        <p>RGR France informe le client de son droit à la médiation : <strong>SAS Médiation Solution – 222 chemin de la Bergerie, 01800 Saint-Jean-de-Niost</strong>.</p>
                        <p>Conformément à l’article L612-1 du Code de la consommation, le client peut saisir gratuitement le médiateur.</p>
                        <p>Les CGV applicables sont celles en vigueur au moment de la commande.</p>
                    </section>

                    <section class="legal-section" id="pieces">
                        <p class="legal-section__number">Article 14</p><h2>Vente de pièces automobiles et droit de rétractation</h2>
                        <p>RGR France propose à la vente des pièces automobiles neuves ou d’occasion destinées à l’entretien, la réparation, la modification ou l’homologation des véhicules. Conformément aux articles L221-18 et suivants du Code de la consommation, le client consommateur dispose d’un délai légal de 14 jours à compter de la réception de la pièce pour exercer son droit de rétractation, sans avoir à justifier de motifs ni à supporter de pénalités.</p>
                        <p>Le droit de rétractation ne peut toutefois être exercé dans les cas suivants :</p>
                        <ul class="legal-list"><li>pièces sur mesure, programmées, codées, flashées ou personnalisées pour le véhicule du client ;</li><li>pièces spécialement commandées ou importées à la demande du client, non stockées par RGR France ;</li><li>pièces ayant été montées, installées, essayées, modifiées ou présentant des traces d’utilisation ;</li><li>pièces dont l’emballage d’origine est endommagé, manquant ou ne permettant plus leur remise en stock.</li></ul>
                        <p>Pour exercer son droit de rétractation, le client doit notifier sa décision par écrit à RGR France. La pièce doit être retournée neuve, non montée, dans son emballage d’origine, accompagnée de tous ses accessoires et documents. Les frais de retour sont à la charge du client, sauf erreur imputable à RGR France.</p>
                        <p>Après réception et vérification de la pièce, RGR France procédera au remboursement dans un délai de 14 jours, selon le même moyen de paiement que celui utilisé lors de la commande. RGR France se réserve le droit de refuser le remboursement si la pièce retournée ne respecte pas les conditions ci-dessus ou si elle présente une dégradation, une usure ou un dommage résultant d’une mauvaise manipulation.</p>
                        <h3>Disponibilité et délais de livraison</h3>
                        <p>Les pièces automobiles sont proposées dans la limite des stocks disponibles. En cas d’indisponibilité après commande, RGR France informe le client et propose un remplacement, un délai supplémentaire ou un remboursement intégral.</p>
                        <p>Les délais de livraison sont fournis à titre indicatif et dépendent des fournisseurs, transporteurs et conditions logistiques. Ils ne constituent pas un engagement contractuel et ne peuvent donner lieu à indemnisation.</p>
                        <h3>Garantie légale de conformité et vices cachés</h3>
                        <p>Les pièces neuves bénéficient des garanties légales de conformité et des vices cachés. Les pièces d’occasion sont vendues testées mais peuvent présenter une usure normale. Elles ne bénéficient pas de garantie commerciale sauf mention contraire.</p>
                        <p>La garantie ne couvre pas les dommages liés à un montage incorrect, une mauvaise utilisation, une modification de la pièce, un défaut provenant d’un autre élément du véhicule ou un usage non conforme.</p>
                        <p>La responsabilité de RGR France est limitée au remplacement ou au remboursement de la pièce, à l’exclusion de tout dommage indirect.</p>
                        <h3>Article 14 bis – Pièces importées des États-Unis</h3>
                        <p>Les pièces automobiles importées des États-Unis à la demande du client constituent des commandes spécifiques. Conformément à l’article L221-28 du Code de la consommation, aucun droit de rétractation ne s’applique lorsque la pièce fait l’objet d’un approvisionnement sur mesure, non stocké ou d’une commande spéciale auprès d’un fournisseur étranger.</p>
                        <p>Toute commande de pièce importée est ferme et définitive dès validation du devis. Les délais d’acheminement, de dédouanement et de livraison sont indicatifs et peuvent varier selon les transporteurs ou les procédures douanières. Ils ne peuvent donner lieu à indemnisation.</p>
                        <p>RGR France informe le client de l’avancement de l’importation et des éventuels retards.</p>
                    </section>
                </article>
            </div>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
