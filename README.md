
    $taskRunner = new TaskRunner();
    $taskRunner->add('a', function(TaskRunner $taskRunner) {
        $taskRunner->invoke('b');
    });

    $taskRunner->addAbstract('b');

