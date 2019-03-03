<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests')
;

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PSR2' => true,
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
