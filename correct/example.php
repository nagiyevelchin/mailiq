<pre>
<?
include 'users.php';
$users = new Users();


$filter3 = [
    'logic' => 'intersect',
    'rules' => [
        [
            'method' => 'state',
            'settings' => [
                'logic' => 'EQUAL',
                'value' => 'active'
            ]
        ]
    ],
    'children' => [
        'logic' => 'merge',
        'rules' => [
            [
                'method' => 'country',
                'settings' => [
                    'logic' => 'EQUAL',
                    'value' => 'UK'
                ]
            ],
            [
                'method' => 'firstname',
                'settings' => [
                    'logic' => 'EQUAL',
                    'value' => 'Elchin'
                ]
            ]
        ]
    ]
];

print_r($filter3);

$memory = memory_get_peak_usage(true);
echo "real: ".($memory/1024/1024)." MiB\n\n";
$begin = microtime(true);

$users->search($filter3);

$end = microtime(true);
print "\nExecution time: " . ($end - $begin) . " sec\n";
$memory2 = memory_get_peak_usage(true);
echo "memory used: ".(($memory2-$memory1)/1024/1024)." MiB\n\n";
