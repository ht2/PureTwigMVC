<?php
/**
 *
 * @author JamesMullaney
 */
class APITest extends JSONCommand {
    
    public function execute( INotification $notification) {
        parent::execute($notification);
        
        switch( $this->command ){
            default:
                $this->viewHome();
                break;
        }
         
        
        $this->printJSON();        
    }
    
    private function viewHome(){
        $this->json['hello'] = "world";
    }
}

?>
