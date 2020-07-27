<?php


namespace Aimeos\MW\Setup\Task;


/**
 * Create MySQL full text index for mshop_catalog.label
 */
class PlatformCreateWeber extends TablesCreatePlatform
{
	public function getPreDependencies() : array
	{
		return ['TablesCreatePlatform'];
	}


	public function clean()
	{
	}


	public function migrate()
	{
		$this->msg( 'Weber create MySQL full text index for catalog', 0 );
		$this->status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$this->setupPlatform( 'db-catalog', 'mysql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'catalog-mysql.sql' );
	}
}
