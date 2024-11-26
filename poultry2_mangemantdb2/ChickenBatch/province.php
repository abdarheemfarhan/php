<?php
include ('../connect.php');

function add_province(){
    global $con;
    $data =[
        "province_name"=>filterRequest('province_name'),
        "city_id"=>filterRequest('city_id'),

    ];
    insertData($con, 'province_tab', $data);
}
function select_province(){
   
  global $con;
  
  echo json_encode( select_query1($con, 'province_tab',[],[]));

}
function update_province(){
  global $con;
  $province_id=['province_id'=>filterRequest('province_id')];
  $data =[
    "city_name"=>filterRequest('city_name'),
    "city_id"=>filterRequest('city_id'),
    
];
updateRecord($con, 'city_tab',$data,$province_id);
}
function delete_province() {
    global $con;
    $province_id =["province_id"=>filterRequest('province_id')];
    deleteRecord($con,'province_tab',$province_id);
}
switch ($_REQUEST['mask']){
    case "add_province":add_province();
    break;
    case "select_province":select_province();
    break;
    case "update_province":update_province();
    break;
    case "delete_province":delete_province();
    break;
} 
?>