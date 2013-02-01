<?php
/**
 *
 * @author JamesMullaney
 */
class HTMLTest extends HTMLCommand {
    
    public function execute( INotification $notification) {
        parent::execute($notification);
        
        $this->includeHandler->addTypes('jquery', 'bootstrap');
        
        switch( $this->command ){
            default:
                $this->viewHome();
                break;
        }
    }
    
    private function viewHome(){
        $this->page_title = "HT2 Test";
        
        echo $this->printHTML('test.html.twig' );
    }
}

?>
