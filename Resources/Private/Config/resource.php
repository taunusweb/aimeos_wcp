<?php

return [
	'solr' => [
		'scheme' => $GLOBALS['TYPO3_CONF_VARS']['SOLR']['scheme'] ?? 'https',
		'host' => $GLOBALS['TYPO3_CONF_VARS']['SOLR']['host'] ?? 'solr6269:yJcs67JK@solr6269.solr-hosting.info',
		'port' => $GLOBALS['TYPO3_CONF_VARS']['SOLR']['port'] ?? '443',
		'path' => $GLOBALS['TYPO3_CONF_VARS']['SOLR']['path'] ?? 'solr',
		'index' => $GLOBALS['TYPO3_CONF_VARS']['SOLR']['index'] ?? 'aimeos_2022',
	]
];
