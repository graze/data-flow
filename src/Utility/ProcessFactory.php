<?php

namespace Graze\DataFlow\Utility;

use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    /**
     * @param string         $commandline The command line to run
     * @param string|null    $cwd         The working directory or null to use the working dir of the current PHP
     *                                    process
     * @param array|null     $env         The environment variables or null to inherit
     * @param string|null    $input       The input
     * @param int|float|null $timeout     The timeout in seconds or null to disable
     * @param array          $options     An array of options for proc_open
     * @return Process
     * @throws RuntimeException When proc_open is not installed
     */
    public function createProcess(
        $commandline,
        $cwd = null,
        array $env = null,
        $input = null,
        $timeout = 60,
        array $options = array()
    ) {
        return new Process(
            $commandline,
            $cwd,
            $env,
            $input,
            $timeout,
            $options
        );
    }
}
