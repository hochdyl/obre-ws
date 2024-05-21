<?php

namespace App\Service;

use App\Exceptions\ObreatlasExceptions;
use Exception;
use Symfony\Component\String\Slugger\AsciiSlugger;

readonly class SluggerService
{
    /**
     * Verify slug validity
     *
     * @throws Exception
     */
    static function validateSlug(string $string, string $slug): bool
    {
        $string = strtolower($string);
        $slugger = new AsciiSlugger();
        $sluggedString = $slugger->slug($string)->toString();

        if ($sluggedString !== $slug) {
            throw new Exception(ObreatlasExceptions::SLUG_NOT_MATCH_TITLE);
        }

        return true;
    }
}