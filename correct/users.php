<?
class Users {

    private $filters;

    function __construct() {
        $this->filters = new Filters();
    }
    
    public function search($rules) {
        $out = [];

        foreach ((array) $rules['rules'] as $rule) {
            $out[] = array_flip((array) $this->filters->{$rule['method']}($rule['settings']));
        }
        
        if (array_key_exists('children', (array) $rules)) {
            $out[] = array_flip((array) $this->search($rules['children']));
        }
        if (count($out) > 1) {
            switch ($rules['logic']) {
                case 'intersect': return array_keys((array) call_user_func_array('array_intersect_key', $out)); break;
                case 'merge': return array_keys((array) call_user_func_array('array_replace', $out)); break;
            }
        } else {
            return array_keys($out[0]);
        }
    }

}

/*
 * Класс с набором фильтров. 
 * Для расширения набора фильтров необходимо добавить новый метод. 
 * После добавления метода его можно использовать в условиях поиска.
 */

class Filters {
    
    // Настройки соединения с БД
    private $db = [
        'host'      => 'localhost',
        'db'        => 'mailiq',
        'user'      => 'mysql',
        'pass'      => 'mysql',
        'charset'   => 'utf8'
    ];
    
    // Дескриптор соединения с БД
    private $pdo;
    
    // Доступные логические конструкции в SQL запросах
    private $logic = [
        'EQUAL'     => '=',
        'NOT_EQUAL' => '!=',
    ];
    
    function __construct() {
        $this->pdo = new PDO('mysql:host=' . $this->db['host'] . ';dbname=' . $this->db['db'] . ';charset=' . $this->db['charset'], $this->db['user'], $this->db['pass']);
    }

    public function id($filter) {
        $sql = $this->pdo->prepare('SELECT id FROM users WHERE id ' . $this->logic[$filter['logic']] . ' :id');
        $sql->bindParam(':id', $filter['value'], PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function email($filter) {
        $sql = $this->pdo->prepare('SELECT id FROM users WHERE email ' . $this->logic[$filter['logic']] . ' :email');
        $sql->bindParam(':email', $filter['value'], PDO::PARAM_STR);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }

    public function state($filter) {
        $sql = $this->pdo->prepare('SELECT user FROM users_about WHERE item = "state" && value ' . $this->logic[$filter['logic']] . ' :value');
        $sql->bindParam(':value', $filter['value'], PDO::PARAM_STR);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function firstname($filter) {
        $sql = $this->pdo->prepare('SELECT user FROM users_about WHERE item = "firstname" && value ' . $this->logic[$filter['logic']] . ' :value');
        $sql->bindParam(':value', $filter['value'], PDO::PARAM_STR);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function country($filter) {
        $sql = $this->pdo->prepare('SELECT user FROM users_about WHERE item = "country" && value ' . $this->logic[$filter['logic']] . ' :value');
        $sql->bindParam(':value', $filter['value'], PDO::PARAM_STR);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }
    
}
