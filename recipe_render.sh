#!/bin/sh
# Zippe la recette, l'importe dans LaraPaper comme « ShowPoster (test) » et rapatrie les rendus dans $1
set -e
DIR=$(cd "$(dirname "$0")" && pwd)
(cd "$DIR/recipe/src" && rm -f ../showposter.zip && zip -q -r ../showposter.zip .)
scp -q "$DIR/recipe/showposter.zip" "$DIR/recipe_test.php" "$DIR/test_payload.json" root@192.168.1.110:/tmp/
ssh root@192.168.1.110 'for f in showposter.zip recipe_test.php test_payload.json; do docker cp /tmp/$f larapaper-app-1:/tmp/; done && docker exec -u www-data larapaper-app-1 php artisan tinker /tmp/recipe_test.php 2>&1 | grep -E " ok$|Exception|Error|url=|live payload"; rm -rf /tmp/spr && docker cp larapaper-app-1:/tmp/spr /tmp/spr'
mkdir -p "$1"; scp -q 'root@192.168.1.110:/tmp/spr/*' "$1/"
