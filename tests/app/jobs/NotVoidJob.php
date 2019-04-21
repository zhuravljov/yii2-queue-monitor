<?php

namespace tests\app\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Not Void Job
 */
class NotVoidJob extends BaseObject implements JobInterface
{
    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        return [
            'a' => 1,
            'b' => 2,
        ];
    }
}
