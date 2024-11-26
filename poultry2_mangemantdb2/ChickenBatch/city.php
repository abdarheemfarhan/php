<?php
include ('../connect.php');

function add_city(){
    global $con;
    $data =[
        "city_name"=>filterRequest('city_name'),
    ];
    insertData($con, 'city_tab', $data);
}
function select_city(){
   
  global $con;
  
  echo json_encode( select_query1($con, 'city_tab',[],[]));

}
function update_city(){
  global $con;
  $city_id=['city_id'=>filterRequest('city_id')];
  $data =[
    "city_name"=>filterRequest('city_name'),
    
];
updateRecord($con, 'city_tab',$data,$city_id);
}
function delete_city() {
    global $con;
    $city_id =["city_id"=>filterRequest('city_id')];
    deleteRecord($con,'city_tab',$city_id);
}
switch ($_REQUEST['mask']){
    case "add_city":add_city();
    break;
    case "select_city":select_city();
    break;
    case "update_city":update_city();
    break;
    case "delete_city":delete_city();
    break;
} 
?>