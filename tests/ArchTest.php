<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('it will pass the php preset')->preset()->php()
    ->group('local');

arch('it will pass the security preset')->preset()->security()
    ->group('local');

arch('it will pass the laravel preset')->preset()->laravel()
    ->group('local');
