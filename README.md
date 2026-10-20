# ShowPoster

**[English](#english) · [Français](#français)**

![ShowPoster on a color screen](recipe/screenshots/fr-color-upcoming.png)

---

## English

*What's on next at the movies, drawn as a poster.*

A TRMNL plugin that puts one movie on a poster: the next theatrical releases in your country, or the movies showing now, with their poster art, a countdown to the release or the TMDB audience score. It is part of the Poster suite with [GamePoster](http://192.168.1.109:3000/Anthony/GamePoster), [FlightPoster](http://192.168.1.109:3000/Anthony/FlightPoster) and [WeatherPoster](http://192.168.1.109:3000/Anthony/WeatherPoster), and shares their look.

No server to install: the data comes straight from [The Movie Database (TMDB)](https://www.themoviedb.org), with a free API key.

### Features

- **E-ink and color**: a pure black & white style built for 1-bit screens (TRMNL OG, TRMNL X), and a color style for LCD screens
- **Every layout**: full screen, half horizontal, half vertical and quadrant (mashups)
- **Two sections**, each with its own sky color: *Coming soon* (dusk) and *Now showing* (gold)
- **Rotation**: every hour the poster moves to another of the five most awaited (or most watched) movies, and alternates between both sections, or stays on the section you pick
- **Score stamp**: the number of days before the release, or the TMDB audience score out of 100
- **Next releases** listed in calendar order, with genres and original title
- **Your country**: France, Belgium, Switzerland, Canada, United States, United Kingdom
- **English or French**
- Movies without poster art get a clapperboard illustration

### Screenshots

| | |
|---|---|
| ![E-ink, coming soon](recipe/screenshots/en-eink-upcoming.png) | ![Color, now showing](recipe/screenshots/fr-color-now.png) |
| E-ink · coming soon | Color · now showing |
| ![TRMNL X, coming soon](recipe/screenshots/en-x-upcoming.png) | ![Color, rotation](recipe/screenshots/en-color-rotate-now2.png) |
| TRMNL X · coming soon | Color · rotation |
| ![Half horizontal](recipe/screenshots/en-hh.png) | ![Half vertical](recipe/screenshots/fr-hv.png) |
| Half horizontal | Half vertical |

### Installation

1. Create a free account on [themoviedb.org](https://www.themoviedb.org/signup), then copy the **API Key** (v3, not the Read Access Token) from [Settings → API](https://www.themoviedb.org/settings/api).
2. **TRMNL (usetrmnl.com)**: create a *Private Plugin* with the *Polling* strategy, then copy:
   - `polling_url` from `recipe/src/settings.yml` into *Polling URL* (three lines, one URL each);
   - the `custom_fields` from `recipe/src/settings.yml` into *Custom fields* (YAML);
   - `recipe/src/shared.liquid` into *Shared markup*, and each `recipe/src/<layout>.liquid` into the matching layout tab.

   The `recipe/src/` folder follows the [trmnlp](https://github.com/usetrmnl/trmnlp) layout.

   **LaraPaper / BYOS**: *Plugins → Import recipe*, pick `recipe/showposter.zip` (built by `recipe_render.sh`, or zip the contents of `recipe/src/`).
3. Fill in your settings, starting with the TMDB API key.

### Settings

| Field | Purpose |
|---|---|
| TMDB API key | Your free TMDB API key (v3) |
| Country | Release dates of this country |
| Poster | *Rotate* (both sections), or only *Coming soon*, or only *Now showing* |
| Language | English or French (titles, genres and dates) |
| Style | E-ink (black & white) or Color |

### Development

| File | Purpose |
|---|---|
| `recipe_render.sh <folder>` | Zips the recipe, imports it into LaraPaper as "ShowPoster (test)", renders every test case and copies the PNGs into `<folder>` |
| `recipe_test.php` | The test cases (section, rotation, layout, device, language, missing poster, errors, missing key) run inside LaraPaper |
| `test_payload.json` | A frozen TMDB schedule for France (October 20, 2026) used by the test cases |
| `update.php` | Copies the code of "ShowPoster (test)" into the installed plugin, keeping its settings |
| `install.php` | First install: imports the recipe as "ShowPoster"; the API key is then entered in its settings |

### Notes

- The screen is built with JavaScript at render time, so the renderer must run JavaScript.
- Poster art is loaded from TMDB at render time; on e-ink it is turned to grayscale and dithered by the renderer.
- TMDB lists re-releases and movies already out among the upcoming ones: only dates still to come are kept, and *Now showing* only keeps movies released in the last three months.
- The original title is shown only when it differs and is written in the Latin alphabet.
- In LaraPaper, the half and quadrant layouts render blank on the color (Echo Show) screen model, for every Poster plugin: they are tested on e-ink only.
- This product uses the TMDB API but is not endorsed or certified by TMDB.

---

## Français

*Ce qui sort bientôt au cinéma, façon affiche.*

Un plugin TRMNL qui met un film à l'affiche : les prochaines sorties en salle dans votre pays, ou les films à l'affiche, avec leur affiche, un compte à rebours avant la sortie ou la note des spectateurs TMDB. Il fait partie de la suite Poster avec [GamePoster](http://192.168.1.109:3000/Anthony/GamePoster), [FlightPoster](http://192.168.1.109:3000/Anthony/FlightPoster) et [WeatherPoster](http://192.168.1.109:3000/Anthony/WeatherPoster), dont il reprend le style.

Aucun serveur à installer : les données viennent directement de [The Movie Database (TMDB)](https://www.themoviedb.org), avec une clé d'API gratuite.

### Fonctionnalités

- **E-ink et couleur** : un style noir et blanc pur pour les écrans 1 bit (TRMNL OG, TRMNL X) et un style couleur pour les écrans LCD
- **Tous les formats** : plein écran, demi horizontal, demi vertical et quart d'écran (mashups)
- **Deux rubriques**, chacune avec sa couleur de ciel : *Prochainement* (crépuscule) et *À l'affiche* (doré)
- **Rotation** : chaque heure, l'affiche passe à un autre des cinq films les plus attendus (ou les plus vus) et alterne entre les deux rubriques, ou reste sur la rubrique choisie
- **Tampon** : le nombre de jours avant la sortie, ou la note des spectateurs TMDB sur 100
- **Les sorties suivantes** dans l'ordre du calendrier, avec les genres et le titre original
- **Votre pays** : France, Belgique, Suisse, Canada, États-Unis, Royaume-Uni
- **Français ou anglais**
- Un film sans affiche est illustré par un clap

### Captures

| | |
|---|---|
| ![Couleur, prochainement](recipe/screenshots/fr-color-upcoming.png) | ![E-ink, sans affiche](recipe/screenshots/fr-eink-noimg.png) |
| Couleur · prochainement | E-ink · film sans affiche |
| ![Quart d'écran](recipe/screenshots/en-q.png) | ![Demi horizontal, à l'affiche](recipe/screenshots/fr-hh.png) |
| Quart d'écran | Demi horizontal · à l'affiche |

### Installation

1. Créez un compte gratuit sur [themoviedb.org](https://www.themoviedb.org/signup), puis copiez la **clé d'API** (v3, pas le jeton d'accès en lecture) depuis [Paramètres → API](https://www.themoviedb.org/settings/api).
2. **TRMNL (usetrmnl.com)** : créez un *Private Plugin* avec la stratégie *Polling*, puis copiez :
   - `polling_url` de `recipe/src/settings.yml` dans *Polling URL* (trois lignes, une URL par ligne) ;
   - les `custom_fields` de `recipe/src/settings.yml` dans *Custom fields* (YAML) ;
   - `recipe/src/shared.liquid` dans *Shared markup*, et chaque `recipe/src/<format>.liquid` dans l'onglet correspondant.

   **LaraPaper / BYOS** : *Plugins → Import recipe*, choisissez `recipe/showposter.zip` (créé par `recipe_render.sh`, ou zippez le contenu de `recipe/src/`).
3. Remplissez les réglages, à commencer par la clé d'API TMDB.

### Réglages

| Champ | Rôle |
|---|---|
| Clé d'API TMDB | Votre clé d'API TMDB gratuite (v3) |
| Pays | Les dates de sortie de ce pays |
| Affiche | *Rotation* (les deux rubriques), ou seulement *Prochainement*, ou seulement *À l'affiche* |
| Langue | Français ou anglais (titres, genres et dates) |
| Style | E-ink (noir et blanc) ou Couleur |

### Notes

- L'écran est construit en JavaScript au moment du rendu : le moteur de rendu doit exécuter le JavaScript.
- Les affiches sont chargées depuis TMDB au moment du rendu ; en e-ink, elles passent en niveaux de gris, tramés par le moteur de rendu.
- TMDB mêle aux prochaines sorties des reprises et des films déjà sortis : seules les dates à venir sont gardées, et *À l'affiche* ne garde que les films sortis depuis moins de trois mois.
- Le titre original n'est affiché que s'il diffère et s'écrit en alphabet latin.
- Dans LaraPaper, les formats demi et quart d'écran sortent blancs sur le modèle d'écran couleur (Echo Show), pour tous les plugins Poster : ils ne sont testés qu'en e-ink.
- Ce produit utilise l'API TMDB mais n'est ni approuvé ni certifié par TMDB.
