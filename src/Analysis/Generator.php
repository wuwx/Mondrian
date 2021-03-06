<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\Graph;

/**
 * Contract for a factory of a new reduced Graph (from a bigger graph)
 */
interface Generator
{

    /**
     * Creates a new reduced graph from this Graph
     *
     * @return Graph
     */
    public function createReducedGraph();
}
