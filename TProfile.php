<?php


class TProfile {

    public    static $timer       = array();
    public    static $isLoaded    = false;
    protected static $hostName    = '';

    public static function init(){

        if (!self::$isLoaded){

            self::$hostName = gethostname();

            self::$isLoaded = self::pinbaIsLoaded();

            if (self::$isLoaded && isset($_SERVER['REQUEST_URI'])){
                self::setScriptName($_SERVER['REQUEST_URI']);
            }

        }

        return self::$isLoaded;
    }

    public static function pinbaIsLoaded(){

        if (extension_loaded('pinba')){
            return true;
        }

        return false;
    }

    /**
     * @param $options array
     * @return bool
     */

    public static function timerStart($options){

        if (!is_array($options)){
            return false;
        }

        if (!isset($options['category'])){
            $options['category'] = 'Other';
        }

        if (!isset($options['group'])){
            $options['group'] = 'other::other';
        }

        if (!isset($options['__hostname'])){
            $options['__hostname'] = $_SERVER['HTTP_HOST'];
        }

        if (!isset($options['__server_name'])){
            $options['__server_name'] = self::$hostName;
        }


        $name = self::getTimerName($options);

        if (self::init()){
            self::$timer[$name][] = pinba_timer_start($options);
            return true;
        }

        return false;
    }

    public static function timerStop($options){

        if (self::init()){

            $timerName = self::getTimerName($options);

            if (isset(self::$timer[$timerName])){

                $timer = array_pop(self::$timer[$timerName]);

                return pinba_timer_stop($timer);
            }
        }

        return false;
    }

    public static function setScriptName($scriptName){
        if (self::init()) {
            return pinba_script_name_set($scriptName);
        }
    }

    private static function getTimerName($array){
        asort($array);
        return md5(serialize($array));
    }

}