<?php

namespace li3_ironmq\extensions\command;

use Exception;
use lithium\core\Environment;
use li3_ironmq\core\Queue;

class Listener extends \lithium\console\Command {

    public function run( $interval = 5, $queueName=null )
    {
        if( !is_numeric( $interval ) )
        {
            $this->out( "IronMQ Listener: {:error}Please supply a numeric value as interval (got $interval).{:end}" );
            return false;
        }

        if( $interval < 0.1 )
        {
            $this->out( "IronMQ Listener: {:error} Interval has to be at least 0.1 (got $interval).{:end}" );
            return false;
        }

        if( empty( $queueName ) )
        {
            $this->out( "IronMQ Listener: {:error}Please supply a queue name to listen to.{:end}" );
            return false;
        }

        $token = Environment::get( 'ironmq.token' );
        $project_id = Environment::get( 'ironmq.projectId' );
        if( !( $token && $project_id ) )
        {
            $this->out( "IronMQ Listener: {:error}Missing credentials for checking queue.{:end}" );
            return false;
        }

        $this->out( "IronMQ Listener: Setting up listener for queue '$queueName'" );

        $qInfo = Queue::getQueues( );
        if( empty( $qInfo ) )
        {
            $this->out( "IronMQ Listener: {:error}Could not get list of queues. Check credentials{:end}" );
            return false;
        }
        else
        {
            $this->out( "IronMQ Listener: {:success}Verified IronMQ credentials{:end}" );
        }

        while( true )
        {
            $this->out( "IronMQ Listener: checking queue '$queueName'" );
            $m = Queue::getMessage( $queueName );

            if( !empty( $m ) )
            {
                $this->out( "IronMQ Listener: found message $m->id on queue '$queueName'" );

                $this->handle( $m->body , $queueName );
                $this->out( "IronMQ Listener: handled message $m->id on queue '$queueName'" );

                Queue::deleteMessage( $queueName, $m->id );
                $this->out( "IronMQ Listener: deleted message $m->id on queue '$queueName'" );

            }

            usleep( $interval * 1000000 );
        }

    }

    /**
     * example of how to handle received messages
     * most likely you'd want to inherit from this class and implement your own handle method
     * @param  string $payload payload of message as received in message->body
     * @param  string $queue the name of the current queue
     */
    protected function handle( $payload , $queue)
    {
        $this->out( $payload );
        return;
    }


}
