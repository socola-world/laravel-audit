<?php

return [
    'database' => [
        'tables' => [
        ],
    ],
    'ignore' => [
        'class' => [
        ],
        'model' => [
        ],
    ],
    'skip_testcase' => [
        \SocolaDaiCa\LaravelAudit\Tests\App\ModelTest::class => [
            /* method name */
        ],
        //        SocolaDaiCa\LaravelAudit\Tests\Resources\ViewsTest::class => [
        //            'testRelativePathname',
        //        ],
    ],
    'database_design' => null,
];
