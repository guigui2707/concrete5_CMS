<?php  namespace Concrete\Package\DatabaseMigration\Controller\SinglePage\Dashboard\System\Backup;

defined('C5_EXECUTE') or die("Access Denied.");

use Config;
use Core;
use Concrete\Package\DatabaseMigration\Src\DbFix;
use Concrete\Core\File\Service\File;
use \Concrete\Core\Page\Controller\DashboardPageController;

class DatabaseMigration extends DashboardPageController
{
    protected $pkgHandle = 'database_migration';

    public function on_start()
    {
        $al = \Concrete\Core\Asset\AssetList::getInstance();
        $al->register('css', 'database_migration_view', 'css/database_migration.view.css', array(), $this->pkgHandle);
    }

    public function view()
    {
        $this->requireAsset('css', 'database_migration_view');
        $h = new DbFix();
        $this->set('dbscript', $h->getFixScript());
        $this->set('missingTables', $h->getMissingTables());
        $this->set('lowerCaseEnabled', $h->isDatabaseLowerCase());
        $this->set('backupFiles', $this->getBackupFiles());
    }

    private function getBackupFiles()
    {
        $fileService = new File();
        $arr = @$fileService->getDirectoryContents(DIR_FILES_BACKUPS);
        return is_array($arr) ? $arr : array();
    }

    public function migrate()
    {
        $h = new DbFix();
        $h->fix();
        $this->set('message', t("Migrated successfully!"));
        $this->view();
    }

    public function download_script($lowerToUpper = '0')
    {
        $h = new DbFix();
        $th = Core::make("helper/text");
        $values = array(
            'db_migration',
            $th->sanitizeFileSystem(Config::get('concrete.site')),
            $lowerToUpper == '1' ? 'uc-2-lc' : 'lc-2-uc',
            date('Y-m-d'),
        );
        $filename = implode('_', $values) . '.sql';
        $fixScript = $h->getFixScript($lowerToUpper == '1' ? false : true);
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo "-- DB Migration script, " . ($lowerToUpper == '1' ? 'UpperCase to lowercase' : 'lowercase to UpperCase') . "\r\n";
        echo "-- Generated: " . date("Y-m-d G:i:s") . "\r\n";
        echo "\r\n";
        echo $fixScript;
        exit;
    }

    public function migrate_backup()
    {
        $e = Core::make('helper/validation/error');
        $files = $this->getBackupFiles();
        if (count($files) > 0) {
            $fileService = new File();
            $crypt = Core::make('helper/encryption');
            $fix = new DbFix();
            $tables = $fix->getCorrectTables();
            foreach ($files as $file) {
                $encrypt = false;
                $file = DIR_FILES_BACKUPS . '/' . $file;
                $str_restSql = $fileService->getContents($file);
                if (!preg_match('/INSERT/m', $str_restSql) && !preg_match('/CREATE/m', $str_restSql)) {
                    $encrypt = true;
                    $str_restSql = $crypt->decrypt($str_restSql);
                }
                foreach ($tables as $tbl) {
                    $pattern = "/([ `]{1,1})(" . strtolower($tbl) . ")([`]?)([ ;]{1,1})/";
                    $cb = create_function('$matches', 'return ' . get_class() . '::replaceTableName($matches, "' . $tbl . '");');
                    $str_restSql = preg_replace_callback($pattern, $cb, $str_restSql);
                }
                $fileService->clear($file);
                $fileService->append($file, $encrypt ? $crypt->encrypt($str_restSql) : $str_restSql);
            }
            if (!$e->has()) {
                $this->set('message', t("Successfully migrated your backup files!"));
            }
        }
        $this->view();
    }

    public static function replaceTableName($matches, $origTable)
    {
        return $matches[1] . $origTable . $matches[3] . $matches[4];
    }
}