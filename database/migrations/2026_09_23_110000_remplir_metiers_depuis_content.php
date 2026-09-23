<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Détails rapatriés de la constante PRESTATIONS du content.ts du front.
     * Clé = slug du service, ce qui évite de dépendre de l'orthographe exacte
     * des noms (« Pompe à chaleur » vs « Pompe à Chaleur »).
     */
    private const DETAILS = [
        'plomberie' => [
            ['Recherche et réparation de fuite', "Détection non destructive, réparation de canalisation encastrée et remise en état après intervention."],
            ['Débouchage de canalisation', "Évier, douche, WC et colonne d'évacuation : furet, hydrocurage et inspection caméra si nécessaire."],
            ['Salle de bain clé en main', "Douche à l'italienne, vasque suspendue, WC suspendu : dépose, alimentation, évacuation et pose complète."],
            ['Chauffe-eau & ballon', "Remplacement de cumulus, chauffe-eau instantané ou thermodynamique, détartrage et groupe de sécurité."],
            ['Rénovation de réseau', "Reprise de réseau cuivre ou PER, remplacement de robinetterie et pose d'un adoucisseur d'eau."],
        ],
        'chauffage' => [
            ['Installation de chaudière', "Chaudière gaz à condensation ou fioul : dimensionnement, pose, raccordement et mise en service."],
            ['Entretien annuel', "Visite d'entretien obligatoire, nettoyage du corps de chauffe, contrôle de combustion et attestation."],
            ['Radiateurs & thermostats', "Pose de radiateurs acier ou fonte, robinets thermostatiques et régulation connectée pièce par pièce."],
            ['Plancher chauffant', "Création ou reprise de plancher chauffant basse température, collecteurs et équilibrage des boucles."],
            ['Dépannage chauffage', "Radiateurs froids, pression qui chute, chaudière en sécurité : diagnostic, purge et désembouage du réseau."],
        ],
        'climatisation' => [
            ['Climatiseur split mural', "Étude de puissance, pose de l'unité intérieure et extérieure, liaisons frigorifiques et mise en service."],
            ['Multi-split & gainable', "Plusieurs pièces sur un même groupe, ou gainable discret en faux plafond avec grilles de reprise."],
            ['Climatisation réversible', "Le confort d'été et un appoint de chauffage en hiver avec un seul équipement basse consommation."],
            ['Entretien & recharge de fluide', "Nettoyage des filtres et de l'échangeur, contrôle d'étanchéité et complément de fluide frigorigène."],
            ['Dépannage & mise en conformité', "Recherche de fuite, remplacement de carte électronique et mise en conformité des évacuations de condensats."],
        ],
        'pac' => [
            ['PAC air-eau', "Remplacement de chaudière par une pompe à chaleur air-eau, avec ballon et reprise du réseau de radiateurs."],
            ['PAC air-air', "Chauffage et rafraîchissement par unités murales ou gainables, pilotage pièce par pièce."],
            ['Ballon thermodynamique', "Eau chaude sanitaire économe : 200 à 300 L, installation sur air ambiant ou air extérieur."],
            ["Dossier d'aides RGE", "MaPrimeRénov', CEE et TVA réduite : nous montons le dossier et déduisons les aides de votre devis."],
            ['Entretien & maintenance', "Contrat annuel : contrôle du circuit frigorifique, des pressions, de la régulation et des performances."],
        ],
        'ventilation' => [
            ['VMC simple flux hygroréglable', "Extraction pilotée par le taux d'humidité : caisson, gaines isolées et bouches dans chaque pièce d'eau."],
            ['VMC double flux', "Échangeur haut rendement récupérant jusqu'à 90% des calories de l'air extrait pour préchauffer l'air neuf."],
            ['Nettoyage de gaines & bouches', "Dépoussiérage du réseau, désinfection des bouches et remplacement des filtres du caisson."],
            ["Traitement de l'humidité", "Diagnostic condensation et moisissures, ventilation adaptée des pièces humides et des combles."],
            ['Extraction spécifique', "Buanderie, cave, cuisine professionnelle : hotte, extracteur dédié et compensation d'air neuf."],
        ],
    ];

    public function up(): void
    {
        $maintenant = now();

        foreach (self::DETAILS as $slug => $blocs) {
            $serviceId = DB::table('services')->where('slug', $slug)->value('id');

            // Service absent (renommé, supprimé) : on passe, plutôt que
            // d'échouer et de bloquer toute la migration.
            if (! $serviceId) {
                continue;
            }

            // Le service a déjà des détails : on ne touche à rien. Évite les
            // doublons si la migration est rejouée après une saisie manuelle.
            if (DB::table('metiers')->where('service_id', $serviceId)->exists()) {
                continue;
            }

            DB::table('metiers')->insert(array_map(
                fn ($bloc) => [
                    'titre' => $bloc[0],
                    'description' => $bloc[1],
                    'service_id' => $serviceId,
                    'created_at' => $maintenant,
                    'updated_at' => $maintenant,
                ],
                $blocs,
            ));
        }
    }

    /**
     * Ne supprime que les blocs repris ici, repérés par leur titre : un
     * détail saisi depuis l'admin ne doit pas disparaître avec un rollback.
     */
    public function down(): void
    {
        foreach (self::DETAILS as $slug => $blocs) {
            $serviceId = DB::table('services')->where('slug', $slug)->value('id');

            if (! $serviceId) {
                continue;
            }

            DB::table('metiers')
                ->where('service_id', $serviceId)
                ->whereIn('titre', array_column($blocs, 0))
                ->delete();
        }
    }
};
