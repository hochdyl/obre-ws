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
        $slugged = $slugger->slug($string)->toString();

        if ($slugged !== $slug) {
            throw new Exception(ObreatlasExceptions::SLUG_NOT_MATCH_TITLE);
        }

        return true;
    }
}