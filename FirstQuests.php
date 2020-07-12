<?php
/*
 * 1 Задание. Была дана оптимизация одной из таблиц вместо типа int на поле birth_date удобнее использовать тип TIMESTAMP ибо он для того и придуман.
 * SELECT users.id,name, COUNT(phone) from users LEFT JOIN phone_numbers ON phone_numbers.user_id=users.id WHERE users.gender=2 AND users.birth_date BETWEEN CURRENT_TIMESTAMP() - INTERVAL 22 YEAR AND CURRENT_TIMESTAMP()-INTERVAL 18 YEAR   GROUP BY users.id;
 */
// 2 Задание. Вля корректного вывода ампперсантов, при сохранениии валидности урла, выполнять в консоли.
function getUrl(string $url):string{
    $res=parse_url($url);
    $output=[];
    parse_str($res["query"],$output);
    $output=array_filter($output,function ($el){
        return $el!=3;
    });
    asort($output,SORT_NUMERIC);

    array_walk($output,function ($v,$k)use(&$result){
        $result.="$k=$v&";
    });

     return $res['scheme'].'://'.$res['host'].'/?'.(empty($output)?'':$result).'url='.urlencode($res["path"]);



}

/*
 * 3 Задание опредеелено в соответствующей деректории в нем используется паттерн фабрика (интерфейс), так как типов документов может быть много.
 * Dсе определено в виде абстракиных классов и интерфейсов, так как конкретная реализация должна быть определена позднее в соответствии с заданием
 *
 * */

//----------

/*
 * В 4 задании ниже представлены рефактор и исправление основных багов и уязвимостей
 *
 * В первончальной версии были следующие уязвимости:
 * 1. Отсутствии экранирования специальных символов sql (привет бородатый анекдот про роберт брось таблицу).
 * 2. Отсутсвие валидации параметра забираемого из гет запроса (опять таки без преведения к инту можно вдуть sql инъекцию в параметр).
 * */
function getConnect(array $options):mysqli{
    return $con=mysqli_connect($options['host'], $options['user'], $options['pass'], $options['db']);
}

function getData(string $query,string $separator):array {
    return explode($separator,$query);
}

function closeConn(mysqli $db){
    mysqli_close($db);
}

function execQuery(mysqli $db,string $sqlString):mysqli_result{
    $validSql=mysqli_real_escape_string($db,$sqlString);
    return mysqli_query($db, $validSql);
}

function validateParam(array $schema):array {
    $res=[];
    foreach ($schema as $value){
       $res[$value['name']]=($value['func'])($value['param']);
    }
    return $res;
}

function fetch(mysqli_result $sql,array &$data) {

    while($obj = mysqli_fetch_object($sql)){
        $data[$obj->id] = $obj->name;
    }

}

/** Выполнять строго в браузере
 * @param string $user_ids list of user ids separate by ,
 * @return array array of pair id name for users table
 */
function load_users_data(string $user_ids) {
    $user_ids = getData($user_ids, ',');
    $db = getConnect(["host"=>"localhost", "user"=>"root", "pass"=>"","db"=>"sobes_db"]);
    $data=[];
    foreach ($user_ids as $user_id) {
        $id=validateParam([
            ['name'=>'id','func'=>function($param){return (int)$param;},'param'=>$user_id]
        ])['id'];
        $sql = execQuery($db, "SELECT * FROM users WHERE id=$id");
        fetch($sql,$data);

    }
    closeConn($db);
    return $data;
}
if(php_sapi_name()=='cli') {
    echo getUrl("https://www.somehost.com/test/index.html?param1=4&param2=3&param3=2&param4=1&param5=3");

}
else {
// Как правило, в $_GET['user_ids'] должна приходить строка
// с номерами пользователей через запятую, например: 1,2,17,48
    if (isset($_GET['user_ids'])) {
        $data = load_users_data($_GET['user_ids']);

        foreach ($data as $user_id => $name) {
            echo "<a href=\"/show_user.php?id=$user_id\">$name</a><br/>";
        }
    }
}
