<?php
/**
 * Created by solly [07.10.17 22:09]
 */

namespace zhuravljov\yii\queue\monitor\helpers;

use zhuravljov\yii\queue\monitor\records\ExecRecord;

class ExecRecordPresenter
{
    /**
     * @var \zhuravljov\yii\queue\monitor\records\ExecRecord
     */
    private $record;
    
    public function __construct(ExecRecord $record)
    {
        $this->record = $record;
    }
}
