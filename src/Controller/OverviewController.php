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
        $process = new Process(['bash', 'list_repos.sh'], '/app/repos');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $stdOut = $process->getOutput();
        $explodedForm = explode("\n", $stdOut);

        $repositories = [];
        foreach ($explodedForm as $rowPart) {
            $rowPart = trim($rowPart);
            if (!empty($rowPart)) {
                $repositories[] = $rowPart;
            }
        }

        return $this->render('overview/index.html.twig', [
            'repositories' => $repositories,
        ]);
    }

    /**
     * @Route("/overview/{repo}", name="overview-per-repo")
     */
    public function perRepo($repo)
    {
        // Per weekday.
        $contribPerWeekday = [];
        $process = new Process(['bash', 'count.sh', $repo], '/app/repos');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $stdOut = $process->getOutput();
        $explodedForm = explode("\n", $stdOut);

        foreach ($explodedForm as $rowPart) {
            $rowPart = trim($rowPart);
            $parts = explode(' ', $rowPart);
            if (!empty($parts[0]) && !empty($parts[1])) {
                $contribPerWeekday[$parts[1]] = (int)$parts[0];
            }
        }

        // Per file.
        $mostFrequentlyChangedFiles = [];
        $process = new Process(['bash', 'most_frequently_changed_files.sh', $repo], '/app/repos');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $stdOut = $process->getOutput();
        $explodedForm = explode("\n", $stdOut);

        foreach ($explodedForm as $rowPart) {
            $rowPart = trim($rowPart);
            $parts = explode(' ', $rowPart);
            if (!empty($parts[0]) && !empty($parts[1])) {
                $mostFrequentlyChangedFiles[$parts[1]] = (int)$parts[0];
            }
        }

        // Per hour.
        $commitsPerHour = [];
        $process = new Process(['bash', 'count_commits_per_hour.sh', $repo], '/app/repos');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $stdOut = $process->getOutput();
        $explodedForm = explode("\n", $stdOut);

        foreach ($explodedForm as $rowPart) {
            $rowPart = trim($rowPart);
            $parts = explode(' ', $rowPart);
            if (!empty($parts[0]) && !empty($parts[1])) {
                $key = $parts[1] . ':00:00 - ' . $parts[1] . ':59:59';
                $commitsPerHour[$key] = (int)$parts[0];
            }
        }

        return $this->render('overview/repo.html.twig', [
            'contribPerWeekday' => $contribPerWeekday,
            'mostFrequentlyChangedFiles' => $mostFrequentlyChangedFiles,
            'commitsPerHour' => $commitsPerHour,
            'reponame' => $repo,
        ]);
    }
}
