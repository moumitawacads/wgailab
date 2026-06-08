<?php

namespace Ghanem\Zoom\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ghanem\Zoom\Zoom
 */
class Zoom extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'zoom';
    }
}
