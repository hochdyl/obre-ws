<?php

namespace App\Service;

use Symfony\Component\String\AbstractUnicodeString;
use Symfony\Component\String\Slugger\AsciiSlugger;

readonly class SluggerService
{
    /**
     * Create a slug based on a string
     *
     * @param string $string
     * @return AbstractUnicodeString
     */
    static function getSlug(string $string): string
    {
        $slugger = new AsciiSlugger();
        return $slugger->slug($string)->toString();
    }
}