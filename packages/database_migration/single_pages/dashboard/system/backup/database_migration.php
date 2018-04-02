<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
$ih = Core::make('helper/concrete/ui');
?>

<div class="database-migration-wrapper">
    <h2>
        <?php  echo t("Database Migration Tool") ?>
    </h2>

    <p>
        <?php  echo t("This tool lets you easily convert your case insensitive database table names to case sensitive."); ?>
    </p>

    <div class="row">
        <div class="col-lg-6">
            <h3>
                <?php  echo t("Migrate Automatically") ?>
            </h3>

            <p>
                <?php  echo t("If you want to migrate this site's table names, just click the button below."); ?>
            </p>

            <?php  if ($lowerCaseEnabled) { ?>
                <div class="alert alert-danger">
                    <p>
                        <i class="fa fa-exclamation"></i>
                        <strong>
                            <?php  echo t("Your database is set to lower case mode and running this script automatically will not work"); ?>
                        </strong>
                        <i class="fa fa-exclamation"></i>
                    </p>

                    <p>
                        <?php  echo t("Please note that for you to be able to do this, you need to have following configuration in your MySQL server's %1s or %2s.", '<strong>my.cnf</strong>', '<strong>my.ini</strong>') ?>
                    </p>

                    <pre>lower_case_table_names=0</pre>

                    <p>
                        <?php  echo t("You can also run the script manually after you've moved the site by downloading the migration script - click the '%1s' button.", t('Download Migration Script')); ?>
                    </p>
                </div>
            <?php  } ?>

            <?php  echo $ih->button('<i class="fa fa-database"></i> ' . t('Run Migration'), $this->action('migrate'), '', 'btn-primary'); ?>

            <?php  if (count($backupFiles) > 0) { ?>
                <h3>
                    <?php  echo t('Migrate Backup Files'); ?>
                </h3>

                <p>
                    <?php  echo t('Currently you have %d backup file(s) available. If you want to migrate these files, just click on the link below.', count($backupFiles)); ?>
                </p>

                <?php  echo $ih->button(t('Migrate Backup Files'), $this->action('migrate_backup'), '', 'btn-primary'); ?>
            <?php  } ?>
        </div>
        <div class="col-lg-6">
            <h3>
                <?php  echo t("Manual Migration"); ?>
            </h3>

            <p>
                <?php  echo t("If you need to migrate your moved site download the SQL script below and run it on your database server to be migrated.") ?>
            </p>

            <h4>
                <?php  echo t('lowercase to UpperCase'); ?>
            </h4>

            <?php  echo $ih->button('<i class="fa fa-download"></i> ' . t("Download Migration Script"), $this->action("download_script"), '', 'btn-success'); ?>

            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                <?php  echo t("In case you're moving from a (Linux) UpperCase environment to a (Windows) lowercase environment, there's also a migration vice-versa (Uppercase to lowercase) downloadable below."); ?>
            </div>

            <h4>
                <?php  echo t('UpperCase to lowercase'); ?>
            </h4>

            <?php  echo $ih->button('<i class="fa fa-download"></i> ' . t("Download Migration Script"), $this->action("download_script", 1), '', 'btn-info'); ?>
        </div>
    </div>

    <?php 
    $missingTablesCount = count($missingTables);
    if ($missingTablesCount > 0) {
        ?>
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i>
            <?php  echo t("Some tables are missing from the migration!") ?>
        </div>

        <p>
            <?php  echo t("Because Concrete5 is open source and has a wide range of third party Add-Ons, this tool can not guarantee that all the database tables are included in the migration. Missing tables might also belong to some uninstalled Add-Ons.") ?>
        </p>

        <p>
            <?php 
            if ($missingTablesCount == 1) {
                echo t("This table is missing from this site's database migration");
            } else {
                echo t("These %1s tables are missing from this site's database migration", '<strong>' . $missingTablesCount . '</strong>');
            } ?>:
        </p>

        <pre><?php  echo trim(implode("\r\n", $missingTables)); ?></pre>
    <?php  } ?>
</div>
