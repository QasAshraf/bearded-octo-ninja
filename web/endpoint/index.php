<?php
require_once('phpws/websocket.client.php');

new SMSInterceptor();

class SMSInterceptor {

    protected $socketServer = '109.109.137.94:8080';

    public function __construct()
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        switch($_SERVER['REQUEST_METHOD'])
        {
            case 'POST':
                $this->post($this->cleanInputs($_POST));
                break;
            case 'GET':
            case 'PUT':
            case 'DELETE':
            default:
                $this->response('Invalid method', 405);
                break;

        }
    }

    private function validateMessageContent($content)
    {
        // Validate that date, only accept certain commands
        $message = explode(' ', $content);

        // Check only command + argument
        if(count($message) < 0 && count($message) > 4)
            return false;

        $command = $message[0];

        // Check it's a valid command
        $possibleCommands = array('join', 'move', 'leave');
        if(!in_array($command, $possibleCommands))
            return false;

        return true;
    }

    // Get the data we need, then send it via JSON to the web sockets server.
    private function post($data)
    {
        if(!$this->validateMessageContent(strtolower($data['content'])))
            return $this->response('Message content not valid for our server, but thanks anyway.');

        // Only interested in two fields: content, from.
        $clockwork_request = array(
            'operation' => 'SMS',
            'type' => 'incoming',
            'recipient' => $data['to'],
            'message' => strtolower($data['content']), // TODO: Cleanup the input, at the moment we're just chuking it through
            'sender' => $data['from'],
            'id' => $data['msg_id']
        );

        $msg = WebSocketMessage::create(json_encode($clockwork_request));
        $client = new WebSocket('ws://' . $this->socketServer);
        $client->open();
        $client->sendMessage($msg); // Fire off message to socket server.
        $client->close();

        return  $this->response('Message passed onto the socket server');
    }

    // Clean up data BEFORE we use it
    private function cleanInputs($data)
    {
        $clean_input = Array();

        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }

        return $clean_input;
    }

    private function explainStatus($errorCode)
    {
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');

        // Decipher http code
        return ($status[$errorCode]) ? $status[$errorCode] : $status[500];
    }

    private function response($data, $code = 200)
    {
        header("HTTP/1.1 " . $code . " " . $this->explainStatus($code));
        return json_encode($data);
    }

} 