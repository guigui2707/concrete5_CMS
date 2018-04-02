<?php namespace Concrete\Package\DatabaseMigration;

defined('C5_EXECUTE') or die("Access Denied.");

use Config;
use Database;
use Package;
use SinglePage;

class Controller extends Package
{
    protected $pkgHandle = 'database_migration';
    protected $appVersionRequired = '5.7.4.0';
    protected $pkgVersion = '0.9.5';

    public function getPackageName()
    {
        return t('Database Migration');
    }

    public function getPackageDescription()
    {
        return t('Migrate your database from lowercase to case sensitive tables or vice versa');
    }

    public function install()
    {
        $pkg = parent::install();
        SinglePage::add('/dashboard/system/backup/database_migration', $pkg);
    }

    public function getDatabaseMigrationTables()
    {
	    $concreteVersion = Config::get('concrete.version');
	    $tables = array(
		    'CollectionSearchIndexAttributes',
		    'FileSearchIndexAttributes',
		    'OauthUserMap',
		    'SystemDatabaseMigrations',
		    'UserSearchIndexAttributes',
	    );
	    if (version_compare($concreteVersion, '8.0.0', '>=')) {
		    $db = Database::connection();
		    $expressEntities = $db->fetchAll('SELECT * FROM ExpressEntities');
		    foreach ($expressEntities as $expressEntity) {
			    $expressEntityHandle = $expressEntity['handle'];
			    $expressEntityHandleNameSpace = implode('', array_map(function ($v, $k) {
				    return ucFirst($v);
			    }, explode('_', $expressEntityHandle), array_keys(explode('_', $expressEntityHandle))));
			    $tables[] = $expressEntityHandleNameSpace . 'ExpressSearchIndexAttributes';
		    }
		    $tables[] = 'SiteSearchIndexAttributes';
	    }
	    return $tables;
    }
}