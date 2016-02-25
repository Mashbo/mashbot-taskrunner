<?php

namespace Mashbo\Mashbot\TaskRunner;

interface TaskRunnerExtension
{
    public function amendTasks(TaskRunner $taskRunner);
}