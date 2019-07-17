<?php

namespace MathEvalBundle;
use \Symfony\Component\HttpKernel\Bundle\Bundle;
use MathEvalBundle\DependencyInjection\UnconventionalExtensionClass;
use MathEvalBundle\Entity\MathEvalCalculation;

class MathEvalBundle extends Bundle
{
    public function do($string)
    {
        $mathEval = new MathEvalCalculation($string);
        return $mathEval->getCalculation($string);
    }

}