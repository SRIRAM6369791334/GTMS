<?php
/**
 * Add shared lz-* classes next to a page's own prefixed classes, mark the
 * wrapper with .lz-page and link gtms-lease.css.  Markup only: <style> and
 * <script> blocks are not touched.
 *
 * Usage:
 *   php apply_lz.php <blade-file> --root=<wrapper-id> --prefix=<prefix> [--skip=a,b] [--dry]
 *
 *   --root    id of the page wrapper, e.g. gtms-step1-page or gtms-dgps-wizard-page
 *   --prefix  class prefix WITHOUT the dash, e.g. s1  (classes look like s1-main-card)
 *   --skip    component names to leave alone (custom widgets), e.g. input-group,input,select,addon
 *   --dry     print what would change, write nothing
 *
 * Always review `git diff` afterwards.
 */
// Parse --key=value / --flag in any order (getopt() stops at the file argument)
$opts = []; $file = null;
foreach (array_slice($argv, 1) as $a) {
    if (preg_match('/^--([\w-]+)(?:=(.*))?$/', $a, $m)) { $opts[$m[1]] = $m[2] ?? true; }
    elseif ($file === null) { $file = $a; }
}
if (!$file || !is_file($file) || empty($opts['root']) || empty($opts['prefix'])) {
    fwrite(STDERR, "usage: php apply_lz.php <blade-file> --root=<wrapper-id> --prefix=<prefix> [--skip=a,b] [--dry]\n");
    exit(1);
}
$root = $opts['root'];
$p = preg_quote($opts['prefix'], '/');
$skip = !empty($opts['skip']) ? explode(',', $opts['skip']) : [];
$dry = array_key_exists('dry', $opts);

// component suffix => lz classes (see references/design-system.md)
$map = [
    'main-card' => 'lz-card', 'header-block' => 'lz-head', 'eyebrow' => 'lz-eyebrow', 'time-tag' => 'lz-time',
    'title' => 'lz-title', 'subtitle' => 'lz-subtitle', 'section-card' => 'lz-section',
    'section-header' => 'lz-section-head', 'section-title' => 'lz-section-title', 'label' => 'lz-label',
    'opt-tag' => 'lz-opt', 'opt-badge' => 'lz-opt', 'hint' => 'lz-hint', 'field-hint' => 'lz-hint',
    'form-row' => 'lz-row', 'input-group' => 'lz-group', 'addon' => 'lz-addon', 'input' => 'lz-input',
    'textarea' => 'lz-textarea', 'select' => 'lz-select', 'notice-card' => 'lz-notice',
    'notice-icon' => 'lz-notice-icon', 'notice-text' => 'lz-notice-text', 'notice-desc' => 'lz-notice-text',
    'notice-title' => 'lz-notice-title', 'actions' => 'lz-actions',
    'btn-back' => 'lz-btn lz-btn-ghost', 'btn-cancel' => 'lz-btn lz-btn-ghost lz-btn-cancel',
    'btn-draft' => 'lz-btn lz-btn-outline', 'btn-submit' => 'lz-btn lz-btn-primary',
    'btn-continue' => 'lz-btn lz-btn-primary',
];
foreach ($skip as $s) unset($map[$s]);

$orig = file_get_contents($file);
$parts = preg_split('/(<style\b.*?<\/style>|<script\b.*?<\/script>)/s', $orig, -1, PREG_SPLIT_DELIM_CAPTURE);
if ($parts === false) { fwrite(STDERR, "split failed, nothing written\n"); exit(1); }
$added = 0; $log = [];
foreach ($parts as $k => $part) {
    if (preg_match('/^<(style|script)\b/', $part)) continue;
    $new = preg_replace_callback('/class=(["\'])(.*?)\1/s', function ($m) use ($map, $p, &$added, &$log) {
        $tokens = preg_split('/\s+/', trim($m[2]));
        $extra = [];
        foreach ($tokens as $t) {
            if (preg_match('/^' . $p . '-(.+)$/', $t, $pm) && isset($map[$pm[1]])) {
                foreach (explode(' ', $map[$pm[1]]) as $c) {
                    if (!in_array($c, $tokens, true) && !in_array($c, $extra, true)) $extra[] = $c;
                }
            }
        }
        if (!$extra) return $m[0];
        $added += count($extra);
        $log[] = trim($m[2]) . '  +' . implode(' ', $extra);
        return 'class=' . $m[1] . rtrim($m[2]) . ' ' . implode(' ', $extra) . $m[1];
    }, $part);
    if ($new === null) { fwrite(STDERR, "regex failed, nothing written\n"); exit(1); }
    $parts[$k] = $new;
}
$src = implode('', $parts);

// wrapper: add lz-page
$wrapCount = 0;
$src = preg_replace_callback('/<div id="' . preg_quote($root, '/') . '"(?: class="([^"]*)")?/', function ($m) use ($root) {
    $cls = $m[1] ?? '';
    if (str_contains($cls, 'lz-page')) return $m[0];
    return '<div id="' . $root . '" class="' . trim($cls . ' lz-page') . '"';
}, $src, 1, $wrapCount);

// stylesheet link (layouts.app pages only)
if (!str_contains($src, 'gtms-lease.css') && str_contains($src, "@section('main_content')")) {
    $src = preg_replace("/(@section\('main_content'\))/", "@push('styles')\n<link href=\"/css/gtms-lease.css?v=2\" rel=\"stylesheet\">\n@endpush\n\n$1", $src, 1);
}

if ($src === null || trim($src) === '') { fwrite(STDERR, "result empty, nothing written\n"); exit(1); }
echo basename($file) . ": +$added lz classes, wrapper=" . ($wrapCount ? 'yes' : 'NOT FOUND (check --root)') . ($dry ? ' [dry run]' : '') . "\n";
if ($dry) { echo implode("\n", array_slice($log, 0, 40)), "\n"; exit(0); }
file_put_contents($file, $src);
