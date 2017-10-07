<?php
/**
 * Created by solly [08.10.17 0:01]
 */
declare(strict_types=1);

namespace zhuravljov\yii\queue\monitor\helpers;

class ExecRecordPresenter
{
    
    /**
     * @var \zhuravljov\yii\queue\monitor\records\ExecRecord
     */
    private $record;
    
    /**
     * @param \zhuravljov\yii\queue\monitor\records\ExecRecord|\yii\db\ActiveRecord $record
     */
    public function __construct($record)
    {
        $this->record = $record;
    }
    
    
    public function isDone(): bool
    {
        return !is_null($this->record->done_at);
    }
    
    public function isFirstAttempt(): bool
    {
        return $this->record->attempt === 1;
    }
    
    public function hasExecutionError(): bool
    {
        return !is_null($this->record->error);
    }
    
    public function shouldRetry(): bool
    {
        return (bool)$this->record->retry;
    }
    
    public function executionTime():int
    {
        return $this->isDone() ? $this->record->done_at - $this->record->reserved_at : 0;
    }
}
