<?php

namespace App\Exceptions;

readonly final class ObreatlasExceptions
{
    const SLUG_NOT_MATCH_NAME = 'Slug does not match with name';
    const SLUG_NOT_MATCH_TITLE = 'Slug does not match with title';
    const PROTAGONIST_EXIST = 'Protagonist already exists';
    const GAME_NOT_FOUND = 'Game not found';
}