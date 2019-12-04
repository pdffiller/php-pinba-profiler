<?php
namespace Pdffiller\PinbaProfiler;

class PinbaProfiler {

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

        if (self::init()){

            $options = self::noramlizeOptions($options);
            $name    = self::getTimerName($options);

            //TLog::write('start: '.$name. 'dedug:'.json_encode(debug_backtrace()), 'TProfile.log');

            self::$timer[$name][] = pinba_timer_start($options);

            return true;
        }

        return false;
    }

    /**
     * @param string $title Ex: Keen.IO
     * @param string $methodName method name must be delimited with :: Ex. Keen::addEvents
     * @return array
     */
    public static function getRequiredOptions($title, $methodName, $additionalOptions = []) {
        $pinbaTagsTemplate = [
            'category' => $title,
            'group' => $methodName
        ];

        return array_merge($pinbaTagsTemplate,$additionalOptions);
    }

    /**
     * @param $options
     * @param integer $timeInMilliseconds 1sec = 1000ms
     * @return bool
     */
    public static function timerAndStop($options, $timeInMilliseconds) {
        if (self::init()){
            return pinba_timer_add($options, $timeInMilliseconds);
        }

        return false;
    }

    public static function timerStop($options){

        if (self::init()){

            $options   = self::noramlizeOptions($options);
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

    public static function noramlizeOptions($options){

        if (!isset($options['category'])){
            $options['category'] = 'Other';
        }

        if (!isset($options['group'])){
            $options['group'] = 'other::other';
        }

        if (!isset($options['__hostname'])){

            if (!isset($_SERVER['HTTP_HOST'])){
                $options['__hostname'] = self::$hostName;
            }else {
                $options['__hostname'] = $_SERVER['HTTP_HOST'];
            }
        }

        if (!isset($options['__server_name'])){
            $options['__server_name'] = self::$hostName;
        }

        return $options;
    }

    /**
     * Useful when you need to send request data to the server immediately (for long running scripts)
     *
     * @param string|null $scriptName
     * @param int|null $flag
     */
    public static function flush($scriptName = null, $flag = PINBA_FLUSH_ONLY_STOPPED_TIMERS)
    {
        if (self::init()) {
            pinba_flush($scriptName, $flag);
        }
    }
}
