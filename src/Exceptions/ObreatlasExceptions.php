<?php

namespace App\Exceptions;

readonly final class ObreatlasExceptions
{
    const SLUG_NOT_MATCH = 'Slug does not match';
    const GAME_EXIST = 'This game already exists';
    const CANT_VIEW_GAME = "You can't view this game";
    const CANT_EDIT_GAME = "You can't edit this game";
    const PROTAGONIST_EXIST = 'This protagonist already exists';
    const PROTAGONIST_NOT_EXIST = "This protagonist don't exists";
    const CANT_VIEW_PROTAGONIST = "You can't view this protagonist";
    const CANT_CHOOSE_PROTAGONIST = "You can't choose this protagonist";
    const CANT_EDIT_PROTAGONIST = "You can't edit this protagonist";

}