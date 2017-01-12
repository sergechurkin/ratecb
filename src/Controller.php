<?php

namespace ratecb;

use ratecb\Model;
 
class Controller {
/*
 * sergechurkin/ratecb
*/
    public function run() {
        $model = new Model();
        $model->createPage();
        $model = null;
    }                               
}

