
    $taskRunner = new TaskRunner();
    $taskRunner->addComposed('do_things', ['thing1', 'thing2', 'thing3']);
    $taskRunner->addComposed('do_things2', ['x', 'y', 'z']);
    $taskRunner->addComposed('do_things3', ['do_things', 'do_things2']);

    $taskRunner->invoke('do_things', ['a' => 'A', 'b' => 'B');

    $taskRunner->add('do_things', function(TaskContext $context, Logger $logger, $a, $b) {
        $context->arguments();
    });

    $taskRunner->invoke('do_things3', ['args', 'args']);


    $taskRunner->before('dns:record:add', function(BeforeTaskContext $context) {
        $context->setArgument('record', $context->argument('record') . ".mashbo.com");
    });

    $taskRunner->tasks()->get('project:start')