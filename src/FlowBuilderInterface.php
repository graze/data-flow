<?php

namespace Graze\DataFlow;

interface FlowBuilderInterface
{
    /**
     * Add a namespace to check for flow names within
     *
     * @param string $namespace
     *
     * @return void
     */
    public function addNamespace($namespace);

    /**
     * Return a list of namespaces to search for flows in
     *
     * @return string[]
     */
    public function getNamespaces();

    /**
     * Create a new Flow based on a name
     *
     * @param string $flowName
     * @param array  $arguments
     *
     * @return FlowInterface
     */
    public function buildFlow($flowName, array $arguments = []);
}
