<?php
/**
 * Created by solly [07.10.17 22:20]
 */

namespace zhuravljov\yii\queue\monitor\helpers;

use zhuravljov\yii\queue\monitor\records\ExecRecord;

class JobStatus
{
    public function __construct(bool $isStopped, ?ExecRecord $lastExec)
    {
    
    }
}
