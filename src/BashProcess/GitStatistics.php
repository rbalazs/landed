<?php


namespace App\BashProcess;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Responsible for providing git statistics.
 */
class GitStatistics
{
    /**
     * @param $repo
     * @return array
     */
    public function getCommitPerAuthors($repo): array
    {
        $commitsPerAuthor = [];

        $processOutput = $this->getProcessOutput('count_commits_per_authors.sh', $repo);

        foreach ($processOutput as $rowPart) {
            $rowPart = trim($rowPart);
            $parts = explode(' ', $rowPart);
            if (!empty($parts[0]) && !empty($parts[1])) {
                $commitsPerAuthor[$parts[1]] = (int)$parts[0];
            }
        }

        return $commitsPerAuthor;
    }

    /**
     * @param $repo
     * @return array
     */
    public function getCommitPerHour($repo): array
    {
        $commitsPerHour = [];

        $processOutput = $this->getProcessOutput('count_commits_per_hour.sh', $repo);

        foreach ($processOutput as $rowPart) {
            $rowPart = trim($rowPart);
            $parts = explode(' ', $rowPart);
            if (!empty($parts[0]) && !empty($parts[1])) {
                $key = $parts[1] . ':00:00 - ' . $parts[1] . ':59:59';
                $commitsPerHour[$key] = (int)$parts[0];
            }
        }

        return $commitsPerHour;
    }

    /**
     * @param $repo
     * @return array
     */
    public function getCommitPerWeekday($repo): array
    {
        $contribPerWeekday = [];

        $processOutput = $this->getProcessOutput('count_commits_per_weekday.sh', $repo);

        foreach ($processOutput as $rowPart) {
            $rowPart = trim($rowPart);
            $parts = explode(' ', $rowPart);
            if (!empty($parts[0]) && !empty($parts[1])) {
                $contribPerWeekday[$parts[1]] = (int)$parts[0];
            }
        }

        return $contribPerWeekday;
    }

    /**
     * @param $repo
     * @return array
     */
    public function getMostFrequentlyChangingFiles($repo): array
    {
        $mostFrequentlyChangedFiles = [];

        $processOutput = $this->getProcessOutput('most_frequently_changed_files.sh', $repo);

        foreach ($processOutput as $rowPart) {
            $rowPart = trim($rowPart);
            $parts = explode(' ', $rowPart);
            if (!empty($parts[0]) && !empty($parts[1])) {
                $mostFrequentlyChangedFiles[$parts[1]] = (int)$parts[0];
            }
        }

        return $mostFrequentlyChangedFiles;
    }

    public function listRepositories(): array
    {
        $processOutput = $this->getProcessOutput('list_repos.sh', null);

        $repositories = [];
        foreach ($processOutput as $rowPart) {
            $rowPart = trim($rowPart);
            if (!empty($rowPart)) {
                $repositories[] = $rowPart;
            }
        }

        return $repositories;
    }

    /**
     * @param $repo
     * @return array
     */
    public function getProcessOutput($cmd, $repo = null): array
    {
        if (is_null($repo)) {
            $process = new Process(['bash', $cmd], '/app/repos');
        } else {
            $process = new Process(['bash', $cmd, $repo], '/app/repos');
        }

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $stdOut = $process->getOutput();

        return explode("\n", $stdOut);
    }
}