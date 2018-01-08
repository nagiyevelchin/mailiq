# Универсальный класс поиска пользователей

**(ID = 1000) ИЛИ (Страна != Россия)**

    $userCondition = new UserConditionKeeper();
    $userCondition->setId(1000, '=');
    $userCondition->setCountry('Россия', '!=', 'OR');
Результат 

    SELECT
      us.*,
      MAX(IF(ua.item = 'country', ua.value, '')) country,
      MAX(IF(ua.item = 'firstname', ua.value, '')) firstname,
      MAX(IF(ua.item = 'state', ua.value, '')) state
    FROM
      users us
      JOIN users_about ua
        ON us.id = ua.user
    GROUP BY us.id
    HAVING  us.id = :id_000000000e09ce950000000080dc0097 OR 
            country != :country_000000000e09ce950000000080dc0097 
    LIMIT 0,100;
    
    [id_000000000e09ce950000000080dc0097] => 1000
    [country_000000000e09ce950000000080dc0097] => Россия



----------------
**(Страна = Россия) И (Состояние пользователя != active)**

    $userCondition2 = new UserConditionKeeper();
    $userCondition2->setCountry('Россия', '=');
    $userCondition2->setState('active', '!=');

Результат 

    SELECT
      us.*,
      MAX(IF(ua.item = 'country', ua.value, '')) country,
      MAX(IF(ua.item = 'firstname', ua.value, '')) firstname,
      MAX(IF(ua.item = 'state', ua.value, '')) state
    FROM
      users us
      JOIN users_about ua
        ON us.id = ua.user
    GROUP BY us.id
    HAVING  country = :country_000000000e09ce940000000080dc0097 AND 
            state != :state_000000000e09ce940000000080dc0097 
    LIMIT 0,100;
    
    [country_000000000e09ce940000000080dc0097] => Россия
    [state_000000000e09ce940000000080dc0097] => active

----------------
**((Страна != Россия) ИЛИ (Состояние пользователя = active)) И (E-Mail = user@domain.com)**

    $userCondition3 = new UserConditionKeeper();
    $userCondition3->setState('active', '=');
    $userCondition3->setCountry('Россия', '!=', 'OR');
    
    $userCondition4 = new UserConditionKeeper();
    $userCondition4->setEmail('user@domain.com', '=');
    
    $condition = new UserComplexConditionKeeper();
    $condition->setCondition($userCondition3);
    $condition->setCondition($userCondition4);

Результат

    SELECT
      us.*,
      MAX(IF(ua.item = 'country', ua.value, '')) country,
      MAX(IF(ua.item = 'firstname', ua.value, '')) firstname,
      MAX(IF(ua.item = 'state', ua.value, '')) state
    FROM
      users us
      JOIN users_about ua
        ON us.id = ua.user
    GROUP BY us.id
    HAVING  ( state = :state_000000000e09ce930000000080dc0097 OR 
              country != :country_000000000e09ce930000000080dc0097
             ) AND ( 
              us.email = :email_000000000e09ce920000000080dc0097
             )  
	LIMIT 0, 100
	
	[state_000000000e09ce930000000080dc0097] => active
    [country_000000000e09ce930000000080dc0097] => Россия
    [email_000000000e09ce920000000080dc0097] => user@domain.com

----------------
**(((Страна != Россия) ИЛИ (Состояние пользователя = active)) И (E-Mail = user@domain.com)) ИЛИ (Role != user)**

    $userCondition3 = new UserConditionKeeper();
    $userCondition3->setState('active', '=');
    $userCondition3->setCountry('Россия', '!=', 'OR');
    
    $userCondition4 = new UserConditionKeeper();
    $userCondition4->setEmail('user@domain.com', '=');
    
    $condition = new UserComplexConditionKeeper();
    $condition->setCondition($userCondition3);
    $condition->setCondition($userCondition4);
    
    $userCondition5 = new UserConditionKeeper();
    $userCondition5->setRole('user', '!=');
    
    $condition2 = new UserComplexConditionKeeper();
    $condition2->setCondition($condition);
    $condition2->setCondition($userCondition5,'OR');

Результат

    SELECT
      us.*,
      MAX(IF(ua.item = 'country', ua.value, '')) country,
      MAX(IF(ua.item = 'firstname', ua.value, '')) firstname,
      MAX(IF(ua.item = 'state', ua.value, '')) state
    FROM
      users us
      JOIN users_about ua
        ON us.id = ua.user
    GROUP BY us.id
    HAVING  ( 
			    (state = :state_00000000558bdddd00000000fcfeacb7 OR 
			    country != :country_00000000558bdddd00000000fcfeacb7)
			    AND
			    (us.email = :email_00000000558bdddc00000000fcfeacb7) 
		    ) OR (
			     us.role != :role_00000000558bddd100000000fcfeacb7
		    )
    LIMIT 0, 100
    
    [state_00000000558bdddd00000000fcfeacb7] => active
    [country_00000000558bdddd00000000fcfeacb7] => Россия
    [email_00000000558bdddc00000000fcfeacb7] => user@domain.com
    [role_00000000558bddd100000000fcfeacb7] => user 
