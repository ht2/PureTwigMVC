<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JSONCommand
 *
 * @author JamesMullaney
 */
class JSONCommand extends ExtendedCommand{
    //put your code here
    
    protected $json = array();
        
    public function printJSON(){
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo json_encode($this->json);
        exit();
    }
}

?>
