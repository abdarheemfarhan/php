<?php
include ('../connect.php');

function add_supplier(){
    global $con;
    $data =[
        "supp_name"=>filterRequest('supp_name'),
        "supp_phone"=>filterRequest('supp_phone'),
        "supp_email"=>filterRequest('supp_email'),
        "city_id"=>filterRequest('city_id'),
        "prov_id"=>filterRequest('prov_id'),
        "area_id"=>filterRequest('area_id'),
        
    ];
    insertData($con, 'suppliers_tab', $data);
}
function select_supplier(){
   
  global $con;
  
  echo json_encode( select_query1($con, 'suppliers_tab',[],[]));

}
function update_supplier(){
  global $con;
  $aa=['supp_id'=>filterRequest('supp_id')];
  $data =[
    "supp_name"=>filterRequest('supp_name'),
    "supp_phone"=>filterRequest('supp_phone'),
    "supp_email"=>filterRequest('supp_email'),
    "city_id"=>filterRequest('city_id'),
    "prov_id"=>filterRequest('prov_id'),
    "area_id"=>filterRequest('area_id'),
];
updateRecord($con, 'suppliers_tab',$data,$aa);
}
function delete_supplier() {
    global $con;
    $supp_id =["supp_id"=>filterRequest('supp_id')];
    deleteRecord($con,'suppliers_tab', $supp_id);
}
switch ($_REQUEST['mask']){
    case "add_supplier":add_supplier();
    break;
    case "select_supplier":select_supplier();
    break;
    case "update_supplier":update_supplier();
    break;
    case "delete_supplier":delete_supplier();
    break;
} 
?>