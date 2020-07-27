<?php

return [
    'html' => [
        'catalog' => [
            'lists' => [
                'levels' => 2,
                'domains' => [
                        'media', 'price', 'text', 'product/property', 'attribute'
                ],
            ],
            'suggest' => [
                'name' => 'Weber'
            ]
        ],
        'common' => [
            'decorators' => [
                 'default' => ['Context']
            ],
       ],
    ]
];
