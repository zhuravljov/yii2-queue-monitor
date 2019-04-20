<?php

namespace tests\app\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Simple Job
 */
class SimpleJob extends BaseObject implements JobInterface
{
    /**
     * @var int number of seconds to execute the job.
     */
    public $timeout = 10;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        sleep($this->timeout);
    }
}
