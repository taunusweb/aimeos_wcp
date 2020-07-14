<?php

return [
	'customer' => [
		'manager' => [
			'typo3' => [
				'pid-default' => 87
			],
		],
	],
	'index' => [
		'manager' => [
			'name' => 'Solr',
			'standard' => [
				'domains' => [
					'catalog' => 'catalog',
					'attribute' => 'attribute',
					'product' => ['default'],
					'price' => ['default'],
					'text' => 'text',
				],
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
