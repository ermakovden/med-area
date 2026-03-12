<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['vendor', 'bootstrap/cache', 'storage'])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        '@PHP85Migration' => true,
    ])
    ->setFinder($finder)
;