<?php

return array(
	'name' => 'aimeos_wcp',
	'depends' => array(
		'aimeos-core',
		'ai-admin-jqadm',
		'ai-admin-jsonadm',
		'ai-client-html',
		'ai-client-jsonapi',
		'ai-controller-jobs',
		'ai-controller-frontend',
	),
	'include' => array(
		'lib/custom/src',
		'client/html/src',
		'client/jsonapi/src',
		'controller/common/src',
		'controller/frontend/src',
		'controller/jobs/src',
		'admin/jsonadm/src',
		'admin/jqadm/src',
	),
	'i18n' => array(
		'client' => 'client/i18n',
/*		'admin' => 'admin/i18n',
		'admin/jsonadm' => 'admin/jsonadm/i18n',
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
		'controller/common' => 'controller/common/i18n',
		'controller/frontend' => 'controller/frontend/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
		'mshop' => 'lib/custom/i18n',
*/	),
	'config' => array(
		'config',
	),
	'template' => [
		'admin/jqadm/templates' => [
			'admin/jqadm/templates',
		],
		'admin/jsonadm/templates' => [
			'admin/jsonadm/templates',
		],
		'client/html/templates' => [
			'client/html/templates',
		],
		'client/jsonapi/templates' => [
			'client/jsonapi/templates',
		],
		'controller/jobs/templates' => [
			'controller/jobs/templates',
			'client/html/templates',
			'lib/custom/templates',
		],
	],
	'custom' => [
		'admin/jqadm' => [
			'admin/jqadm/manifest.jsb2',
		],
		'controller/jobs' => [
			'controller/jobs/src',
		],
	],
	'setup' => array(
		'lib/custom/setup',
	),
);
