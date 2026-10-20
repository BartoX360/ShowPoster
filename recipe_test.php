<?php
use App\Models\{Plugin, Device, DeviceModel, User};
use App\Services\{PluginImportService, ImageGenerationService};
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

Plugin::where('name', 'ShowPoster (test)')->get()->each->delete();
$file = new UploadedFile('/tmp/showposter.zip', 'showposter.zip', 'application/zip', null, true);
$p = Plugin::find(app(PluginImportService::class)->importFromZip($file, User::find(1))->id);
// la clé TMDB (affiches) est reprise du plugin installé, pour ne pas l'écrire dans le dépôt
$tmdb = Plugin::where('name', 'ShowPoster')->first()?->configuration['tmdb_key'] ?? '';
$base = ['trakt_id' => 'test', 'origins' => ['us', 'gb', 'dk,se,no,fi,is']];
$p->update(['name' => 'ShowPoster (test)', 'configuration' => $base + ['style' => 'eink', 'language' => 'fr', 'featured' => 'rotate']]);
echo "url=", str_replace("\n", "\nurl=", $p->resolveLiquidVariables($p->polling_url)), "\n";
echo "header=", str_replace("\n", " | ", $p->resolveLiquidVariables($p->polling_header)), "\n";
echo 'tmdb key: ', $tmdb ? 'yes' : 'no', "\n";

// Premières Trakt figées du 20 octobre 2026 sur 33 jours, tous pays, et les séries les plus attendues
$feed = json_decode(file_get_contents('/tmp/test_payload.json'), true);
$err = ['IDX_0' => ['error' => 'Failed to fetch data'], 'IDX_1' => ['error' => 'Failed to fetch data']];
$empty = ['IDX_0' => ['data' => []], 'IDX_1' => ['data' => []]];
// heure UTC du 20/10 dont le créneau horaire vaut $k modulo 10 (rotation : rubrique = k % 2, rang = (k / 2) % 5)
$slot = fn($k) => (int) collect(range(0, 23))->first(fn($h) => intdiv(Carbon::create(2026, 10, 20, $h, 0, 0, 'UTC')->timestamp, 3600) % 10 == $k);
$cases = [
  // nom, taille, modèle, style, langue, affiche, données, heure (UTC), clé Trakt, clé TMDB
  ['fr_color_seasons', 'full', 55, 'color', 'fr', 'seasons', $feed, $slot(0), 'k', $tmdb],
  ['en_eink_seasons', 'full', 1, 'eink', 'en', 'seasons', $feed, $slot(1), 'k', $tmdb],
  ['fr_color_series', 'full', 55, 'color', 'fr', 'series', $feed, $slot(0), 'k', $tmdb],
  ['fr_eink_series', 'full', 1, 'eink', 'fr', 'series', $feed, $slot(1), 'k', $tmdb],
  ['en_x_seasons', 'full', 14, 'eink', 'en', 'seasons', $feed, $slot(2), 'k', $tmdb],
  ['fr_color_rotate_s3', 'full', 55, 'color', 'fr', 'rotate', $feed, $slot(4), 'k', $tmdb],
  ['en_color_rotate_n2', 'full', 55, 'color', 'en', 'rotate', $feed, $slot(3), 'k', $tmdb],
  ['fr_eink_notmdb', 'full', 1, 'eink', 'fr', 'seasons', $feed, $slot(0), 'k', ''],
  ['en_hh', 'half_horizontal', 1, 'eink', 'en', 'seasons', $feed, $slot(0), 'k', $tmdb],
  ['fr_hh', 'half_horizontal', 1, 'eink', 'fr', 'series', $feed, $slot(0), 'k', $tmdb],
  ['fr_hv', 'half_vertical', 1, 'eink', 'fr', 'seasons', $feed, $slot(3), 'k', $tmdb],
  ['en_q', 'quadrant', 1, 'eink', 'en', 'series', $feed, $slot(0), 'k', $tmdb],
  ['fr_q', 'quadrant', 1, 'eink', 'fr', 'seasons', $feed, $slot(0), 'k', $tmdb],
  ['fr_color_hh', 'half_horizontal', 55, 'color', 'fr', 'seasons', $feed, $slot(0), 'k', $tmdb],
  ['en_color_hv', 'half_vertical', 55, 'color', 'en', 'series', $feed, $slot(0), 'k', $tmdb],
  ['fr_color_q', 'quadrant', 55, 'color', 'fr', 'series', $feed, $slot(1), 'k', $tmdb],
  ['fr_error', 'full', 1, 'eink', 'fr', 'rotate', $err, 10, 'k', $tmdb],
  ['fr_nokey', 'full', 55, 'color', 'fr', 'rotate', $err, 10, '', $tmdb],
  ['en_empty', 'full', 55, 'color', 'en', 'rotate', $empty, 10, 'k', $tmdb],
];
@mkdir('/tmp/spr'); array_map('unlink', glob('/tmp/spr/*.png'));
foreach ($cases as [$name, $size, $mid, $style, $lang, $feat, $payload, $h, $key, $tk]) {
  Carbon::setTestNow(Carbon::create(2026, 10, 20, $h, 0, 0, 'UTC'));
  $p->configuration = ['trakt_id' => $key, 'tmdb_key' => $tk, 'style' => $style, 'language' => $lang, 'featured' => $feat];
  $p->data_payload = $payload; $p->saveQuietly();
  $model = DeviceModel::find($mid);
  $dev = new Device(['name' => 'test']); $dev->setRelation('deviceModel', $model);
  $html = $p->fresh()->render($size, true, $dev);
  $pal = $mid == 55 ? App\Models\DevicePalette::find(12) : null;
  $id = ImageGenerationService::generateImageFromModel(markup: $html, deviceModel: $model, palette: $pal);
  copy(storage_path("app/public/images/generated/$id.png"), "/tmp/spr/$name.png");
  echo "$name ok\n";
}
Carbon::setTestNow();
