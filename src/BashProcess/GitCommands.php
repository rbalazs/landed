<?php

namespace App\BashProcess;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitCommands
{
    public function cloneRepository($url)
    {
        $process = new Process(['git', 'clone', $url], '/app/repos');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

    }
}