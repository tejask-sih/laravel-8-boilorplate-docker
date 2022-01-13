<?php

namespace App\Contracts;

interface LedgerIdResolver
{
    /**
     * Resolve the IP Address.
     *
     * @return string
     */
    public static function resolve();
}
