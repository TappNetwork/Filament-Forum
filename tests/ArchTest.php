<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch()->preset()->php()
    ->group('local');

arch()->preset()->security()
    ->group('local');

arch()->preset()->laravel()
    ->group('local');
