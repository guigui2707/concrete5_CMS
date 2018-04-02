<?php namespace Concrete\Package\DatabaseMigration\Src;

defined('C5_EXECUTE') or die("Access Denied.");

use BlockType;
use BlockTypeList;
use Config;
use Database;
use Environment;
use Package;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Package\DatabaseMigration\Src\DbSchemaParser;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class DbSchemaReader
{
    private $parser = null;

    public function __construct()
    {
        $this->parser = new DbSchemaParser();
    }

    public function fixDatabaseNames()
    {
        $db = Database::connection();
        $queries = $this->getFixScriptRows();
        foreach ($queries as $query) {
            $db->executeQuery($query);
        }
    }

    protected function getCurrentTables(){
        $currentTablesRows = Database::connection()->fetchAll("SHOW TABLES");
        $currentTables = array();
        foreach($currentTablesRows as $currentTablesRow){
            $currentTables[] = $currentTablesRow[key($currentTablesRow)];
        }
        return $currentTables;
    }

    public function getFixScriptRows($appendCorrect = false, $lowerToUpper = true)
    {
        $names = $this->getDatabaseTableNames(true);
        $currentTables = $this->getCurrentTables();
        $rows = array();
        foreach ($names as $tbl) {
            $migrateTbl = $lowerToUpper ? strtolower($tbl) : $tbl;
            $tbl = !$lowerToUpper ? strtolower($tbl) : $tbl;
            if ($appendCorrect || ($key = array_search($migrateTbl, $currentTables)) !== false) {
                $tmpName = $migrateTbl . "_tmp";
                array_push($rows, "RENAME TABLE " . $migrateTbl . " TO " . $tmpName . ";");
                if ($appendCorrect || ($key2 = array_search($tbl, $currentTables)) !== false) {
                    array_push($rows, "DROP TABLE IF EXISTS " . $tbl . ";");
                    unset($currentTables[$key2]);
                }
                array_push($rows, "RENAME TABLE " . $tmpName . " TO " . $tbl . ";");
                unset($currentTables[$key]);
            }
        }
        return $rows;
    }

    public function getMissingTables()
    {
        $names = $this->getDatabaseTableNames(true);
        $currentTables = $this->getCurrentTables();
        foreach ($names as $tbl) {
            $migrateTbl = strtolower($tbl);
            if (($key = array_search($migrateTbl, $currentTables)) !== false) {
                unset($currentTables[$key]);
            }
            if (($key = array_search($tbl, $currentTables)) !== false) {
                unset($currentTables[$key]);
            }
        }
        return $currentTables;
    }

    public function getDatabaseTableNames($asort = false)
    {
        if (sizeof($this->parser->getTableNames()) > 0) {
            return $this->parser->getTableNames();
        }
        $this->parseCoreSchema();
        $this->parseCoreSpecialSchema();
        $this->parseBlockSchemas();
        $this->parseAttributeTypeSchemas();
        $this->parsePackageSchemas();
	    $this->parseEntities();
        $tableNames = $this->parser->getTableNames();
        if($asort){
            asort($tableNames);
        }
        return $tableNames;
    }

    private function parseCoreSpecialSchema()
    {
        return $this->parseSchema(DIR_BASE_CORE . "/authentication/concrete", FILENAME_PACKAGE_DB);
    }

    private function parseCoreSchema()
    {
        return $this->parseSchema(DIR_BASE_CORE . "/config", FILENAME_PACKAGE_DB);
    }

    private function parseBlockSchemas()
    {
        $env = Environment::get();
        $blocks = BlockTypeList::getInstalledList();
        $internalBlocks = Database::connection()->fetchAll('SELECT *, bt.btID from BlockTypes bt LEFT JOIN BlockTypeSetBlockTypes btsbt ON btsbt.btID = bt.btID WHERE btIsInternal = ? ORDER BY btDisplayOrder', array('1'));
        foreach ($internalBlocks as $internalBlock) {
            $blocks[] = BlockType::getByHandle($internalBlock['btHandle']);
        }
        foreach ($blocks as $b) {
            $btHandle = $b->getBlockTypeHandle();
            $dir = dirname($env->getPath(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_CONTROLLER));
            if ($b->getPackageID() > 0) {
                $pkgHandle = $b->getPackageHandle();
                $dir = dirname($env->getPath(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_CONTROLLER, $pkgHandle));
            }
            if (file_exists($dir . '/' . FILENAME_BLOCK_DB)) {
                $this->parseSchema($dir, FILENAME_BLOCK_DB);
            }
        }
        return true;
    }

    private function parseAttributeTypeSchemas()
    {
        $ats = AttributeType::getList();
        foreach ($ats as $at) {
            if ($file = $this->getAttributeTypeFilePath($at, FILENAME_ATTRIBUTE_DB)) {
                $this->parseSchema($file);
            }
        }
        return true;
    }

    private function parsePackageSchemas()
    {
        $packages = Package::getInstalledList();
        foreach ($packages as $pkg) {
	        $pkgHandle = $pkg->getPackageHandle();
	        $pkg = Package::getByHandle($pkgHandle); // needed in version 5.7.x
	        if (is_object($pkg)) {
                $dir = DIR_PACKAGES . '/' . $pkgHandle . '/';
                if (file_exists($dir . '/' . FILENAME_PACKAGE_DB)) {
                    $this->parseSchema($dir, FILENAME_BLOCK_DB);
                }
	            if (method_exists('Concrete\Package\\' . $this->_nameSpace($pkgHandle) . '\Controller', 'getDatabaseMigrationTables')) {
                    $arr = $pkg->getDatabaseMigrationTables();
                    if (is_array($arr)) {
                        asort($arr);
                        foreach ($arr as $tbl) {
                            $this->parser->addTableName($tbl);
                        }
                    }
                }
            }
        }
        return true;
    }

    private function parseSchema($dirOrPath, $dbFile = null)
    {
        $xmlFile = $dirOrPath;
        if ($dbFile !== null) {
            $xmlFile .= "/" . $dbFile;
        }
        $this->parser->parseSchema($xmlFile);
    }

	private function parseEntities()
	{
		$concreteVersion = Config::get('concrete.version');
		if (version_compare($concreteVersion, '8.0.0', '>=')) {
			$entitiesDirectory = DIR_BASE_CORE . '/src/Entity';
			$di = new RecursiveDirectoryIterator($entitiesDirectory, RecursiveDirectoryIterator::SKIP_DOTS);
			$it = new RecursiveIteratorIterator($di);
			$patterns = '/@ORM\\\Table\(name="([a-zA-Z]+)"|@ORM\\\JoinTable\(name="([a-zA-Z]+)"/';
			$checkKeys = array(1, 2);
			foreach ($it as $file) {
				if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
					$fileContents = preg_replace('/\s+/', '', str_replace('*', '', $file->getContents())); // replace * characters and remove all spaces/whitespace
					preg_match_all($patterns, $fileContents, $matches);
					if (!empty($matches)) {
						foreach ($checkKeys as $checkKey) {
							if (isset($matches[$checkKey]) && !empty($matches[$checkKey])) {
								$matches[$checkKey] = array_filter($matches[$checkKey]);
								foreach ($matches[$checkKey] as $match) {
									$this->parser->addTableName($match);
								}
							}
						}
					}
				}
			}
		}
	}

	private function _nameSpace($name)
	{
		$nameSpaced = implode('', array_map(function ($v, $k) {
			return ucfirst($v);
		}, explode('_', $name), array_keys(explode('_', $name))));
		return $nameSpaced;
	}

	private function getAttributeTypeFilePath($at, $_file){
		$env = Environment::get();
		$r = $env->getRecord(implode('/', array(DIRNAME_ATTRIBUTES . '/' . $at->getAttributeTypeHandle() . '/' . $_file)), $at->getPackageHandle());
		if ($r->exists()) {
			return $r->file;
		}
	}
}