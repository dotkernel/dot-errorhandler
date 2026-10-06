<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('ReplacementStrategy', 'src/Extra/ReplacementStrategy.php')
    ->layer('Processor', 'src/Extra/Processor')
    ->layer('Provider', 'src/Extra/Provider')
    ->layer('Extra', 'src/Extra', [
        'src/Extra/ReplacementStrategy.php',
        'src/Extra/Processor',
        'src/Extra/Provider',
    ])
    ->layer('ErrorHandler', 'src', 'src/Extra')
    ->ruleset([
        'ReplacementStrategy' => [],
        'Processor'           => ['ReplacementStrategy'],
        'Provider'            => ['+Processor'],
        'Extra'               => ['+Provider'],
        'ErrorHandler'        => ['+Extra'],
    ]);
