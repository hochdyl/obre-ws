<?php

namespace App\Exceptions;

readonly final class ObreatlasExceptions
{
    // Slug
    const SLUG_NOT_MATCH = 'Slug does not match';

    // Game
    const GAME_EXIST = 'This game already exists';
    const CANT_VIEW_GAME = 'You can\'t view this game';
    const NOT_GAME_MASTER = 'You are not the game master';

    // Protagonist
    const PROTAGONIST_EXIST = 'This protagonist already exists';
    const PROTAGONIST_NOT_FOUND = 'This protagonist was not found';
    const CANT_VIEW_PROTAGONIST = 'You can\'t view this protagonist';
    const CANT_CHOOSE_PROTAGONIST = 'You can\'t choose this protagonist';

    // Metric
    const METRIC_NOT_FOUND = 'This metric was not found';
}