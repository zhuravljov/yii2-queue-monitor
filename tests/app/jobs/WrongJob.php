<?php

namespace tests\app\jobs;

use Exception;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Wrong Job
 */
class WrongJob implements JobInterface, RetryableJobInterface
{
    public function execute($queue)
    {
        throw new Exception('Test exception.');
    }

    public function getTtr()
    {
        return 10;
    }

    public function canRetry($attempt, $error)
    {
        return $attempt < 2;
    }
}
