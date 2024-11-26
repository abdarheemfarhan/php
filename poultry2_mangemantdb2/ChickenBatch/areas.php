<?php
include ('../connect.php');

function add_areas(){
    global $con;
    $data =[
        "area_name"=>filterRequest('area_name'),
        "province_id"=>filterRequest('province_id'),

    ];
    insertData($con, 'areas_tab', $data);
}
function select_areas(){
   
  global $con;
  
  echo json_encode( select_query1($con, 'areas_tab',[],[]));

}
function update_areas(){
  global $con;
  $area_id=['area_id'=>filterRequest('area_id')];
  $data =[
    "area_name"=>filterRequest('area_name'),
    "province_id"=>filterRequest('province_id'),
    
];
updateRecord($con, 'city_tab',$data,$area_id);
}
function delete_areas() {
    global $con;
    $area_id =["area_id"=>filterRequest('area_id')];
    deleteRecord($con,'areas_tab',$area_id);
}
switch ($_REQUEST['mask']){
    case "add_areas":add_areas();
    break;
    case "select_areas":select_areas();
    break;
    case "update_areas":update_areas();
    break;
    case "delete_areas":delete_areas();
    break;
} 
?>