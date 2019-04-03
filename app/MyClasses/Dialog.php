<?php

namespace App\MyClasses;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

// (new App\MyClasses\Dialog('Olá'))->getBody()

class Dialog {

    private $projectId = 'newagent-b5ba3';
    private $text;
    private $sessionId = '123456';
    private $fulfilmentText;
    private $languageCode = 'pt-BR';
    private $intent;

    function __construct($text) {
        // $this->init($text);
        $this->text = $text;
    }

    public function send() {
        $text = $this->text;

            // new session
        $test = array('credentials' => $this->credentials());
        $sessionsClient = new SessionsClient($test);
        $session = $sessionsClient->sessionName($this->projectId, $this->sessionId ?? uniqid());
        // printf('Session path: %s' . PHP_EOL, $session);
        \Log::info('Session => ' . $session);

        // create text input
        $textInput = new TextInput();
        $textInput->setText($text);
        $textInput->setLanguageCode($this->languageCode);

        // create query input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        // get response and relevant info
        $response = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        $queryText = $queryResult->getQueryText();
        $intent = $queryResult->getIntent();
        $displayName = $intent->getDisplayName();
        $confidence = $queryResult->getIntentDetectionConfidence();
        $fulfilmentText = $queryResult->getFulfillmentText();

        // var_dump($displayName);
        $this->intent = (string) $displayName;

        $this->fulfilmentText = (string) $fulfilmentText;
        // output relevant info
        // print(str_repeat("=", 20) . PHP_EOL);
        // printf('Query text: %s' . PHP_EOL, $queryText);
        // printf('Detected intent: %s (confidence: %f)' . PHP_EOL, $displayName,
        //     $confidence);
        // print(PHP_EOL);
        // printf('Fulfilment text: %s' . PHP_EOL, $fulfilmentText);
        
        $sessionsClient->close();
        return $this;
    }

    public function setSession($session)
    {
        $this->sessionId = $session;
        return $this;
    }

    public function getIntent() 
    {
        return $this->intent;
    }

    public function getBody() {
        return (string) $this->fulfilmentText;
    }

    protected function credentials() {
        return [
            "type"=> "service_account",
            "project_id"=> "newagent-b5ba3",
            "private_key_id"=> "f48e834600f9b26e057eee63539fed1c467b561c",
            "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCtGO4VR3J3jYYd\nvQMvOhj4lDWJ3nwr2VKsX1EpNdlkSFcGhXWjNyRyI11P5aY9tRb9utbxG4CkwqYe\nZ2wFb5zaEAxB18oxp1/e01A09Js008CDetkZP2PmssvW4OdEDiNLGszFbutwfrVQ\n9fL/me3YTNqQqFqpT7mm8/iFtimNZt+eKlKPvaQHyUiy5tFktvnTYkvDBgk2QLJL\niF7cgMeUo56l+22aIz/Retustnx6gNVADQgfyRhqpnbjw/0GiwmDP4crLwJJVbiG\n62c5HYMK/n8tDEaopXjOAwkO7aGC75BfcaiHLe2zQpIlXQidOqt8Bckvkx9PS1z4\nN1CVC41fAgMBAAECggEAAUMQquUMQTGt8m+OicloC//nBNKAOac29a/1A1KAlQn8\nYHBlDPb5wNjf9RVc74KirUZ8cXuPpfCpWpVkcZ6Aftjl55QvX2QZ/258Kjar6855\nzwhi2gISGpXfhV+6yYGhrj1+x322IVBuX/qH3M2X5B82hLffGVbnqP9XjRoCSYtp\n9ifpsPHUsFviskWC5fhmC6SM87CmECz53cq7LkuXVVsb9n+iehR0OZf6QvcFlz3q\n3wR0FZFoDBMTk3/yxDl5eMjMTqjNvwD0eZuqHKTgrFGJstwVjsv/eSqtNtQobBtc\nZ/XUx3VMr5pYhasWv+2j96ZjIHMiOER69oBp/9gQ2QKBgQDxBbR/soFd9w9eTW5Q\n58PopGhb8ZyIe2YlrgB6PVnC6ElcD3CtF5qrLPgGdjbZhda3ELJPzU0iwwqq7tMS\n+W8GOAnroZR54jvRjWX93dPj4LwKqqx3DGi1ezu2lyijD0T3dbdY2RQCqv/rpxZm\nIVxhZFBcfCazQv3CYeS5iKK1ywKBgQC32qTXOhNtuB7UIWvfmbkWFzB7ox6f5la4\n/vBHg6C78QSTzzrTTuulOf6tfPa2bJ+OTpPjR+8lZ1rU+ISKzW+LWkf9V4BiJLdh\nzB5eXksqlFcFLHFgH2wW0XdJFrMSPcZl10bT3Ww+xldfOcvSezsJbZEA4Vyh8qOH\nM4kUSOs0PQKBgQCtXkIwnJErqvwWBDJ25c9ot2INyOSk08ZtEhVr2FeJuJaULtMI\n3vK1cFpUI5Jes0P4WH06o915RLyWqcWQX3V6DrMsGAT6Cz6mBES58GdrAgugzJXT\n6EwlRqh0NZTYfbJJIhapRTR8ms4NjxmwiwytTX/0lqUryuNHgC0LO/p2MwKBgDF/\nX9nB/PERHNunk89DJ51W6OqgY+JQtRBhMndObLusi7rvk7rICJEXKAW1GwbJ/7rk\nVNRfzXaYeM/ViHmGDX3K9I60PBAwKl7eAV8Oq9Xu0e7GpE9opkUOZ7r5rYQ/tWY9\nqwfnGPldlLBCcylbm+1R3jQKeFHxbS09Jq6bYxdRAoGBAMMKqvky4s+W94KsCJes\nk2OUz+6Dj2/TzxHXC6UB8ShjhlwVC4ij7FuSx3VoJEEac2nihqXK6uL6X/J0CERS\nNeus1Fm/wKLwukjfsLqQpnx92JjvzOk+rxrJKLv/2n8JvJgodGC8ylK9gGkMdSe9\nwS250nk2lRM97DDm6GzPS7cW\n-----END PRIVATE KEY-----\n",
            "client_email"=> "dialogflow-vohded@newagent-b5ba3.iam.gserviceaccount.com",
            "client_id"=> "100775228648561704530",
            "auth_uri"=> "https=>//accounts.google.com/o/oauth2/auth",
            "token_uri"=> "https=>//oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/dialogflow-vohded%40newagent-b5ba3.iam.gserviceaccount.com"
        ];
    }
}