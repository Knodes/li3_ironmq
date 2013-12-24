<?php

namespace li3_ironmq\core;

require_once "libraries/IronCore.class.php";
require_once "libraries/IronMQ.class.php";

use Exception;
use lithium\core\Environment;

use IronMQ;

/**
 * Wrapper class to allow LI3 communication with IronMQ
 */
class Queue extends \lithium\core\StaticObject {

    protected static $ironmq = null;
    protected static $pid = null;
    protected static $config = array(
        'token' => null,
        'projectId' => null,
    );



    /**
     * Return an instance of the IronMQ class.
     *
     * @return IronMQ Instance.
     */
    public static function getInstance()
    {
        // Detect when the PID of the current process has changed (from a fork, etc)
        // and force a reconnect to redis.
        $pid = getmypid();
        if ( self::$pid !== $pid ) {
            self::$ironmq = null;
            self::$pid   = $pid;
        }

        if( !is_null( self::$ironmq ) ) {
            return self::$ironmq;
        }

        foreach( array_keys( self::$config ) as $param ) {
            if( Environment::get( 'ironmq.' . $param ) ) {
                self::$config[$param] = Environment::get( 'ironmq.' . $param );
            }
        }

        if( !( self::$config['token'] && self::$config['projectId'] ) )
        {
            throw new Exception( 'missing IronMQ Configuration', 500 );
        }

        try {
            self::$ironmq = new IronMQ( array(
                'token' => self::$config['token'],
                'project_id' => self::$config['projectId']
            ) );
        } catch( Exception $e ) {
            return null;
        }

        return self::$ironmq;
    }

    /**
     * runs requested method (with arguments) on a ironmq instance
     *
     * @param string $name The name of the method called.
     * @param array $args Array of supplied arguments to the method.
     * @return mixed Return value from IronMQ::call() based on the command.
     */
    public static function run( $name, $args ) {
        $ironmq = static::getInstance();

        try {
            return call_user_func_array( array( $ironmq, $name ), $args );
        } catch( Exception $e ) {
            return false;
        }
    }


    /**
     * Does proxying the method calls
     * @param string $method
     * @param mixed $arguments
     */
    public static function __callStatic( $method, $arguments ) {
        return static::run( $method,$arguments );
    }

}

?>