<?php
/**
 * Survey a GTMS Blade page before upgrading it. READ-ONLY.
 *
 * Usage:  php survey.php <blade-file> [more files...]
 *
 * Reports: page wrapper ids, <style>/<script> blocks, class prefixes and the
 * components under each prefix (to map onto lz-* classes), and known pitfalls
 * that were found and fixed in the lease module (see references/lessons.md).
 */
if ($argc < 2) { fwrite(STDERR, "usage: php survey.php <blade-file> [...]\n"); exit(1); }

$lzComponents = ['main-card','header-block','eyebrow','time-tag','title','subtitle','section-card','section-header',
    'section-title','label','opt-tag','opt-badge','hint','field-hint','form-row','input-group','addon','input',
    'textarea','select','notice-card','notice-icon','notice-text','notice-title','notice-desc','actions',
    'btn-back','btn-cancel','btn-draft','btn-submit','btn-continue'];

foreach (array_slice($argv, 1) as $file) {
    if (!is_file($file)) { echo "!! not found: $file\n"; continue; }
    $src = file_get_contents($file);
    $lines = substr_count($src, "\n") + 1;
    echo str_repeat('=', 78), "\n$file  ($lines lines)\n", str_repeat('=', 78), "\n";

    // Layout + wrappers
    preg_match('/@extends\(\'([^\']+)\'\)/', $src, $ext);
    echo "extends     : " . ($ext[1] ?? 'STANDALONE (no layout: global CSS/JS not loaded)') . "\n";
    preg_match_all('/id="([a-z0-9_-]*-page)"/i', $src, $ids);
    echo "wrapper ids : " . (implode(', ', array_unique($ids[1])) ?: 'none (add one, e.g. id="gtms-<module>-page")') . "\n";
    echo "lz-page     : " . (str_contains($src, 'lz-page') ? 'yes' : 'no') . "\n";
    echo "lease css   : " . (str_contains($src, 'gtms-lease.css') ? 'linked' : 'not linked') . "\n";

    // Blocks
    preg_match_all('/<style\b[^>]*>(.*?)<\/style>/s', $src, $styles);
    $cssLines = array_sum(array_map(fn($c) => substr_count($c, "\n"), $styles[1]));
    $important = array_sum(array_map(fn($c) => substr_count($c, '!important'), $styles[1]));
    echo "style blocks: " . count($styles[0]) . " ($cssLines lines, $important !important)\n";
    echo "script tags : " . preg_match_all('/<script\b/', $src) . "\n";

    // Prefix -> components (markup only)
    $markup = preg_replace('/<style\b.*?<\/style>|<script\b.*?<\/script>/s', '', $src);
    preg_match_all('/class=(["\'])(.*?)\1/s', $markup, $cm);
    $byPrefix = [];
    foreach ($cm[2] as $cls) {
        foreach (preg_split('/\s+/', trim($cls)) as $t) {
            if (preg_match('/^([a-z][a-z0-9]{0,5})-([a-z][a-z0-9-]+)$/', $t, $m)) {
                $byPrefix[$m[1]][$m[2]] = ($byPrefix[$m[1]][$m[2]] ?? 0) + 1;
            }
        }
    }
    $ignore = ['bi','fa','fas','far','btn','bg','text','border','col','row','d','me','ms','mb','mt','mx','my','p','px','py','pt','pb','g','gap','fw','fs','w','h','align','justify','flex','position','rounded','shadow','form','input','table','badge','alert','modal','nav','dropdown','list','card','lz','gt','sp','opacity','overflow','order','float','lh','user','visually','spinner','progress','accordion','was','is','has','tab','start','end','top','bottom','z','vstack','hstack','ratio','font','link','icon','dlab','owl','dz','wizard','step'];
    uksort($byPrefix, fn($a, $b) => array_sum($byPrefix[$b]) <=> array_sum($byPrefix[$a]));
    echo "prefixes    :\n";
    $shown = 0;
    foreach ($byPrefix as $prefix => $comps) {
        if (in_array($prefix, $ignore, true) || array_sum($comps) < 3) continue;
        $match = array_values(array_intersect(array_keys($comps), $lzComponents));
        printf("  %-6s %3d uses, %2d components; maps to lz-*: %s\n", $prefix . '-', array_sum($comps), count($comps),
            $match ? implode(', ', $match) : '-');
        if (++$shown >= 8) break;
    }
    echo "stepper     : " . (preg_match('/step-progress|sp-step/', $markup) ? 'yes (.step-progress / .sp-step)' : 'no') . "\n";

    // Pitfalls found in the lease module
    $checks = [
        'half-step bootstrap classes (now defined in gtms-ui.css)' => preg_match_all('/\b(?:m|p)[tbsexy]?-[0-9]\.5\b|\bgap-[0-9]\.5\b/', $markup),
        'return confirm(...) handlers (auto-converted to SweetAlert)' => preg_match_all('/return\s+confirm\(/', $src),
        'native confirm()/alert() inside scripts (convert to GTMS.confirm / toast)' => max(0, preg_match_all('/(?<![.\w$])(?:confirm|alert)\s*\(/', $src) - preg_match_all('/return\s+confirm\(/', $src)),
        'Swal.fire calls (prefer GTMS.toast / GTMS.confirm, errors under fields)' => preg_match_all('/Swal\.fire\(/', $src),
        'toastr calls (prefer GTMS.toast)' => preg_match_all('/toastr\.(?:success|error|info|warning)\(/', $src),
        'maxlength on inputs (cuts pasted text before filtering; use data-v)' => preg_match_all('/<input[^>]*maxlength=/', $markup),
        'required fields in a form without novalidate (native bubbles)' => (preg_match('/<form(?![^>]*novalidate)[^>]*>/', $markup) && preg_match('/\brequired\b/', $markup)) ? 1 : 0,
        'text-secondary / btn-outline-secondary (theme secondary = PINK)' => preg_match_all('/\b(?:text-secondary|btn-outline-secondary|bg-secondary)\b/', $markup),
        'text-primary on eyebrow/labels (forces navy, fights lz orange)' => preg_match_all('/class="[^"]*eyebrow[^"]*text-primary|class="[^"]*text-primary[^"]*eyebrow/', $markup),
        'inputs with data-v validation' => preg_match_all('/data-v="/', $markup),
        'fake money fallbacks (?? 50000 / : 150000)' => preg_match_all('/\?\?\s*50000|:\s*150000(?:\.00)?|\?\?\s*\'PANNA0000P\'/', $src),
        "payment_status checks (add 'free' handling if module gets waivers)" => preg_match_all('/payment_status/', $src),
        'fixed heights on buttons beside inputs (height: 40px vs 44px inputs)' => preg_match_all('/height:\s*(?:38|40)px/', implode("\n", $styles[1])),
        'text-truncate on values (hides data; prefer wrap)' => preg_match_all('/class="[^"]*\btext-truncate\b/', $markup),
    ];
    echo "pitfalls    :\n";
    foreach ($checks as $label => $count) {
        if ($count > 0) printf("  [%3d] %s\n", $count, $label);
    }
    echo "\n";
}
