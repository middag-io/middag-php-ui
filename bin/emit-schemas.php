#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * middag-io/ui — JSON Schema emitter.
 *
 * Writes the wire-contract JSON Schemas (page-contract.json, fragment.json) from
 * the PHP value objects' hand-authored jsonSchema() into ./schema/. The PHP VOs
 * are the single source of truth (D-02:A); @middag-io/react reads these to codegen
 * its TypeScript types + zod schema, and the MCP server bundles them.
 *
 * Usage:
 *   php bin/emit-schemas.php           # write schema/*.json
 *   php bin/emit-schemas.php --check   # exit 1 if committed schema/*.json is stale
 */

namespace Middag\Ui\Bin;

use Middag\Ui\Schema\SchemaRegistry;

$autoload = __DIR__ . '/../vendor/autoload.php';
if (! is_file($autoload)) {
    fwrite(STDERR, "Run `composer install` first (vendor/autoload.php missing).\n");
    exit(2);
}
require $autoload;

$check = in_array('--check', $argv, true);
$outDir = __DIR__ . '/../schema';

if (! $check && ! is_dir($outDir) && ! mkdir($outDir, 0o775, true) && ! is_dir($outDir)) {
    fwrite(STDERR, "Cannot create output dir: {$outDir}\n");
    exit(2);
}

$exit = 0;

foreach (SchemaRegistry::bundles() as $file => $bundle) {
    $json = json_encode($bundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    $path = $outDir . '/' . $file;

    if ($check) {
        $current = is_file($path) ? file_get_contents($path) : '';
        if ($current === $json) {
            fwrite(STDOUT, "ok: {$file} up to date\n");

            continue;
        }
        fwrite(STDERR, "DRIFT: {$file} is stale — run `composer emit:schemas` and commit.\n");
        $exit = 1;

        continue;
    }

    file_put_contents($path, $json);
    fwrite(STDOUT, "wrote: schema/{$file}\n");
}

exit($exit);
