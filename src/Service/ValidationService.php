<?php

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationList;

readonly class ValidationService
{
    /**
     * Transform a violation list into an associative array
     *
     * @param ConstraintViolationList $violations
     * @return array
     */
    static function getViolations(ConstraintViolationList $violations): array
    {
        $data = [];

        for ($i = 0; $i < $violations->count(); $i++) {
            $violation = $violations->get($i);
            $data[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $data;
    }
}