<?php
/**
 * Created by solly [07.10.17 21:46]
 */

namespace zhuravljov\yii\queue\monitor\helpers;

use Yii;
use zhuravljov\yii\queue\monitor\records\PushRecord;

class PushRecordPresenter
{
    const STATUS_STOPPED = 'stopped';
    const STATUS_WAITING = 'waiting';
    const STATUS_STARTED = 'started';
    const STATUS_DONE = 'done';
    const STATUS_FAILED = 'failed';
    const STATUS_RESTARTED = 'restarted';
    const STATUS_BURIED = 'buried';
    
    /**
     * @var \zhuravljov\yii\queue\monitor\records\PushRecord
     */
    private $record;
    
    public function __construct(PushRecord $record)
    {
        $this->record = $record;
    }
    
    public function status()
    {
        if ($this->record->isStopped()) {
            return self::STATUS_STOPPED;
        }
        if (!$this->record->lastExec) {
            return self::STATUS_WAITING;
        }
        if (!$this->record->lastExec->done_at && $this->record->lastExec->attempt == 1) {
            return self::STATUS_STARTED;
        }
        if ($this->record->lastExec->done_at && $this->record->lastExec->error === null) {
            return self::STATUS_DONE;
        }
        if ($this->record->lastExec->done_at && $this->record->lastExec->retry) {
            return self::STATUS_FAILED;
        }
        if (!$this->record->lastExec->done_at) {
            return self::STATUS_RESTARTED;
        }
        if ($this->record->lastExec->done_at && !$this->record->lastExec->retry) {
            return self::STATUS_BURIED;
        }
        return null;
    }
    
    public function pushedAt(): string
    {
        return Yii::$app->formatter->asDatetime($this->record->pushed_at);
    }
    
    public function attempts(): int
    {
        return $this->record->execCount['attempts'] ?: 0;
    }
    
    public function waitTimeTillExecute()
    {
        if ($this->record->firstExec) {
            return $this->record->firstExec->reserved_at - $this->record->pushed_at - $this->record->delay;
        } else {
            return 0;
        }
    }
    
    public function lastExecutionTime()
    {
        if ($this->record->lastExec && $this->record->lastExec->done_at) {
            return $this->record->lastExec->done_at - $this->record->lastExec->reserved_at;
        }
        return 0;
    }
    
    public function lastExecutionError()
    {
        if ($this->record->lastExec && $this->record->lastExec->getErrorLine() !== false) {
            return $this->record->lastExec->getErrorLine();
        } else {
            return '';
        }
    }
    
    public function jobAttributes()
    {
        return get_object_vars($this->record->getJob());
    }
}
