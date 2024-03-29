<?php

return [
    'html' => [
        'catalog' => [
			'detail' => [
				'url' => [
					'filter' => []
				]
			],
            'lists' => [
                'show_longtext' => ['Aktionen_52400_FM'],
                'decorators' => [
                    'global' => ['ListCategories']
                ],
                'levels' => 2,
                'domains' => [
                   'media', 'price', 'text', 'product/property', 'attribute'
                ],
            ],
			'stock' => [
				'level' => [
						'low' => 100
				]
            ],
            'suggest' => [
                'name' => 'Weber'
            ]
        ],
        'common' => [
            'decorators' => [
                'default' => ['Context']
            ],
			'partials' => [
				'selection' => 'common/partials/selection-list'
			]
        ],
        'checkout' => [
            'standard' => [
                'process' => [
                    'address' => [
                        'name' => 'Weber'
                    ]
                ]
            ]
        ]
    ]
];
