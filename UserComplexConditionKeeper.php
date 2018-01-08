<?php
/**
 * UserComplexConditionKeeper.php
 * Author: Elchin Nagiyev <elchin at nagiyev.pro>
 * Date: 1/9/2018 00:22
 */

class UserComplexConditionKeeper {
	private $conditions = [], $and_condition, $sorted = false, $statement = false, $limitRowOffset = 0;

	/**
	 * @param mixed  $condition
	 * @param string $logic AND, NOT, OR, XOR
	 */
	public function setCondition($condition, $logic = 'AND') {
		$this->conditions[] = [$condition, $logic];
		$this->sorted = false;
		if ($this->and_condition === null && $logic == 'AND') {
			$this->and_condition = count($this->conditions) - 1;
		}
	}

	public function getConditions() {
		$this->sortConditions();
		$conditions = [];
		foreach ($this->conditions as $cnd) {
			$arr = $cnd[0]->getConditions();
			$arr['logic'] = $cnd[1];
			$conditions[] = $arr;
		}

		return $conditions;
	}

	public function sortConditions() {
		if (!$this->sorted) {
			$this->sorted = true;
			if ($this->and_condition !== null) {
				$cnd = $this->conditions[$this->and_condition];
				unset($this->conditions[$this->and_condition]);
				$this->conditions = array_merge([$cnd], $this->conditions);
			}
		}
	}

	public function executeNext() {
		$this->limitRowOffset += UserConditionKeeper::$limitRowCount;
		$this->execute(false);
	}

	public function execute($reset_row_offset = true) {
		if ($reset_row_offset) {
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
                  ' . UserConditionKeeper::$usersTable . ' us
                  JOIN ' . UserConditionKeeper::$usersAboutTable . ' ua
                    ON us.id = ua.user
                GROUP BY us.id
                HAVING ';
		$query = $this->getConditionsQuery();
		$query['query'] = $sql . $query['query'];
		if ($with_limit) {
			$query['query'] = $query['query'] . ' 
			LIMIT ' . $this->limitRowOffset . ', ' . UserConditionKeeper::$limitRowCount;
		}

		return $query;
	}

	public function getConditionsQuery() {
		$this->sortConditions();
		$conditions = [];
		foreach ($this->conditions as $cnd) {
			$arr = $cnd[0]->getConditionsQuery();
			$arr['logic'] = $cnd[1];
			$conditions[] = $arr;
		}
		$sql = '';
		$params = [];
		$iteration = 0;
		foreach ($conditions as $cnd) {
			if ($iteration === 0) {
				$sql .= sprintf(' (%s) ', $cnd['query']);
			} else {
				$sql .= sprintf(' %s (%s) ', $cnd['logic'], $cnd['query']);
			}
			$params = array_merge($params, $cnd['params']);
			++$iteration;
		}

		return ['query' => $sql, 'params' => $params];
	}

	public function fetch() {
		if ($this->statement === false) {
			$this->execute();
		}

		return DPDO::fetch($this->statement);
	}

	public function getCount() {
		$query = $this->getQuery(false);
		$cnt = DPDO::executeAndGetFirst(sprintf(' SELECT COUNT(*) AS cnt FROM (%s) ttt', $query['query']), $query['params']);

		return $cnt['cnt'];
	}

}
