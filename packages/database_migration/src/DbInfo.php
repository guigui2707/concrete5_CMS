<?php namespace Concrete\Package\DatabaseMigration\Src;

defined('C5_EXECUTE') or die("Access Denied.");

use Database;

class DbInfo
{
    public function getVariableList($variableName)
    {
        $list = Database::connection()->fetchAll("SHOW VARIABLES LIKE ?", array($variableName));
        $variables = array();
        foreach ($list as $row) {
            array_push($variables, $row['Value']);
        }
        return $variables;
    }

    public function getVariable($variableName)
    {
        $list = $this->getVariableList($variableName);
        return sizeof($list) > 0 ? array_shift($list) : null;
    }
}