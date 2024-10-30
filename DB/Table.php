<?php


namespace DB;



//use views\mPrint;

class Table extends Connection
{
    private $declareVariable = Array();
    private $TMpNameTable = '';

    private $childClass;
    /**
     * Создание новой таблицы не существующей в БД
     */
    public function create()
    {
        $this->initColumn();
        $query = $this->createQuery();
        try {
            $this->complexQuery($query);
        }catch (\PDOException $e) {
            //mPrint::PDO($e,$query);
            exit;
        }


        $indexes = $this->createNoClusteredIndexes();
        foreach ($indexes as $query){
            $this->complexQuery($query);
        }

        $this->TMpNameTable = '';
    }



    public function setTMpNameTable($TMpNameTable)
    {
        $this->TMpNameTable = $TMpNameTable;
    }

    /**
     * базовая инициализация массива описания таблицы
     * @param string $childClass
     */
    public function initColumn($childClass = '')
    {
        $this->childClass = $childClass;
        $this->declareVariable = Array(
            'identifierColumn' => false,
            'columns' => Array(),
            'primaryIndex' => Array(),
            'nonclusteredIndex' => Array(),
        );

        $this->setDefaultType($this->getConstants());
    }


    /**
     * назначает всем константам значение по умолчанию
     * @param $arrayAllColumns
     */
    private function setDefaultType($arrayAllColumns)
    {
        foreach ($arrayAllColumns as $column){
            $this->declare_type($column,Type::nvarchar,50);
        }
    }



    /**
     * получает список всех констант(полей БД) в дочернем классе
     * @return array
     */
    private function getConstants() {
        $oClass = new \ReflectionClass($this->childClass);
        return $oClass->getConstants();
    }




    /**
     * @param string $variable
     * @param string $type
     * @param string|bool $size
     */
    public function declare_type(string $variable,string $type,string|bool  $size = false)
    {
        $this->declareVariable['columns'][$variable]['type'] = $type;
        $size = \DB\Type::getSizeFalseForType($type,$size);
        $this->declareVariable['columns'][$variable]['size'] = $size;
    }


    /**
     * @param string $variable
     * @param string|null $value
     */
    public function declare_defaultValue(string $variable,string $value = null)
    {
        $this->declareVariable['columns'][$variable]['defaultValue'] = $value;
    }


    /**
     * @param string $variable
     * @param string $sort прямая сортировка по умолчанию (ASC) обратьная должно быть значение DESC
     */
    public function declare_primaryIndex(string $variable,string $sort = 'ASC')
    {
        $this->declareVariable['primaryIndex'][$variable] = $sort;
    }


    /**
     * @param string $nameIndex
     * @param string $variable
     * @param string $sort
     */
    public function declare_nonclusteredIndex(string $nameIndex,string $variable,string $sort = 'ASC')
    {
        $this->declareVariable['nonclusteredIndex'][$nameIndex][$variable] = $sort;
    }



    /**
     * @param string $variable
     */
    public function identifierColumn(string $variable)
    {
        $this->declareVariable['identifierColumn'] = $variable;
    }



    /**
     * @return array
     */
    public function getDeclareVariable(): array
    {
        return $this->declareVariable;
    }



    private function createQuery()
    {
        $table = $this->getName().$this->TMpNameTable;
        $query = '';

        foreach ($this->declareVariable['columns'] as $column => $properties){

            $type = $properties['type'];
            $type .= $properties['size'] !== false ? "( {$properties['size']} )" : "";

            // блок определения значенеи поля по умолчанию

            $defaultValue = 'NULL';

            // Если поле попадает в кластеризованный индекс оно автоматом становитья не нуливое
            if (array_key_exists($column,$this->declareVariable['primaryIndex'])){
                $defaultValue = 'NOT NULL';
            }

            if (array_key_exists('defaultValue',$properties))
                switch ($properties['type']){
                    case 'int':
                    case 'money':
                        $defaultValue = "default ({$properties['defaultValue']})";
                        break;
                    default:{
                        $l = "'"; // по умолчанию дефолтное значение берётся в одинарные кавычки
                        if ( // но если присутствует скобочка, то дефолтное значение возвращает какаято функция
                            (str_contains($properties['defaultValue'],'(')) ||
                            (str_contains($properties['defaultValue'],')'))
                        ){
                            $l = ''; // в этой связи дефолтное значение не берётся в одинарные кавычки
                        }
                        $defaultValue = "default ($l{$properties['defaultValue']}$l)";
                    }

                }

            // в случае если столбец имеет автоинкремент то значение по умочанию замещается инструкцией
            if (array_key_exists('identifierColumn',$this->declareVariable)){
                if ($this->declareVariable['identifierColumn'] == $column){
                    $defaultValue = 'IDENTITY(1,1) NOT NULL';
                }
            }

            // построение кластеризоыванного индекса

            $query .= " $column $type $defaultValue,";
        }
        $query = substr($query,0,-1);

        $clusteredIndex = $this->createClusteredIndex();

        $query = "CREATE TABLE dbo.$table ( $query  $clusteredIndex)";
        return $query;
    }


    public function createClusteredIndex()
    {
        $query  = '';
        $table = $this->getName().$this->TMpNameTable;
        if (array_key_exists('primaryIndex',$this->declareVariable)){
            foreach ($this->declareVariable['primaryIndex'] as $column => $sort) {
                $query .= "$column $sort,";
            }
            $query = substr($query,0,-1);
            if ($query != "")
                $query = ", CONSTRAINT [{$table}_PK_{$table}] PRIMARY KEY CLUSTERED (	$query )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]";
        }
        return $query;
    }

    /**
     * @return array
     */
    private function createNoClusteredIndexes()
    {
        $query = Array();
        if (array_key_exists('nonclusteredIndex',$this->declareVariable)) {
            foreach ($this->declareVariable['nonclusteredIndex'] as $nameIndex => $fields) {
                $query[] = $this->createNoClusteredIndex($nameIndex, $fields);
            }
        }
        return $query;
    }



    /**
     *  формирование запроса по создапнию индекса
     * @param string $nameIndex имя индекса
     * @param array $fields Список полей индекса и тип сортировки полей
     * @return string SQL запрос
     */
    public function createNoClusteredIndex(string $nameIndex, Array $fields)
    {
        $query = '';
        $table = $this->getName().$this->TMpNameTable;
        foreach ($fields as $column => $sort) {
            $query .= "$column $sort,";
        }
        $query = substr($query,0,-1);
        $query = "CREATE NONCLUSTERED INDEX [{$table}_IX_$nameIndex] ON [dbo].$table ( $query )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]";

        return $query;
    }

    /**
     * формирование запроса по удалению индекса
     * @param string $nameIndex имя индекса
     * @return string SQL запрос
     */
    public function DROP_NoClusteredIndex(string $nameIndex)
    {
        $table = $this->getName();

        $query = " DROP INDEX  IF EXISTS  {$table}_IX_$nameIndex ON [dbo].$table";

        return $query;
    }

    /**
     * Должна возвращать массив со строками по умолчанию, либо false
      Array(
            Array(
                'id' => 1,
                'name' => 'Красный'
            )
      )
     * @return mixed
     */
    public function defaultRows()
    {
        return false;
    }

    public function reRecordDefaultRows()
    {
        $rows = $this->defaultRows();
        $this->table(self::getName());
        $this->delete();
        foreach ($rows as $key => $row){
            $this->init();
            foreach ($row as $field => $value){
                $this->set($field , $value);
            }
            $this->insert();
        }
    }
}
