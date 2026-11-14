<?php
/**
 * Remove inline <style> rules that only style components now provided by a
 * shared stylesheet (gtms-lease.css), or that you are about to rewrite.
 * Page-unique rules are kept. Works through @media blocks.
 *
 * Usage:
 *   php strip_rules.php <blade-file> --root=<wrapper-id> --prefix=<prefix> [--components=a,b] [--skip=a,b] [--write] [--log]
 *
 *   Default (no --write) is a DRY RUN that prints what would be removed.
 *   --components  replace the default shared list (use when redesigning a
 *                 page's own widgets, e.g. kpi-card,kpi-label,feedback-box)
 *   --skip        remove names from the list (e.g. custom input groups)
 *
 * A selector is removed only when, after the wrapper (#root) and optional
 * dark-mode prefix, EVERY class in it is <prefix>-<component> from the list
 * (or the generic stepper classes). Selectors mixing in other classes stay.
 * Never writes an empty/failed result.
 */
// Parse --key=value / --flag in any order (getopt() stops at the file argument)
$opts = []; $file = null;
foreach (array_slice($argv, 1) as $a) {
    if (preg_match('/^--([\w-]+)(?:=(.*))?$/', $a, $m)) { $opts[$m[1]] = $m[2] ?? true; }
    elseif ($file === null) { $file = $a; }
}
if (!$file || !is_file($file) || empty($opts['root']) || empty($opts['prefix'])) {
    fwrite(STDERR, "usage: php strip_rules.php <blade-file> --root=<wrapper-id> --prefix=<prefix> [--components=a,b] [--skip=a,b] [--write] [--log]\n");
    exit(1);
}
$root = preg_quote($opts['root'], '/');
$prefix = preg_quote($opts['prefix'], '/');
$shared = ['main-card','header-block','eyebrow','time-tag','title','subtitle','section-card','section-header',
    'section-title','label','opt-tag','opt-badge','hint','field-hint','form-row','input-group','addon','input',
    'textarea','select','notice-card','notice-icon','notice-text','notice-title','notice-desc','actions',
    'btn-back','btn-cancel','btn-draft','btn-submit','btn-continue'];
if (!empty($opts['components'])) $shared = explode(',', $opts['components']);
if (!empty($opts['skip'])) $shared = array_values(array_diff($shared, explode(',', $opts['skip'])));
$generic = ['step-progress','sp-step','sp-label','circ','active','done','input-group-text','form-control','form-select','wizard-card'];
$write = array_key_exists('write', $opts);
$showLog = array_key_exists('log', $opts) || !$write;

$src = file_get_contents($file);
if (!preg_match_all('/<style\b[^>]*>(.*?)<\/style>/s', $src, $blocks, PREG_OFFSET_CAPTURE)) {
    fwrite(STDERR, "no <style> block\n"); exit(1);
}

$isShared = function (string $sel) use ($root, $prefix, $shared, $generic): bool {
    $s = trim(preg_replace('/^body\[data-theme-version=["\']dark["\']\]\s*/', '', trim($sel)));
    if (!preg_match('/^#' . $root . '(\.[\w-]+)*\b/', $s)) return false;
    $rest = preg_replace('/^#' . $root . '(\.[\w-]+)*/', '', $s);
    preg_match_all('/\.([a-zA-Z0-9_-]+)/', $rest, $cm);
    if (!$cm[1]) return false;
    $els = preg_replace('/\.[a-zA-Z0-9_-]+|::?[a-z-]+(\([^)]*\))?|\[[^\]]*\]|[>+~\s]/', ' ', $rest);
    foreach (preg_split('/\s+/', trim($els)) as $el) {
        if ($el !== '' && !in_array($el, ['i', 'svg', 'span'], true)) return false;
    }
    $hit = false;
    foreach ($cm[1] as $c) {
        if (preg_match('/^' . $prefix . '-(.+)$/', $c, $pm) && in_array($pm[1], $shared, true)) { $hit = true; continue; }
        if (in_array($c, $generic, true)) { if (in_array($c, ['step-progress', 'sp-step', 'sp-label', 'circ'], true)) $hit = true; continue; }
        return false;
    }
    return $hit;
};

$removed = 0; $kept = 0; $log = [];
$process = function (string $block) use (&$process, $isShared, &$removed, &$kept, &$log): string {
    $res = ''; $i = 0; $len = strlen($block);
    while ($i < $len) {
        if (substr($block, $i, 2) === '/*') { $e = strpos($block, '*/', $i + 2); $e = $e === false ? $len : $e + 2; $res .= substr($block, $i, $e - $i); $i = $e; continue; }
        $b = strpos($block, '{', $i);
        if ($b === false) { $res .= substr($block, $i); break; }
        $c = strpos($block, '/*', $i);
        if ($c !== false && $c < $b) { $res .= substr($block, $i, $c - $i); $i = $c; continue; }
        $prelude = substr($block, $i, $b - $i);
        $depth = 1; $j = $b + 1;
        while ($j < $len && $depth > 0) { if ($block[$j] === '{') $depth++; elseif ($block[$j] === '}') $depth--; $j++; }
        $body = substr($block, $b + 1, $j - $b - 2);
        $lead = substr($prelude, 0, strlen($prelude) - strlen(ltrim($prelude)));
        $pre = trim($prelude);
        if (str_starts_with($pre, '@media') || str_starts_with($pre, '@supports')) {
            $inner = $process($body);
            $res .= trim(preg_replace('/\/\*.*?\*\//s', '', $inner)) === '' ? $lead : $lead . $pre . ' {' . $inner . '}';
        } elseif (str_starts_with($pre, '@')) {
            $res .= $prelude . '{' . $body . '}';
        } else {
            $sels = array_map('trim', explode(',', $pre));
            $keep = array_values(array_filter($sels, fn($s) => !$isShared($s)));
            if (!$keep) { $removed++; $log[] = '- ' . $pre; $res .= $lead; }
            else {
                $kept++;
                if (count($keep) < count($sels)) $log[] = '~ ' . $pre . '  =>  ' . implode(', ', $keep);
                $res .= $lead . implode(",\n  ", $keep) . ' {' . $body . '}';
            }
        }
        $i = $j;
    }
    return $res;
};

// process every <style> block, last to first so offsets stay valid
$out = $src;
foreach (array_reverse($blocks[1]) as [$css, $offset]) {
    $new = preg_replace("/\n[ \t]*(\n[ \t]*){2,}/", "\n\n", $process($css));
    $out = substr($out, 0, $offset) . $new . substr($out, $offset + strlen($css));
}

echo basename($file) . ": " . ($write ? 'removed' : 'would remove') . " $removed rules, kept $kept\n";
if ($showLog) echo implode("\n", $log), "\n";
if ($write) {
    if (trim($out) === '' || strlen($out) < strlen($src) * 0.3) { fwrite(STDERR, "result suspiciously small, nothing written\n"); exit(1); }
    file_put_contents($file, $out);
}
