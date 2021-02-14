<?php

function updateBlackList(){
    $now=time();
    $sql="DELETE FROM `black_list` WHERE `expires_at`<'$now'";
    $db=new authDB();
    $db->getConnection()->query($sql);
}



updateBlackList();





