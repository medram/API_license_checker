<?php

namespace MR4Web_API\Model;

abstract class Model {
	
	protected static $_db;
	protected $table;

	public function __construct()
	{
		self::$_db = &DB::getInstance();
	}

	protected function _setTable($tableName)
	{
		$this->table = $tableName;
	}

	protected function _tableName()
	{
		return $this->table;
	}

	protected function DB()
	{
		return self::$_db;
	}

	public function query($sql, array $params = array())
	{
		$stm = $this->DB()->prepare($sql);
		$stm->execute($params);
		if ($stm->rowCount() > 1)
			return $stm->fetchAll(\PDO::FETCH_ASSOC);
		else
			return $stm->fetch(\PDO::FETCH_ASSOC);
	}

	public function findAll($fields = NULL, $where = NULL)
	{
		$sql = "SELECT ";

		if ($fields == '*')
		{
			$sql .= ' * ';
		}
		if (!is_array($fields) && $fields != NULL)
		{
			$sql .= "`{$fields}`";
		}
		else if (is_array($fields))
		{
			foreach ($fields as $key => $field)
			{
				$sql .= "`{$field}`";
				if ($key != count($fields) - 1)
					$sql .= ", ";
			}
		}

		$sql .= " `{$this->tableName}` ";

		if (!is_array($where))
		{
			$sql .= " ".$where;
		}
		else if (count($where))
		{
			$sql .= "WHERE ";
			$i = 0;
			foreach ($where as $field => $value)
			{
				$sql .= "`{$field}`=:{$field}";
				if ($i != count($where) - 1)
					$sql .= " AND ";
				++$i;
			}
		}

		$stm = $this->DB()->prepare($sql);

		if (is_array($where) && count($where))
		{
			foreach ($where as $field => $value)
				$stm->bindParam(":{$field}", $value);
		}

		$stm->execute();
		if ($stm->rowCount())
			return $stm->fetchAll(\PDO::FETCH_ASSOC);
		return [];
	}

	public function find($fields = NULL, array $where = array())
	{
		return $this->findAll($fields, $where)[0];
	}

	public function update(){}
	public function delete(){}
}

?>