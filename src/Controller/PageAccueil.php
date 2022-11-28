<?php

namespace App;

use Symfony\Component\HttpFoundation\Respponse;

class PageAccueil
{
    function bonjour(){
        return new Response('bonjour et bienvenue !');
    }
}