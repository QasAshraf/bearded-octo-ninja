<?php
namespace BON\Route;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SMSIncomingController implements ControllerProviderInterface{

    public function connect(Application $app) 
    {
            $controllers = $app['controllers_factory'];

            $controllers->post('/', function(Request $request) 
	            { 
	            	
	            	$content = decodeContent($request->get('content'));
                    $from = decodeContent($request->get('from'));
                    $clockwork_request = array(
                        'content' => $content,
                        'from' => $from
                    );
                    $context = new React\ZMQ\Context();
                    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
                    $socket->connect("tcp://109.109.137.94:5555");

                    $socket->send(json_encode($clockwork_request));

	                return new Response('Cheers Clockwork!', 200); 
	            }
	        ); 

        return $controllers;

    }

    protected function decodeContent($string)
    {
    	return explode(" ", urldecode($string), 2);
    }
}