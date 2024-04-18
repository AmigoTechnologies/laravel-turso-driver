<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Facades;

use Illuminate\Support\Facades\Facade;
use RichanFongdasen\Turso\TursoManager;

/**
 * @see \RichanFongdasen\Turso\TursoClient
 *
 * @mixin \RichanFongdasen\Turso\TursoManager
 * @mixin \RichanFongdasen\Turso\TursoClient
 */
class Turso extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TursoManager::class;
    }
}
