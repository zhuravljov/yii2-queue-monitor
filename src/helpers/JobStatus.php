<?php
/**
 * Created by solly [07.10.17 22:20]
 */
declare(strict_types=1);

namespace zhuravljov\yii\queue\monitor\helpers;

class JobStatus
{
    
    const STATUS_STOPPED = 'stopped';
    const STATUS_WAITING = 'waiting';
    const STATUS_STARTED = 'started';
    const STATUS_DONE = 'done';
    const STATUS_FAILED = 'failed';
    const STATUS_RESTARTED = 'restarted';
    const STATUS_BURIED = 'buried';
    const STATUS_UNDEFINED = 'undefined';
    
    private $status;
    
    /**
     * @param bool $isStopped
     * @param null|\zhuravljov\yii\queue\monitor\helpers\ExecRecordPresenter     $lastExec
     */
    public function __construct(bool $isStopped, $lastExec)
    {
        $this->status = $this->resolveStatus($isStopped, $lastExec);
    }
    
    public function value(): string
    {
        return $this->status;
    }
    
    public function isWaiting(): bool
    {
        return $this->value() === self::STATUS_WAITING;
    }
    
    public function isStopped(): bool
    {
        return $this->value() === self::STATUS_STOPPED;
    }
    
    public function isRestarted(): bool
    {
        return $this->value() === self::STATUS_RESTARTED;
    }
    
    public function isStarted(): bool
    {
        return $this->value() === self::STATUS_STARTED;
    }
    
    public function isFailed(): bool
    {
        return $this->value() === self::STATUS_FAILED;
    }
    
    public function isDone(): bool
    {
        return $this->value() === self::STATUS_DONE;
    }
    
    public function isBuried(): bool
    {
        return $this->value() === self::STATUS_BURIED;
    }
    
    /**
     * @param bool                                                           $isStopped
     * @param null|\zhuravljov\yii\queue\monitor\helpers\ExecRecordPresenter $lastExec
     *
     * @return string
     */
    protected function resolveStatus(bool $isStopped, $lastExec): string
    {
        if ($isStopped) {
            return self::STATUS_STOPPED;
        }
        if (!$lastExec) {
            return self::STATUS_WAITING;
        }
        if (!$lastExec->isDone() && $lastExec->isFirstAttempt()) {
            return self::STATUS_STARTED;
        }
        if ($lastExec->isDone() && !$lastExec->hasExecutionError()) {
            return self::STATUS_DONE;
        }
        if ($lastExec->isDone() && $lastExec->shouldRetry()) {
            return self::STATUS_FAILED;
        }
        if (!$lastExec->isDone()) {
            return self::STATUS_RESTARTED;
        }
        if ($lastExec->isDone() && !$lastExec->shouldRetry()) {
            return self::STATUS_BURIED;
        }
        return self::STATUS_UNDEFINED;
    }
}
