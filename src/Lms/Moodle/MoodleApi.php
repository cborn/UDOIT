<?php

namespace App\Lms\Moodle;

use App\Lms\LmsResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class MoodleApi {

    protected $session;
    protected $baseUrl;
    protected $httpClient;

    public function __construct($baseUrl, $apiToken)
    {
        $this->httpClient = HttpClient::create([
            'headers' => ["Authorization: Bearer " . $apiToken],
        ]);
        $this->baseUrl = $baseUrl;
    }

    /**
     * API call GET
     *
     * @param string $url
     * @param array $options
     * @param integer $perPage
     * 
     * @return LmsResponse
     */
    public function apiGet($url, $options = [], $lmsResponse = null)
    {      
        if (!$lmsResponse) {
            $lmsResponse = new LmsResponse();
        }

        if (strpos($url, $this->baseUrl) === false) {
            $url = "https://{$this->baseUrl}/moodle/api/{$url}";
        }

        $response = $this->httpClient->request('GET', $url, $options);
        $lmsResponse->setResponse($response);
        $content = $lmsResponse->getContent();
        
        if (!empty($content['errors'])) {
            foreach ($content['errors'] as $error) {
                $lmsResponse->setError($error['message']);
            }
        }

        return $lmsResponse;
    }

    // public function apiPost($url, $options, $sendAuthorized = true) 
    // {
    //     $lmsResponse = new LmsResponse();

    //     if (strpos($url, 'https://') === false) {
    //         $url = "https://{$this->baseUrl}/d2l/api/{$url}";
    //     }

    //     if ($sendAuthorized) {
    //         $response = $this->httpClient->request('POST', $url, $options);
    //     }
    //     else {
    //         $client = HttpClient::create();
    //         $response = $client->request('POST', $url, $options);
    //     }
    //     $lmsResponse->setResponse($response);

    //     $content = $lmsResponse->getContent();
    //     if (!empty($content['errors'])) {
    //         // TODO: If error is invalid token, refresh API token and try again 

    //         foreach ($content['errors'] as $error) {
    //             $lmsResponse->setError($error['message']);
    //         }
    //     }

    //     return $lmsResponse;
    // }

    /**
     * Posts a file to Moodle
     *
     * @param string $url
     * @param array $options
     * @param string $filepath
     * 
     * @return LmsResponse
     */
    public function apiFilePut($url, $filepath)
    {
        $formFields = [];

        $formFields['file'] = DataPart::fromPath($filepath);
        $formData = new FormDataPart($formFields);

        $headers = $formData->getPreparedHeaders()->toArray();
        $headers[] = 'Content-Length: ' . mb_strlen($formData->bodyToString(), '8bit');

        $fileResponse = $this->apiPut($url, [
            'headers' => $headers,
            'body' => $formData->bodyToIterable(),
        ]);
        
        return $fileResponse;
    }

    public function apiPut($url, $options)
    {
        $lmsResponse = new LmsResponse();

        if (strpos($url, 'https://') === false) {
            $url = "https://{$this->baseUrl}/moodle/api/{$url}";
        }

// print "URL: {$url}\n<br/>";
// print_r($options);
// exit;

        $response = $this->httpClient->request('PUT', $url, $options);
        $lmsResponse->setResponse($response);

        $content = $lmsResponse->getContent();
        if (!empty($content['errors'])) {
            // TODO: If error is invalid token, refresh API token and try again 

            foreach ($content['errors'] as $error) {
                $lmsResponse->setError($error['message']);
            }
        }
        else if ($lmsResponse->getStatusCode() >= 400) {
            $lmsResponse->setError($content);
        }

        return $lmsResponse;
    }

}