<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt']),
        ];
    }

    /**
     * Filtre pour tronquer une chaîne à un certain nombre de mots
     */
    public function excerpt(string $text, int $nbWords): string
    {

        $arrayText = explode(' ', $text, ($nbWords + 1));

        if( count($arrayText) > $nbWords ){
            array_pop($arrayText);
            return implode(' ', $arrayText) . '...';
        }

        return $text;

    }




}
