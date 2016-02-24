<?php
/**
 * This file is part of graze/data-flow
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/data-flow/blob/master/LICENSE.md
 * @link    https://github.com/graze/data-flow
 */

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
