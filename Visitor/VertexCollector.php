<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\Vertex;

/**
 * VertexCollector is a visitor to transform code into graph vertices
 */
class VertexCollector extends PassCollector
{

    /**
     * {@inheritDoc}
     */
    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        switch ($node->getType()) {

            case 'Stmt_Class' :
                $this->currentClass = (string) $node->namespacedName;
                $this->pushClass($node);
                break;

            case 'Stmt_Interface' :
                $this->currentClass = (string) $node->namespacedName;
                $this->pushInterface($node);
                break;

            case 'Stmt_ClassMethod' :
                if ($node->isPublic()) {
                    $this->currentMethod = $node->name;
                    // only if this method is first declared in this class
                    $declaringClass = $this->getDeclaringClass($this->currentClass, $this->currentMethod);
                    // we add the vertex. If not, it will be a higher class/interface
                    // in the inheritance hierarchy which add it.
                    if ($this->currentClass == $declaringClass) {
                        $this->pushMethod($node);
                    }
                    // if not abstract we add the vertex for the implementation
                    if (!$this->isInterface($this->currentClass) && !$node->isAbstract()) {
                        $this->pushImplementation($node);
                    }
                }
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function leaveNode(\PHPParser_Node $node)
    {
        if ($node->getType() == 'Stmt_Class') {
            $this->currentClass = false;
        }
        if ($node->getType() == 'Stmt_ClassMethod') {
            $this->currentMethod = false;
        }
    }

    /**
     * add a new ClassVertex with the class node
     *
     * @param \PHPParser_Node_Stmt_Class $node
     */
    protected function pushClass(\PHPParser_Node_Stmt_Class $node)
    {
        $index = (string) $node->namespacedName;
        if (!$this->existsVertex('class', $index)) {
            $v = new Vertex\ClassVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('class', $index, $v);
        }
    }

    /**
     * Adding a new vertex if the index is not already indexed
     * 
     * @param \PHPParser_Node_Stmt_Interface $node 
     */
    protected function pushInterface(\PHPParser_Node_Stmt_Interface $node)
    {
        $index = (string) $node->namespacedName;
        if (!$this->existsVertex('interface', $index)) {
            $v = new Vertex\InterfaceVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('interface', $index, $v);
        }
    }

    /**
     * Adding a new vertex if the index is not already indexed
     * Since it is a method, I'm also adding the parameters
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node 
     */
    protected function pushMethod(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $index = $this->getCurrentMethodIndex();
        if (!$this->existsVertex('method', $index)) {
            $v = new Vertex\MethodVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('method', $index, $v);
            foreach ($node->params as $order => $aParam) {
                $this->pushParameter($index, $order);
            }
        }
    }

    /**
     * Adding a new vertex if the index is not already indexed
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node 
     */
    protected function pushImplementation(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $index = $this->getCurrentMethodIndex();
        if (!$this->existsVertex('impl', $index)) {
            $v = new Vertex\ImplVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('impl', $index, $v);
        }
    }

    /**
     * Add a parameter vertex. I must point out that I storre the order 
     * of the parameter, not its name. Why ? Because, name can change accross
     * inheritance tree. Therefore, it could fail the refactoring of the source
     * from the digraph.
     * 
     * @param string $methodName like 'FQCN::method'
     * @param int $order 
     */
    protected function pushParameter($methodName, $order)
    {
        $index = $methodName . '/' . $order;
        if (!$this->existsVertex('param', $index)) {
            $v = new Vertex\ParamVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('param', $index, $v);
        }
    }

}