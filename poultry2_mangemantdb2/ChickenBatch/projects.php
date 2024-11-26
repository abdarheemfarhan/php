<?php
include ('../connect.php');

function add_project(){
    global $con;
    $data =[
        "project_name"=>filterRequest('project_name'),
        "city_id"=>filterRequest('city_id'),
        "prov_id"=>filterRequest('prov_id'),
        "area_id"=>filterRequest('area_id'),
        "create_dare"=>filterRequest('create_dare'),
        "details"=>filterRequest('details'),
    ];
    insertData($con, 'project_tab', $data);
}
function select_project(){
   
  global $con;
  
  echo json_encode( select_query1($con, 'project_tab',[],[]));

}
function update_project(){
  global $con;
  $aa=['project_id'=>filterRequest('project_id')];
  $data =[
    "project_name"=>filterRequest('project_name'),
    "city_id"=>filterRequest('city_id'),
    "prov_id"=>filterRequest('prov_id'),
    "area_id"=>filterRequest('area_id'),
    "create_dare"=>filterRequest('create_dare'),
    "details"=>filterRequest('details'),
];
updateRecord($con, 'project_tab',$data,$aa);
}
function delete_project() {
    global $con;
    $project_id =["project_id"=>filterRequest('project_id')];
    deleteRecord($con,'project_tab',$project_id);
}
switch ($_REQUEST['mask']){
    case "add_project":add_project();
    break;
    case "select_project":select_project();
    break;
    case "update_project":update_project();
    break;
    case "delete_project":delete_project();
    break;
} 
?>