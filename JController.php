<?php

class JController extends CController
{    
    /**
     * Render json template
     * @param string $view
     * @param type $data
     * @param type $return
     * @return type 
     */
    public function renderJSON($view, $data = null, $return = false)
    {
        $this->layout = false;
        $view .= '_json';
        if (($view = $this->getViewFile($view)) === false) {
            echo CJSON::encode($data);
        } else {
            if ($data !== null) {
                JB::setVar($data);
            }
            return parent::render($view, $data, $return);            
        }        
    }
    
    public function render($view, $data = null, $return = false)
    {
        if (strtolower(Yii::app()->getRequest()->getParam('_format')) === 'json') {
            return $this->renderJSON($view, $data, $return);
        }
        return parent::render($view, $data, $return);
    }
}