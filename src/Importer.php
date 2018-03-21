<?php

use DataSource\DataSource;
use Grace\DBAL\ConnectionAbstract\ConnectionInterface;

/**
 * Class Importer
 */
class Importer
{
    private $fields = [];

    /** @var ConnectionInterface */
    protected $db;
    protected $table;

    protected $rowsPerInsert = 1000;
    private $sqlHeader;

    /**
     * Importer constructor.
     * @param ConnectionInterface $db
     * @param $table
     * @param array $fields
     * @param bool $isTemp
     * @throws ImporterException
     */
    public function __construct(ConnectionInterface $db, $table, array $fields, $isTemp = true)
    {
        if (!$table) {
            throw new ImporterException('Не задана таблица для импорта');
        }

        if (!$fields) {
            throw new ImporterException('Не заданы поля для импорта.');
        }

        $this->db     = $db;
        $this->fields = $fields;
        $this->table  = $table;

        if ($isTemp) {
            $this->table .= '_xml_importer';
            DbHelper::createTable($this->db, $this->table, $this->fields, $isTemp);
        }
    }

    /**
     * @param int $limit
     * @return int
     */
    public function setRowsLimit($limit = 10000)
    {
        return $this->rowsPerInsert = $limit;
    }

    /**
     * @return int
     */
    public function getRowsLimit()
    {
        return $this->rowsPerInsert;
    }

    /**
     * @param DataSource $reader
     * @return string
     * @throws \Grace\DBAL\Exception\QueryException
     */
    public function import(DataSource $reader)
    {
        $this->db->start();
        $i = 0;
        foreach ($reader->getRows($this->getRowsLimit()) as $rows) {
            $sql = $this->getQuery($rows);
            try {
                $this->db->execute($sql, [[$rows]]);
            }catch(Exception $e){
                echo '!';
                continue;
            }
            ++$i;
            if (($i % 10000) == 0) {
                //$memory = $this->convert(memory_get_usage(true));
                //echo ". ({$memory})";
                echo ".";
            }
        }
        $this->db->commit();

        return $this->table;
    }

    function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * @param $rowExample
     * @return mixed
     */
    private function getQuery($rowExample)
    {
        if (!$this->sqlHeader) {
            $fields = [];
            $primary = '';
            foreach ($rowExample as $attribute => $devNull) {
                $fields[] = $this->fields[$attribute]['name'];
                if(!empty($this->fields[$attribute]['primary'])){
                    $primary = $this->fields[$attribute]['name'];
                }
            }

            $headerPart = $this->db->replacePlaceholders('INSERT INTO ?f (?i) VALUES {v} ', [$this->table, $fields]);
            unset($fields);

            if($primary) {
                $headerPart .= ' ON CONFLICT ("'.$primary.'") DO NOTHING;';
            }

            $this->sqlHeader = str_replace('{v}', '?v', $headerPart);
        }

        return $this->sqlHeader;
    }
}
