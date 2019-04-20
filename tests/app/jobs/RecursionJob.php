<?php

namespace tests\app\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Recursion Job
 */
class RecursionJob extends BaseObject implements JobInterface
{
    /**
     * @var int
     */
    public $iterations = 3;
    /**
     * @var int number of seconds to execute the job.
     */
    public $timeout = 10;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        if (--$this->iterations > 0) {
            $queue->push($this);
        }
        sleep($this->timeout);
    }
}
