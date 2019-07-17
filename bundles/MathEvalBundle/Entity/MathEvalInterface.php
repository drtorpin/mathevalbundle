<?php


namespace MathEvalBundle\Entity;


interface MathEvalInterface
{
    public function getCalculation($expression);
}