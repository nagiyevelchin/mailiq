https://github.com/nagiyevelchin/mailiq/blob/master/correct/example.php

    Array
    (
        [logic] => intersect
        [rules] => Array
            (
                [0] => Array
                    (
                        [method] => state
                        [settings] => Array
                            (
                                [logic] => EQUAL
                                [value] => active
                            )
                    )
            )
        [children] => Array
            (
                [logic] => merge
                [rules] => Array
                    (
                        [0] => Array
                            (
                                [method] => country
                                [settings] => Array
                                    (
                                        [logic] => EQUAL
                                        [value] => UK
                                    )
                            )
                        [1] => Array
                            (
                                [method] => firstname
                                [settings] => Array
                                    (
                                        [logic] => EQUAL
                                        [value] => Elchin
                                    )
                            )
                    )
            )
    )
    real: 0.5 MiB
    
    Execution time: 18.674900054932 sec
    memory used: 101.25 MiB


----------
https://github.com/nagiyevelchin/mailiq/blob/master/test.php

    Array
    (
        [query] => SELECT
                      us.*,
                      MAX(IF(ua.item = 'country', ua.value, '')) country,
                      MAX(IF(ua.item = 'firstname', ua.value, '')) firstname,
                      MAX(IF(ua.item = 'state', ua.value, '')) state
                    FROM
                      users us
                      JOIN users_about ua
                        ON us.id = ua.user
                    GROUP BY us.id
                    HAVING  ( state = :state_0000000070c7e4bb00000000bc4e6794)  AND ( ( country = :country_0000000070c7e4ba00000000bc4e6794)  OR ( firstname = :firstname_0000000070c7e4bd00000000bc4e6794) ) 
        [params] => Array
            (
                [state_0000000070c7e4bb00000000bc4e6794] => active
                [country_0000000070c7e4ba00000000bc4e6794] => UK
                [firstname_0000000070c7e4bd00000000bc4e6794] => Elchin
            )
    
    )
    real: 0.5 MiB
    
    
    Execution time: 14.24952507019 sec
    memory used: 36 MiB
