#!/usr/bin/env bash
# Mise en production sur o2switch : récupère le code depuis GitHub et met à jour l'application.
# Usage, depuis le terminal cPanel : bash ~/repositories/PeCc_back/deploy.sh
set -euo pipefail
cd "$(dirname "$0")"

# Le site passe en maintenance pendant la mise à jour et revient en ligne même si une étape échoue.
php artisan down --retry=60 || true
trap 'php artisan up' EXIT

git pull --ff-only
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan optimize:clear
php artisan optimize

echo "Déploiement terminé : $(git log -1 --format='%h %s')"
