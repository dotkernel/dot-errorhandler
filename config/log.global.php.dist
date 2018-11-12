<?php

return [
    'dot_log' => [
        'loggers' => [
            'default_logger' => [
                'writers' => [
                    'FileWriter' => [
                        'name' => 'stream',
                        'priority' => \Zend\Log\Logger::ALERT,
                        'options' => [
                            'stream' => __DIR__.'/../../data/logs/dk-'.date('Y-m-d').'.log',
                            // explicitly log all messages
                            'filters' => [
                                'allMessages' => [
                                    'name' => 'priority',
                                    'options' => [
                                        'operator' => '>=',
                                        'priority' => \Zend\Log\Logger::EMERG,
                                    ],
                                ],
                            ],
                            'formatter' => [
                                'name' => \Zend\Log\Formatter\Json::class,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
