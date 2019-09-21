<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class OverviewController extends AbstractController
{
    /**
     * @Route("/overview", name="overview")
     */
    public function index()
    {
        $process = new Process(['git', 'status'], '/app/repos');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();

        return $this->render('overview/index.html.twig', [
            'controller_name' => 'OverviewController',
        ]);
    }
}
