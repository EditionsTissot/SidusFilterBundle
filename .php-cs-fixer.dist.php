<?php

$config = new EditionsTissot\CS\Config\Config;
$config->getFinder()
    ->notPath('vendor')
    ->in([__DIR__])
;

return $config;
