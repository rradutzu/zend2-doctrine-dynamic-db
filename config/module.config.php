<?php

namespace DoctrineDynamicDb;

return [
    'doctrine' => [
        'dynamic_entitymanager' => [
            'orm_default' => []
        ]
    ],
    'doctrine_factories' => [
        'dynamic_entitymanager' => 'DoctrineDynamicDb\Service\DynamicEntityManagerFactory'
    ]
];
