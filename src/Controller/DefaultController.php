<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MathEvalBundle\MathEvalBundle;

class DefaultController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $mathEval = new MathEvalBundle();
        return new Response($mathEval->do('123+345-'));
    }
}