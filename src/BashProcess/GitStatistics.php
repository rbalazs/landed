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
    public function getMasterMergesPerAuthors($repo): array
    {
        $result = [];

        $processOutput = $this->getProcessOutput('count_master_merge_commits_per_authors.sh', $repo);

        foreach ($processOutput as $rowPart) {
            $rowPart = trim($rowPart);
            $parts = explode(' ', $rowPart);
            if (!empty($parts[0]) && !empty($parts[1])) {
                $result[$parts[1]] = (int)$parts[0];
            }
        }

        return $result;
    }

    /**
     * @param $repo
     * @return array
     */
    public function getCommitPerHour($repo): array
    {
        $commitsPerHour = [];

        $processOutput = $this->getProcessOutput('count_commits_per_hour.sh', $repo);

        for ($i = 0; $i < 24; $i++) {
            $key = $i . ':00 - ' . ($i + 1) . ':00';
            $commitsPerHour[$key] = 0;
        }

        foreach ($processOutput as $rowPart) {

            for ($i = 0; $i < 24; $i++) {
                $key = $i . ':00 - ' . ($i + 1) . ':00';
                $rowPart = trim($rowPart);
                $parts = explode(' ', $rowPart);
                if (!empty($parts[0]) && !empty($parts[1]) && $i == $parts[1]) {
                    $commitsPerHour[$key] = (int)$parts[0];
                }
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