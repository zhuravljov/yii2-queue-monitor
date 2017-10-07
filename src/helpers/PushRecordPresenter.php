<?php
/**
 * Created by solly [07.10.17 21:46]
 */
declare(strict_types=1);

namespace zhuravljov\yii\queue\monitor\helpers;

use Yii;
use yii\helpers\VarDumper;
use function time;

class PushRecordPresenter
{
    
    /**
     * @var \zhuravljov\yii\queue\monitor\records\PushRecord
     */
    private $record;
    
    /**
     * @param \zhuravljov\yii\queue\monitor\records\PushRecord|\yii\db\ActiveRecord $record
     */
    public function __construct($record)
    {
        $this->record = $record;
    }
    
    public function pushedAt(): string
    {
        return Yii::$app->formatter->asDatetime($this->record->pushed_at);
    }
    
    public function attempts(): int
    {
        return $this->record->execCount['attempts'] ?: 0;
    }
    
    public function waitTimeTillExecute(): int
    {
        if ($this->record->firstExec) {
            return $this->record->firstExec->reserved_at - ($this->record->pushed_at + $this->record->delay);
        } else {
            return time() - ($this->record->pushed_at + $this->record->delay);
            //return 0;
        }
    }
    
    public function lastExecutionTime(): int
    {
        if ($this->record->lastExec && $this->record->lastExec->done_at) {
            return $this->record->lastExec->done_at - $this->record->lastExec->reserved_at;
        }
        return 0;
    }
    
    public function lastExecutionError(): string
    {
        if ($this->record->lastExec && !empty($this->record->lastExec->getErrorLine())) {
            return $this->record->lastExec->getErrorLine();
        } else {
            return '';
        }
    }
    
    public function jobAttributes(): array
    {
        return array_map(
            function ($prop) {
                return VarDumper::dumpAsString($prop);
            },
            get_object_vars($this->record->getJob())
        );
    }
    
    public function ttr(): int
    {
        return (int)$this->record->ttr;
    }
    
    public function delay(): int
    {
        return (int)$this->record->delay;
    }
    
    public function jobUid(): string
    {
        return $this->record->job_uid;
    }
    
    public function senderName(): string
    {
        return $this->record->sender_name;
    }
    
}
