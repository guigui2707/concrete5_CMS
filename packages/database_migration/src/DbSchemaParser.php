<?php namespace Concrete\Package\DatabaseMigration\Src;

defined('C5_EXECUTE') or die("Access Denied.");

class DbSchemaParser
{
    private $tableNames = array();

    private function createParser()
    {
        $xmlParser = xml_parser_create();
        xml_set_object($xmlParser, $this);
        xml_set_element_handler($xmlParser, 'tagOpen', 'tagClose');
        xml_set_character_data_handler($xmlParser, 'tagCdataTagClose');
        return $xmlParser;
    }

    private function tagOpen(&$parser, $tag, $attributes)
    {
        switch (strtoupper($tag)) {
            case 'TABLE':
                $this->addTableName($attributes['NAME']);
            default:
                // Dummy
        };
    }

    private function tagClose(&$parser, $tag)
    {
        // Dummy
    }

    private function tagCdataTagClose(&$parser, $cdata)
    {
        // Dummy
    }

    public function addTableName($name)
    {
        $this->tableNames[] = $name;
    }

    public function getTableNames()
    {
        asort($this->tableNames);
        return $this->tableNames;
    }

    public function parseSchema($xmlFile)
    {
        if (!(file_exists($xmlFile))) {
            return false;
        }
        if (!($fp = @fopen($xmlFile, 'r'))) {
            return false;
        }
        $parser = $this->createParser();
        while ($data = fread($fp, 4096)) {
            if (!xml_parse($parser, $data, feof($fp))) {
                die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xmlParser)), xml_get_current_line_number($xmlParser)));
            }
        }
        xml_parser_free($parser);
        $this->tableNames = array_unique($this->tableNames);
    }
}