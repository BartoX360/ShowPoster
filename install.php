<?php
// Importe la recette comme « ShowPoster » ; la clé d'API TMDB se renseigne ensuite dans les réglages du plugin
use App\Models\{Plugin, User};
use App\Services\PluginImportService;
use Illuminate\Http\UploadedFile;
Plugin::where('name', 'like', 'ShowPoster%')->get()->each->delete();
$file = new UploadedFile('/tmp/showposter.zip', 'showposter.zip', 'application/zip', null, true);
$p = Plugin::find(app(PluginImportService::class)->importFromZip($file, User::find(1))->id);
$p->name = 'ShowPoster';
$p->configuration = ['api_key' => '', 'featured' => 'rotate', 'style' => 'color', 'language' => 'fr'];
$p->save();
echo "installed id {$p->id}\n";
