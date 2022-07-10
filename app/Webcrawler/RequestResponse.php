<?php

namespace App\Webcrawler;

/**
 *  Manages sending and receiving for HTTP requests
 */
class RequestResponse
{
  // The url for initating a request
  protected string $url;

  // The information received from the request operation
  protected array $details = [];

  // The response content
  protected string $content = '';

  /**
   *  creates an App\WebCrawler\RequestReponse instance
   */
  public function __construct(String $url)
  {
    $this->url = $url;
    $this->send();
  }

  /**
   *  Sends a (CURL) request and records request / response details
   */
  protected function send()
  {
    $curlRequest = curl_init();
    curl_setopt($curlRequest, CURLOPT_URL, $this->url);
    curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, true);
    // follow location headers
    curl_setopt($curlRequest, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($curlRequest);
    if ($content !== FALSE) {
      $this->content = $content;
    }
    $this->details = curl_getinfo($curlRequest);
  }

  /**
   *  Retrieves the URL of the request
   */
  public function getRequestUrl() : string {
    return $this->url;
  }

  /**
   *  Retrieves the URL of the response
   *  (possibly different from request URL - e.g., redirected request)
   */
  public function getResponseUrl() : string {
    return $this->details['url'] ?? '';
  }

  /**
   *  Retrieves the request response time (seconds)
   */
  public function getReponseTime() {
    return $this->details['total_time'] ?? 0;
  }

  /**
   *  Retrieves the request response status code (e.g., 200, 404)
   */
  public function getResponseStatus() {
    return $this->details['http_code'] ?? 0;
  }

  /**
   *  Retrieves the response content (e.g., HTML of a web document)
   */
  public function getContent() {
    return $this->content;
  }
}
