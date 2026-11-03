<?php

/**
 * Génère les fiches PDF (apprenant et tuteur) à partir des fichiers Markdown.
 *
 * Usage : php docs/fiches/build.php
 * Nécessite Google Chrome (impression PDF en mode headless).
 */

use Illuminate\Support\Str;
use League\CommonMark\GithubFlavoredMarkdownConverter;

require __DIR__.'/../../vendor/autoload.php';

const CHROME = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

$publics = [
    'apprenant' => ['libelle' => 'Fiche apprenant', 'livret' => 'Livret de l\'apprenant', 'couleur' => '#0d6efd'],
    'tuteur' => ['libelle' => 'Fiche tuteur', 'livret' => 'Livret du tuteur', 'couleur' => '#b4530a'],
];

$convertisseur = new GithubFlavoredMarkdownConverter(['html_input' => 'allow']);
$temp = sys_get_temp_dir().'/fiches-'.getmypid();
@mkdir($temp, 0777, true);

foreach ($publics as $public => $config) {
    $sortie = __DIR__."/pdf/{$public}";
    @mkdir($sortie, 0777, true);

    $intro = lireFiche(__DIR__."/{$public}/introduction.md", $convertisseur);
    $semaines = array_map(
        fn (string $fichier) => lireFiche($fichier, $convertisseur),
        glob(__DIR__."/{$public}/semaine-*.md")
    );

    foreach ($semaines as $fiche) {
        $nom = sprintf('Semaine-%02d-%s.pdf', $fiche['meta']['semaine'], slug($fiche['meta']['titre']));
        imprimer(page($config, [blocSemaine($config, $fiche)]), "{$sortie}/{$nom}", $temp);
        echo "✓ {$public}/{$nom}\n";
    }

    $livret = [couverture($config, $semaines), blocIntroduction($config, $intro)];
    foreach ($semaines as $fiche) {
        $livret[] = blocSemaine($config, $fiche);
    }
    $nomLivret = $public === 'apprenant' ? '00-Livret-apprenant-complet.pdf' : '00-Livret-tuteur-complet.pdf';
    imprimer(page($config, $livret), "{$sortie}/{$nomLivret}", $temp);
    echo "✓ {$public}/{$nomLivret}\n";
}

array_map('unlink', glob("{$temp}/*.html"));

/**
 * @return array{meta: array<string, string>, html: string}
 */
function lireFiche(string $fichier, GithubFlavoredMarkdownConverter $convertisseur): array
{
    $contenu = file_get_contents($fichier);
    $meta = [];

    if (preg_match('/\A---\n(.*?)\n---\n/s', $contenu, $m)) {
        foreach (explode("\n", $m[1]) as $ligne) {
            [$cle, $valeur] = array_map('trim', explode(':', $ligne, 2));
            $meta[$cle] = $valeur;
        }
        $contenu = substr($contenu, strlen($m[0]));
    }

    return ['meta' => $meta, 'html' => (string) $convertisseur->convert($contenu)];
}

function blocSemaine(array $config, array $fiche): string
{
    $m = $fiche['meta'];
    $competences = implode('', array_map(
        fn (string $c) => '<span class="tag">'.htmlspecialchars(trim($c)).'</span>',
        explode('·', $m['competences'] ?? '')
    ));

    return <<<HTML
    <section class="fiche">
        <header class="bandeau">
            <div class="public">{$config['libelle']} · Projet Suivi d'incidents backbone</div>
            <div class="numero">Semaine {$m['semaine']}</div>
            <h1>{$m['titre']}</h1>
            <div class="meta"><span>Durée : {$m['duree']}</span>{$competences}</div>
        </header>
        <div class="contenu">{$fiche['html']}</div>
    </section>
    HTML;
}

function blocIntroduction(array $config, array $intro): string
{
    return <<<HTML
    <section class="fiche">
        <header class="bandeau">
            <div class="public">{$config['livret']}</div>
            <h1>{$intro['meta']['titre']}</h1>
        </header>
        <div class="contenu">{$intro['html']}</div>
    </section>
    HTML;
}

function couverture(array $config, array $semaines): string
{
    $lignes = implode('', array_map(
        fn (array $f) => "<li><span>Semaine {$f['meta']['semaine']}</span>{$f['meta']['titre']}</li>",
        $semaines
    ));

    return <<<HTML
    <section class="couverture">
        <div class="public">{$config['livret']}</div>
        <h1>Suivi d'incidents<br>sur un réseau backbone</h1>
        <p class="sous-titre">Apprendre Laravel, MySQL et le développement web<br>en construisant un projet concret, en 8 semaines.</p>
        <ol class="sommaire">{$lignes}</ol>
        <p class="pile">Laravel · Blade · Bootstrap 5 · MySQL</p>
    </section>
    HTML;
}

function page(array $config, array $blocs): string
{
    $corps = implode("\n", $blocs);
    $css = str_replace('--accent-valeur', $config['couleur'], file_get_contents(__DIR__.'/style.css'));

    return "<!doctype html><html lang=\"fr\"><head><meta charset=\"utf-8\"><style>{$css}</style></head><body>{$corps}</body></html>";
}

function imprimer(string $html, string $pdf, string $temp): void
{
    $source = $temp.'/'.md5($pdf).'.html';
    file_put_contents($source, $html);

    $commande = sprintf(
        '%s --headless=new --disable-gpu --no-pdf-header-footer --user-data-dir=%s --print-to-pdf=%s %s 2>/dev/null',
        escapeshellarg(CHROME),
        escapeshellarg($temp.'/chrome'),
        escapeshellarg($pdf),
        escapeshellarg('file://'.$source)
    );
    exec($commande, $sortie, $code);

    if ($code !== 0 || ! is_file($pdf)) {
        fwrite(STDERR, "Échec de la génération de {$pdf}\n");
        exit(1);
    }
}

function slug(string $texte): string
{
    return Str::slug($texte);
}
