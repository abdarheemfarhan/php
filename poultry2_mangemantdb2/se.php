<?php

function select_project(){
   
    global $con;

    echo json_encode(select_query1($con,'project_tab',[],[]));
   // echo json_encode( select_query1($con, 'project_tab',[],[]));
  
  }
  
?>
 