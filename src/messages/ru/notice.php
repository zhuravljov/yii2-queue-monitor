<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

return [
    'Push is forbidden.' => 'Перезапуск задачи запрещен',
    'Record not found.' => 'Запись не найдена',
    'Stop is forbidden.' => 'Прерывание задачи запрещено',
    'The job is already done.' => 'Задача уже выполнена',
    'The job is already stopped.' => 'Задача уже остановлена',
    'The job is pushed again.' => 'Задача отправлена в очередь повторно',
    'The job isn\'t pushed because it must be JobInterface instance.' => 'Задача не может быть добавлена, задача должна реализовывать интерфейс JobInterface ',
    'The job isn\'t pushed because {sender} component isn\'t found.' => 'Задача не может быть добавлена, компонент - отправитель не найден',
    'The job will be stopped.' => 'Задача будет остановлена',
    'The worker will be stopped within {timeout} sec.' => 'Исполнитель будет остановлен в течение {timeout} сек.',
];
