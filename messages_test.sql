-- 20 demandes de test pour la table `messages`.
-- service_id : 1 Plomberie, 2 Chauffage, 3 Climatisation, 4 PAC, 5 Ventilation.
-- Dates étalées sur 3 semaines : permet de vérifier le tri décroissant et la
-- pagination (3 pages de 10, 10 et le reste).
-- Les apostrophes françaises sont doublées ('') : c'est l'échappement SQL standard.

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Jean Dupont', 'jean.dupont@email.fr', '06 12 34 56 78', 'Fuite sous l''évier de la cuisine depuis hier soir.\nJ''ai coupé l''arrivée d''eau en attendant. Pouvez-vous passer rapidement ?', 0, 1, '2026-09-22 08:15:00', '2026-09-22 08:15:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Marie Lefèvre', 'm.lefevre@orange.fr', '07 88 45 12 90', 'Bonjour, je souhaite un devis pour le remplacement de ma chaudière gaz, modèle de 2003.', 0, 2, '2026-09-21 17:42:00', '2026-09-21 17:42:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Pierre Moreau', 'pmoreau@gmail.com', '06 74 23 88 01', 'Installation d''une climatisation réversible dans un salon de 35 m².\nMaison de plain-pied, façade exposée plein sud.', 0, 3, '2026-09-21 11:05:00', '2026-09-21 11:05:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Sophie Bernard', 'sophie.bernard@free.fr', '06 33 91 47 22', 'Devis pour une pompe à chaleur air/eau en remplacement d''une chaudière fioul. Maison de 120 m² à Fontenay-le-Comte.', 0, 4, '2026-09-20 15:30:00', '2026-09-20 15:30:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Lucas Girard', 'lucas.girard@email.fr', '07 12 66 30 44', 'VMC très bruyante depuis quelques semaines. Entretien ou remplacement ?', 0, 5, '2026-09-20 09:12:00', '2026-09-20 09:12:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Isabelle Roux', 'i.roux@wanadoo.fr', '06 55 78 12 03', 'WC bouché à l''étage, l''eau remonte dans la douche.\nC''est urgent, nous recevons ce week-end.', 0, 1, '2026-09-19 19:48:00', '2026-09-19 19:48:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Thomas Petit', 'thomas.petit@gmail.com', '06 91 02 35 76', 'Radiateurs froids au rez-de-chaussée alors que la chaudière tourne. Purge déjà faite, sans résultat.', 0, 2, '2026-09-19 14:20:00', '2026-09-19 14:20:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Nathalie Fournier', 'nfournier@email.fr', '07 45 89 66 10', 'Entretien annuel de ma climatisation bi-split, posée par vos soins l''an dernier.', 1, 3, '2026-09-18 10:00:00', '2026-09-18 10:00:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('David Mercier', 'd.mercier@laposte.net', '06 20 14 77 58', 'Ballon d''eau chaude en panne, plus d''eau chaude depuis deux jours.\nModèle Atlantic 200 L, installé en 2015.', 1, 1, '2026-09-18 07:35:00', '2026-09-18 07:35:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Camille Durand', 'camille.durand@email.fr', '07 63 25 41 09', 'Projet de rénovation complète de salle de bain. Je souhaite un rendez-vous sur place pour un chiffrage.', 0, 1, '2026-09-17 16:55:00', '2026-09-17 16:55:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Olivier Blanc', 'o.blanc@orange.fr', '06 82 39 50 17', 'Installation d''une VMC double flux dans une maison neuve en cours de construction.', 0, 5, '2026-09-17 09:25:00', '2026-09-17 09:25:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Émilie Rousseau', 'emilie.rousseau@free.fr', '06 47 18 92 33', 'Ma pompe à chaleur affiche le code erreur E4 et s''arrête toute seule.\nElle a trois ans, encore sous garantie je crois.', 1, 4, '2026-09-16 13:10:00', '2026-09-16 13:10:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Julien Faure', 'julien.faure@gmail.com', '07 05 71 28 64', 'Remplacement de trois radiateurs en fonte par des modèles plus récents et plus économes.', 0, 2, '2026-09-15 18:02:00', '2026-09-15 18:02:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Sandrine Chevalier', 's.chevalier@email.fr', '06 29 84 06 51', 'Devis pour climatiser deux chambres à l''étage. Combles aménagés, invivable l''été.', 1, 3, '2026-09-15 11:47:00', '2026-09-15 11:47:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Antoine Lemaire', 'antoine.lemaire@laposte.net', '06 66 13 74 20', 'Robinet de la salle de bain qui goutte en continu.\nRien d''urgent, quand vous pourrez passer.', 1, 1, '2026-09-14 08:30:00', '2026-09-14 08:30:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Claire Dubois', 'claire.dubois@wanadoo.fr', '07 91 46 82 05', 'Contrat d''entretien annuel pour chaudière gaz : quels sont vos tarifs ?', 0, 2, '2026-09-13 15:18:00', '2026-09-13 15:18:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Mathieu Garnier', 'm.garnier@email.fr', '06 38 27 90 46', 'Besoin d''aide pour le dossier MaPrimeRénov'' dans le cadre de l''installation d''une pompe à chaleur.', 1, 4, '2026-09-12 12:05:00', '2026-09-12 12:05:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Laura Perrin', 'laura.perrin@gmail.com', '07 52 08 63 91', 'Moisissures dans la salle de bain, je pense à un problème de ventilation.\nLa VMC a plus de quinze ans.', 0, 5, '2026-09-11 17:40:00', '2026-09-11 17:40:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Nicolas Meyer', 'nicolas.meyer@free.fr', '06 74 95 31 28', 'Recherche de fuite : le compteur tourne alors que tous les robinets sont fermés.', 1, 1, '2026-09-10 09:55:00', '2026-09-10 09:55:00');

INSERT INTO `messages` (`nom`, `email`, `telephone`, `message`, `traite`, `service_id`, `created_at`, `updated_at`) VALUES ('Valérie Legrand', 'v.legrand@orange.fr', '07 26 60 14 83', 'Devis pour une PAC air/air dans un local professionnel de 80 m².\nDisponible en semaine après 17 h.', 1, 4, '2026-09-09 14:22:00', '2026-09-09 14:22:00');
