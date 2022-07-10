<?php

namespace App\Webcrawler;

use Illuminate\Support\Str;
use ErrorException;

/**
 *  Manages the WebCrawler state and delegation of the crawling process
 */
class Crawler
{
  // the maximum number of links to crawl
  private int $maxLinksToCrawl = 6;
  // the base url (scheme + host), for crawling
  private string $domain;
  // an array of link locations for the crawler to iterate
  private array $linksToCrawl;
  // array of Page instances
  public array $pages = [];
  // CrawlStats instance
  public CrawlStats $aggStats;

  /**
   * creates an App\WebCrawler\Crawler instance
   */
  public function __construct(String $url='')
  {
    // handle url entry without scheme - e.g., www.google.com
    if (!Str::startsWith($url, 'http')) {
      $url = 'https://' . $url;
    }
    // catch anything that cannot be parsed and process with invalid_url_received
    try {
      $this->domain = parse_url($url)['scheme'] . '://' . parse_url($url)['host'];
    }
    catch (ErrorException $e) {
      // TODO: (suggestion) prompt User Interface feedback for such input (and halt process)
      $this->domain = 'https://invalid_url_received';
    }
    // extract the initial path to crawl for the given url
    $this->linksToCrawl[0] = parse_url($url)['path'] ?? '/';
  }

  /**
   *  Performs the web crawl routine on the current Crawler instance
   *
   *  Crawling starts with the given url on instantiation and continues on to any internal
   *  links found in the response document, up to a maximum number of links to crawl
   *  (see: $this->maxLinksToCrawl)
   */
  public function crawl() : void
  {
    $index = 0;
    // crawls the given page, and continues with internal links
    while ($index < $this->maxLinksToCrawl && isset($this->linksToCrawl[$index])) {
      $reqResponse = new RequestResponse($this->domain . $this->linksToCrawl[$index]);
      $resultPage = new Page($reqResponse);

      // store the state of the page crawled
      $this->pages[] = $resultPage;

      // the crawler crawls internal links
      $this->updateLinksToCrawl($resultPage->internalLinks ?? []);
      $index++;
    }
    // aggregates the crawl statistics after all pages are collected
    $this->aggStats = new CrawlStats($this->pages);
  }

  /**
   *  Updates the links to crawl list with additional links retrieved, up to the maximum limit
   *  (see: this->maxLinksToCrawl for limit)
   *
   *  @param array $newLinks - A list of urls to append to this->linksToCrawl
   */
  private function updateLinksToCrawl($newLinks) : void
  {
    for ($i = 0; $i < count($newLinks); $i++) {
      // break out - if the maximum number of page links to crawl has been reached
      if (count($this->linksToCrawl) === $this->maxLinksToCrawl) {
        return;
      }
      if (!in_array($newLinks[$i], $this->linksToCrawl)) {
        $this->linksToCrawl[] = $newLinks[$i];
      }
    }
  }
}

?>
