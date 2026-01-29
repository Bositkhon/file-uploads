<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__) // Scans all files in the project root and subdirectories
    ->exclude('vendor') // Excludes the vendor directory
;

return (new Config())
    ->setRules([
        '@PSR12' => true, // Applies the PSR-12 coding standard rules
        'array_syntax' => ['syntax' => 'short'], // Enforces the use of short array syntax (e.g., []),
        'single_quote' => true
    ])
    ->setFinder($finder)
;
