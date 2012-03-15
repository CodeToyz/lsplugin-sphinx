<?php

/* -------------------------------------------------------
 *
 *   CodeToyz Developer Team
 *   Author: Yuriy Sergeev aka randomtoy
 *   Visit us: http://codetoyz.ru
  ---------------------------------------------------------
 */

if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginSphinx extends Plugin {

    //protected $aInherits = array(
        //'action' => array('ActionSearch'),
    //);

      protected $aDelegates=array(
	  'action' =>array('ActionSearch'),
      'template'=>array(
      'actions/ActionSearch/results.tpl'
      
      ),
      );

    public function Activate() {
        return true;
    }

    public function Init() {
    }

}

?>
