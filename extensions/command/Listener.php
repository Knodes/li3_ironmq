<?php

namespace li3_ironmq\extensions\command;

use Exception;
use lithium\core\Environment;
use li3_ironmq\core\Queue;

class Listener extends \lithium\console\Command {

    public function run( $queueName=null, $interval = 5)
    {
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

                $this->handle( $m->body );
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
     */
    protected function handle( $payload )
    {
        $this->out( $payload );
        return;
    }


}