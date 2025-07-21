<?php
namespace pms\facade;

use pms\program\cache\Driver;
use pms\Facade;

/**
 * @see Driver
 * @mixin Driver
 */
class Cache extends Facade
{
    protected static function getFacadeClass(): string
    {
        return Driver::class;
    }

}