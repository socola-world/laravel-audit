<?php

namespace SocolaDaiCa\LaravelAudit\Invades;

class Invade
{
    public static function make(object $object)
    {
        return new Invader($object);
    }
}
