<?php
class ScaffoldController extends Utilities_Scaffolding {
    public function init() {
        $this->initScaffolding(new Databases_Products());
    }
}
