<?php namespace Indianajone\Applications\Facades;

class ApplicationFacade extends \Illuminate\Support\Facades\Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'appl'; }

}