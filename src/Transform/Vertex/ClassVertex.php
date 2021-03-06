<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * ClassVertex is a vertex for a class
 */
class ClassVertex extends StaticAnalysis
{

    protected function getSpecific()
    {
        $default = array('shape' => 'circle', 'style' => 'filled',
            'color' => 'red', 'label' => $this->compactFqcn($this->name));

        return $default;
    }

}
