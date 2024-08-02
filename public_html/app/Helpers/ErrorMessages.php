<?php

namespace App\Helpers;

class ErrorMessages
{
    public static function getErrorMessage($entity, $status)
    {
        return trans("errors.{$status}", ['entity' => $entity]) ?? trans('errors.default');
    }
}