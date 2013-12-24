#li3_ironmq

[IronMQ](http://www.iron.io/mq) Wrapper for LI3

##Installation

Get the library code:

    $ cd /path/to/app/libraries
    $ git clone https://github.com/Knodes/li3_ironmq.git

Make sure it's added on `app/config/bootstrap/libraries.php` with the path included:

    Libraries::add('li3_ironmq', array(
      'includePath' => true,
    ));
    

##Configuration
Make sure your environments have the credentials for IronMQ setup as the params:
- ironmq.token
- ironmq.projectId

##Usage
li3\_ironmq supplies you with a static class (li3_ironmq\core\Queue) that's a basic wrapper to all methods the [IronMQ PHP Client](https://github.com/iron-io/iron_mq_php) accepts.

On top of that it manages the instance of the IronMQ object automatically, so there's no need to create it manually.


##Example

    use li3_ironmq\core\Queue;
    
    ...
    
    $res = Queue::postMessage( 'MY_QUEUE_NAME', 'Hello World!' );
    
    ...
    
    $message = Queue::getMessage( 'MY_QUEUE_NAME' );
    
    
    
