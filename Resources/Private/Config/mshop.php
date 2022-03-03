<?php

return [
	'catalog' => [
		'manager' => [
			'decorators' => [
				'local' => [
					'Weber' => 'Weber'
				]
			]
		]
	],
	'customer' => [
		'manager' => [
			'typo3' => [
				'pid-default' => 87
			],
		],
	],
	'index' => [
		'manager' => [
			'chunksize' => 100,
			'name' => 'Solr',
			'domains' => [
				'catalog' => 'catalog',
				'attribute' => 'attribute',
				'product' => ['default'],
				'price' => ['default'],
				'text' => 'text',
			],
			'attribute' => [
				'name' => 'Solr'
			],
			'catalog' => [
				'name' => 'Solr'
			],
			'price' => [
				'name' => 'Solr'
			],
			'text' => [
				'name' => 'Weber'
			]
		]
	],
	'price' => [
		'taxflag' => 0,
	],
];
