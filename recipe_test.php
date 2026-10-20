<?php
use App\Models\{Plugin, Device, DeviceModel, User};
use App\Services\{PluginImportService, ImageGenerationService};
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

Plugin::where('name', 'ShowPoster (test)')->get()->each->delete();
$file = new UploadedFile('/tmp/showposter.zip', 'showposter.zip', 'application/zip', null, true);
$p = Plugin::find(app(PluginImportService::class)->importFromZip($file, User::find(1))->id);
$base = ['api_key' => 'test', 'region' => 'FR'];
$p->update(['name' => 'ShowPoster (test)', 'configuration' => $base + ['style' => 'eink', 'language' => 'fr', 'featured' => 'rotate']]);
echo "url=", str_replace("\n", "\nurl=", $p->resolveLiquidVariables($p->polling_url)), "\n";

// Programme TMDB figé du 20 octobre 2026 (prochaines sorties p. 1 et 2, films à l'affiche)
$feed = json_decode(file_get_contents('/tmp/test_payload.json'), true);
$noimg = $feed; $noimg['IDX_0']['results'][0]['poster_path'] = null; $noimg['IDX_0']['results'][0]['backdrop_path'] = null;
$err = ['IDX_0' => ['error' => 'Failed to fetch data'], 'IDX_1' => ['error' => 'Failed to fetch data'], 'IDX_2' => ['error' => 'Failed to fetch data']];
$empty = ['IDX_0' => ['results' => []], 'IDX_1' => ['results' => []], 'IDX_2' => ['results' => []]];
// heure UTC du 20/10 dont le créneau horaire vaut $k modulo 10 (rotation : rubrique = k % 2, rang = (k / 2) % 5)
$slot = fn($k) => (int) collect(range(0, 23))->first(fn($h) => intdiv(Carbon::create(2026, 10, 20, $h, 0, 0, 'UTC')->timestamp, 3600) % 10 == $k);
// les formats réduits ne sont rendus qu'en e-ink : le mashup de l'écran couleur sort blanc dans LaraPaper
$cases = [
  // nom, taille, modèle, style, langue, affiche, données, heure (UTC), clé
  ['fr_color_upcoming', 'full', 55, 'color', 'fr', 'upcoming', $feed, $slot(0), 'k'],
  ['en_eink_upcoming', 'full', 1, 'eink', 'en', 'upcoming', $feed, $slot(0), 'k'],
  ['fr_color_now', 'full', 55, 'color', 'fr', 'now', $feed, $slot(0), 'k'],
  ['fr_eink_now', 'full', 1, 'eink', 'fr', 'now', $feed, $slot(1), 'k'],
  ['en_x_upcoming', 'full', 14, 'eink', 'en', 'upcoming', $feed, $slot(2), 'k'],
  ['fr_color_rotate_up3', 'full', 55, 'color', 'fr', 'rotate', $feed, $slot(4), 'k'],
  ['en_color_rotate_now2', 'full', 55, 'color', 'en', 'rotate', $feed, $slot(3), 'k'],
  ['fr_eink_noimg', 'full', 1, 'eink', 'fr', 'upcoming', $noimg, $slot(0), 'k'],
  ['en_hh', 'half_horizontal', 1, 'eink', 'en', 'upcoming', $feed, $slot(0), 'k'],
  ['fr_hh', 'half_horizontal', 1, 'eink', 'fr', 'now', $feed, $slot(0), 'k'],
  ['fr_hv', 'half_vertical', 1, 'eink', 'fr', 'upcoming', $feed, $slot(1), 'k'],
  ['en_q', 'quadrant', 1, 'eink', 'en', 'upcoming', $feed, $slot(0), 'k'],
  ['fr_q', 'quadrant', 1, 'eink', 'fr', 'now', $feed, $slot(0), 'k'],
  ['fr_error', 'full', 1, 'eink', 'fr', 'rotate', $err, 10, 'k'],
  ['fr_nokey', 'full', 55, 'color', 'fr', 'rotate', $err, 10, ''],
  ['en_empty', 'full', 55, 'color', 'en', 'rotate', $empty, 10, 'k'],
];
@mkdir('/tmp/spr'); array_map('unlink', glob('/tmp/spr/*.png'));
foreach ($cases as [$name, $size, $mid, $style, $lang, $feat, $payload, $h, $key]) {
  Carbon::setTestNow(Carbon::create(2026, 10, 20, $h, 0, 0, 'UTC'));
  $p->configuration = ['api_key' => $key, 'region' => 'FR', 'style' => $style, 'language' => $lang, 'featured' => $feat];
  $p->data_payload = $payload; $p->saveQuietly();
  $model = DeviceModel::find($mid);
  $dev = new Device(['name' => 'test']); $dev->setRelation('deviceModel', $model);
  $html = $p->fresh()->render($size, true, $dev);
  if ($name == 'fr_color_upcoming') file_put_contents('/tmp/spr/sample.html', $html);
  $pal = $mid == 55 ? App\Models\DevicePalette::find(12) : null;
  $id = ImageGenerationService::generateImageFromModel(markup: $html, deviceModel: $model, palette: $pal);
  copy(storage_path("app/public/images/generated/$id.png"), "/tmp/spr/$name.png");
  echo "$name ok\n";
}
Carbon::setTestNow();
