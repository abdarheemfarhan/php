<?php

function filterRequest($requesttname)
{
    if (isset($_POST[$requesttname])) {
        return htmlspecialchars(strip_tags($_POST[$requesttname]));

    } else {
        return htmlspecialchars(strip_tags($_GET[$requesttname]));
    }
}



function insertData($conn, $table, $data) {
    // إنشاء الأعمدة والقيم للدخول
    $columns = implode(",", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), '?'));
    // بناء الاستعلام
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
   
    // تحضير الاستعلام
    $stmt = $conn->prepare($sql);
   
    if ($stmt === false) {
        die("خطأ في التحضير: " . $conn->error);
    }
   
    // بناء أنواع البيانات المرتبطة بالاستعلام
    $types = str_repeat('s', count($data)); // في هذه الحالة، نفترض أن جميع البيانات هي سلاسل نصية (String)
    // ربط المتغيرات بالقيم
    $stmt->bind_param($types, ...array_values($data));
   
    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        echo  json_encode(array('status'=>'success'));;
    } else {
        echo  json_encode(array('status'=>'fail'));
    }
    // إغلاق البيان
    $stmt->close();
}






function select_query1($conn,$table, $columns = [], $conditions = []) {
    // الاتصال بقاعدة البيانات
    
    // بناء جملة الاستعلام
    $columns_str = empty($columns) ? "*" : implode(", ", $columns);
    $query = "SELECT $columns_str FROM $table";
    $params = [];
    $types = "";

    // إذا كان هناك شروط WHERE
    if (!empty($conditions)) {
        $condition_strings = [];
        foreach ($conditions as $col => $condition) {
            if (is_array($condition)) {
                // إذا كانت شرطًا مع مقارنات متعددة
                $op = $condition['operator']; // مثل '>', '<', '>=', '<='
                $value = $condition['value'];
                $condition_strings[] = "$col $op ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            } else {
                // الشرط بسيط مثل 'col = value'
                $condition_strings[] = "$col = ?";
                $params[] = $condition;
                $types .= is_int($condition) ? "i" : "s";
            }
        }
        $query .= " WHERE " . implode(" AND ", $condition_strings);
    }

    // تحضير الاستعلام
    if ($stmt = $conn->prepare($query)) {
        // إذا كانت هناك شروط، نربط المعاملات
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // تنفيذ الاستعلام
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // إغلاق البيان والاتصال
        $stmt->close();
        $conn->close();

        return $data;

    } else {
        die("Error preparing statement: " . $conn->error);
    }
}




function select_query2($conn,$table, $columns = [], $conditions = []) {
    // الاتصال بقاعدة البيانات
 

    // بناء جملة الاستعلام
    $columns_str = empty($columns) ? "*" : implode(", ", $columns);
    $query = "SELECT $columns_str FROM $table";
    $params = [];
    $types = "";

    // إذا كان هناك شروط WHERE
    if (!empty($conditions)) {
        $condition_strings = [];
        foreach ($conditions as $col => $condition) {
            // افتراض أن الشرط يحتوي على 'operator' و 'value'
            $operator = isset($condition['operator']) ? $condition['operator'] : '=';
            $value = $condition['value'];
           
            // بناء جملة الشرط
            $condition_strings[] = "$col $operator ?";
            $params[] = $value;
            $types .= is_int($value) ? "i" : "s";  // تعيين نوع المعامل بناءً على نوع القيمة
        }
        $query .= " WHERE " . implode(" AND ", $condition_strings);
    }

    // تحضير الاستعلام
    if ($stmt = $conn->prepare($query)) {
        // إذا كانت هناك شروط، نربط المعاملات
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // تنفيذ الاستعلام
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // إغلاق البيان والاتصال
        $stmt->close();
        $conn->close();

        return $data;

    } else {
        die("Error preparing statement: " . $conn->error);
    }
}



function deleteRecord($conn,$table, $conditions) {
    

    // بناء جملة SQL للحذف
    $whereClause = implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($conditions)));

    $sql = "DELETE FROM $table WHERE $whereClause";
    $stmt = $conn->prepare($sql);

    // دمج أنواع المتغيرات وقيمها
    $types = str_repeat("s", count($conditions));
    $stmt->bind_param($types, ...array_values($conditions));

    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        if($stmt->affected_rows>0)
            echo json_encode(array('status'=>'success'));
        else
            echo json_encode(array('status'=>'no record to delete'));
    } else {
        echo json_encode(array('status'=>'faild'));
    }


    $stmt->close();
    $conn->close();
}



function updateRecord($conn,$table, $data, $conditions) {
   

    // بناء جملة SQL للتعديل
    $setPart = implode(" = ?, ", array_keys($data)) . " = ?";
    $whereClause = implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($conditions)));

    $sql = "UPDATE $table SET $setPart WHERE $whereClause";
    $stmt = $conn->prepare($sql);

    // دمج أنواع المتغيرات وقيمها
    $types = str_repeat("s", count($data) + count($conditions));
    $values = array_merge(array_values($data), array_values($conditions));

    $stmt->bind_param($types, ...$values);

    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        if($stmt->affected_rows>0)
            echo json_encode(array('status'=>'success'));
        else
            echo json_encode(array('status'=>'no record to update'));
    } else {
        echo json_encode(array('status'=>'faild'));
    }

    $stmt->close();
    $conn->close();
}


/*
function deleteRecord($conn,$table, $where) {
    

    // بناء جملة SQL للحذف
    $sql = "DELETE FROM $table WHERE farm_id = '$where'";

    // تنفيذ الاستعلام
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    // إغلاق الاتصال
    $conn->close();
}


// echo getData("testtable","name,addrress","name= ?",["asem"])

//echo filterRequest("asem");
function getData($table, $field = "*", $where = null, $values = null): array
{
    global $con;
    $data = [];
    if ($where == null) {
        $stmt = $con->prepare("select $field from $table");
    } else {
        $stmt = $con->prepare("select $field from $table where $where");
    }
    
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();
    if ($count > 0) {
        return ['status' => "success", "count" => "$count", "data" => $data];
    } else {
        return ["status" => "failure", "count" => "$count"];
    }
}
// echo getData("testtable","name,addrress","name= ?",["asem"]);

// insertData("users",{"name"=>"asem","age"=>20});

function insertData($table, $data, $json = false)
{
    global $con;

    foreach ($data as $f => $value) {
        $ins[] = ":" . $f;
    }
    $ins = implode(',', $ins);
    $fields = implode(",", array_keys($data));
    $sql = "insert into $table($fields) values($ins) ";
    // echo $sql;
    $stmt = $con->prepare($sql);
    foreach ($data as $f => $v) {
        $stmt->bindValue(":" . $f, $v);
    }
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json) {
        return ["count" => "$count"];
    } else {
        return $count;
    }

}
// insertData("testtable",["name"=>"asem ali","addrress"=>"alhoban"]);


function printfailure(): void
{
    echo json_encode(['status' => "failure",]);
}

function isvalid($viled)
{
    if (isset($_GET[$viled]) or isset($_POST[$viled])) {
        return true;
    } else
        return false;
}



function execute_query($conn,$table, $columns = [], $values = [], $action = 'SELECT', $conditions = []) {
    // الاتصال بقاعدة البيانات
    

    // بناء جملة الاستعلام بناءً على نوع الإجراء (SELECT, INSERT, UPDATE, DELETE)
    $query = "";
    $types = "";
    $params = [];

    if ($action === 'SELECT') {
        $columns_str = empty($columns) ? "*" : implode(", ", $columns);
        $query = "SELECT $columns_str FROM $table";
       
        // إذا كان هناك شروط WHERE
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", array_map(function($col) {
                return "$col = ?";
            }, array_keys($conditions)));
            $types = str_repeat("s", count($conditions));
            $params = array_values($conditions);
        }

    } elseif ($action === 'INSERT') {
        $columns_str = implode(", ", $columns);
        $placeholders = implode(", ", array_fill(0, count($values), "?"));
        $query = "INSERT INTO $table ($columns_str) VALUES ($placeholders)";
        $types = str_repeat("s", count($values));
        $params = $values;

    } elseif ($action === 'UPDATE') {
        $set_str = implode(", ", array_map(function($col) {
            return "$col = ?";
        }, $columns));
        $query = "UPDATE $table SET $set_str";

        // إذا كان هناك شروط WHERE
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", array_map(function($col) {
                return "$col = ?";
            }, array_keys($conditions)));
            $types = str_repeat("s", count($values) + count($conditions));
            $params = array_merge($values, array_values($conditions));
        }

    } elseif ($action === 'DELETE') {
        $query = "DELETE FROM $table";

        // إذا كان هناك شروط WHERE
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", array_map(function($col) {
                return "$col = ?";
            }, array_keys($conditions)));
            $types = str_repeat("s", count($conditions));
            $params = array_values($conditions);
        }
    }

    // تحضير الاستعلام وتنفيذه
    if ($stmt = $conn->prepare($query)) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        // معالجة نتيجة الاستعلام
        if ($action === 'SELECT') {
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $conn->close();
            
            return $data;
        } else {
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            $conn->close();
            return $affected_rows;
        }
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}
*/
?>