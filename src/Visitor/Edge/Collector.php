<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use Trismegiste\Mondrian\Visitor\VisitorGateway;
use Trismegiste\Mondrian\Transform\ReflectionContext;
use Trismegiste\Mondrian\Transform\GraphContext;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * Collector is the main visitor for creating edges between already-created vertices
 */
class Collector extends VisitorGateway
{

    public function __construct(ReflectionContext $ref, GraphContext $grf, Graph $g)
    {
        $visitor = [
            new \Trismegiste\Mondrian\Visitor\State\PackageLevel(),
            new FileLevel(),
            new ClassLevel(),
            new InterfaceLevel(),
            new TraitLevel(),
            new ClassMethodLevel(),
            new TraitMethodLevel()
        ];

        parent::__construct($visitor, $ref, $grf, $g);
    }

}