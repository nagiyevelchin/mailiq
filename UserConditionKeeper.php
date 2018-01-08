<?php
/**
 * UserConditionKeeper.php
 * Author: Elchin Nagiyev <elchin at nagiyev.pro>
 * Date: 1/9/2018 00:22
 */

class UserConditionKeeper {
	public static $field_list = [
		'id'         => 'us.id',
		'email'      => 'us.email',
		'password'   => 'us.password',
		'role'       => 'us.role',
		'reg_date'   => 'us.reg_date',
		'last_visit' => 'us.last_visit',
		'country'    => 'country',
		'firstname'  => 'firstname',
		'state'      => 'state'
	];
	public static $usersTable = 'users';
	public static $usersAboutTable = 'users_about';
	public static $limitRowCount = 100;
	private $limitRowOffset = 0;
	private $conditions = [], $and_condition;

	/**
	 * @param mixed  $id
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setId($id, $condition, $logic = 'AND') {
		$this->setProperty('id', $id, $condition, $logic);
	}

	private function setProperty($property, $value, $condition, $logic = 'AND') {
		$this->$property = ['value' => $value, 'condition' => $condition, 'logic' => $logic, 'param' => sprintf('%s_%s', $property, spl_object_hash($this))];
		if ($this->and_condition === null && $logic == 'AND') {
			$this->and_condition = $property;
		}
	}

	/**
	 * @param mixed  $email
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setEmail($email, $condition, $logic = 'AND') {
		$this->setProperty('email', $email, $condition, $logic);
	}

	/**
	 * @param mixed  $password
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setPassword($password, $condition, $logic = 'AND') {
		$this->setProperty('password', $password, $condition, $logic);
	}

	/**
	 * @param mixed  $role
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setRole($role, $condition, $logic = 'AND') {
		$this->setProperty('role', $role, $condition, $logic);
	}

	/**
	 * @param mixed  $reg_date
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setRegDate($reg_date, $condition, $logic = 'AND') {
		$this->setProperty('reg_date', $reg_date, $condition, $logic);
	}

	/**
	 * @param mixed  $last_visit
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setLastVisit($last_visit, $condition, $logic = 'AND') {
		$this->setProperty('last_visit', $last_visit, $condition, $logic);
	}

	/**
	 * @param mixed  $country
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setCountry($country, $condition, $logic = 'AND') {
		$this->setProperty('country', $country, $condition, $logic);
	}

	/**
	 * @param mixed  $firstname
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setFirstname($firstname, $condition, $logic = 'AND') {
		$this->setProperty('firstname', $firstname, $condition, $logic);
	}

	/**
	 * @param mixed  $state
	 * @param        $condition =, !=, >, <, NOT, LIKE, IN ...
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setState($state, $condition, $logic = 'AND') {
		$this->setProperty('state', $state, $condition, $logic);
	}

	public function fetch() {
		if ($this->statement === false) {
			$this->execute();
		}

		return DPDO::fetch($this->statement);
	}

	public function execute($reset_row_offset = true) {
		if($reset_row_offset) {
			$this->limitRowOffset = 0;
		}
		$query = $this->getQuery();
		$this->statement = DPDO::prepare($query['query']);
		DPDO::execute($query['params'], $this->statement);
	}

	public function getQuery($with_limit = true) {
		$sql = 'SELECT
                  us.*,
                  MAX(IF(ua.item = \'country\', ua.value, \'\')) country,
                  MAX(IF(ua.item = \'firstname\', ua.value, \'\')) firstname,
                  MAX(IF(ua.item = \'state\', ua.value, \'\')) state
                FROM
                  ' . self::$usersTable . ' us
                  JOIN ' . self::$usersAboutTable . ' ua
                    ON us.id = ua.user
                GROUP BY us.id
                HAVING ';
		$query = $this->getConditionsQuery();
		$query['query'] = $sql . $query['query'];
		if($with_limit) {
			$query['query'] = $query['query'] . ' 
			LIMIT ' . $this->limitRowOffset . ',' . self::$limitRowCount . ';';
		}
		return $query;
	}

	public function getConditionsQuery() {
		$sql = '';
		$params = [];
		$this->getConditions();
		$iteration = 0;
		foreach ($this->conditions as $cnd) {
			if ($iteration === 0) {
				$sql .= sprintf(' %s %s :%s', $cnd['field'], $cnd['condition'], $cnd['param']);
			} else {
				$sql .= sprintf(' %s %s %s :%s', $cnd['logic'], $cnd['field'], $cnd['condition'], $cnd['param']);
			}
			$params[$cnd['param']] = $cnd['value'];
			++$iteration;
		}

		return ['query' => $sql, 'params' => $params];
	}

	public function getConditions() {
		$this->conditions = [];
		if ($this->and_condition) {
			$this->conditions[] = array_merge(['field' => self::$field_list[$this->and_condition]], $this->{$this->and_condition});
		}
		foreach (self::$field_list as $var_name => $column_name) {
			if ($this->$var_name !== null && $this->and_condition !== $var_name) {
				$this->conditions[] = array_merge(['field' => $column_name], $this->$var_name);
			}
		}

		return $this->conditions;
	}

	public function executeNext() {
		$this->limitRowOffset += self::$limitRowCount;
		$this->execute(false);
	}

	public function getCount() {
		$query = $this->getQuery(false);
		$cnt = DPDO::executeAndGetFirst(sprintf(' SELECT COUNT(*) AS cnt FROM (%s) ttt', $query['query']), $query['params']);

		return $cnt['cnt'];
	}
}
