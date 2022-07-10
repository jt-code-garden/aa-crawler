<?php

namespace App\Webcrawler;

use Illuminate\Support\Str;

/**
 *  Gathers and stores data and statistics for an individual crawled web page
 */
class Page
{
 /**
   *  public attributes required here
   *  realized this was necessary for visibility in Laravel Blade Templates
   *  TODO (suggestion): make class accessor methods visible in Blade Templates
   */
  // url of requested page
  public string $reqUrl;
  // url of response page
  public string $respUrl;
  // HTTP code of the response
  public int $status;

  /**
   *  public attributes required here
   *  realized this was necessary for collection->pluck (CrawlStats class)
   *  TODO (suggestion): decouple implementation
   */
  // page load time (in seconds)
  public float $loadTime;
  // the page title <title>
  public string $title = '';
  // all page images
  public array $images = [];
  // all internal page links (those not prefixed by http(s))
  public $internalLinks = [];
  // all external page links (those prefixed by http(s), including links from same response host)
  public $externalLinks = [];
  // word count for all visible and invisible page text
  public int $wordCount = 0;

  /**
   *  creates an App\WebCrawler\Page instance
   */
  public function __construct(RequestResponse $req)
  {
    $p = new Parser($req->getContent());
    $this->reqUrl = $req->getRequestUrl();
    $this->respUrl = $req->getResponseUrl();
    $this->loadTime = $req->getReponseTime();
    $this->status = $req->getResponseStatus();
    $this->title = $p->getTitle();
    $this->images = $p->getImages();
    $pageLinks = $p->getLinks();
    $this->setWordCount($p->getPageText());
    $this->separateInternalExternalLinks($pageLinks);
  }

  /**
   *  Extracts unique internal links and unique external links into their own Page properties
   *
   *  (assumes any extracted link prefixed with http(s) as an external link)
   *
   *  @param array $links - A given array of urls
   */
  protected function separateInternalExternalLinks(array $links) : void
  {
    foreach ($links as $link) {
      // filter out anchor tags
      if (Str::startsWith($link, '#')) {
        continue;
      }
      // Assume http, https or // prefixed links as externals
      if (Str::startsWith($link, 'http') || Str::startsWith($link, '//')) {
        $this->externalLinks[] = $link;
      }
      // remaining links are internal links
      else {
        // prefix all internal links with '/', if not already done so
        $this->internalLinks[] = (!Str::startsWith($link, '/') ? '/' : '') . $link;
      }
    }
  }

  /**
   *  Sets the aggregated word count for the page
   *
   *  @param array $words - A given array of words
   */
  protected function setWordCount(array $words) : void
  {
    $sum = 0;
    foreach ($words as $word) {
      $sum += str_word_count($word);
    }
    $this->wordCount = $sum;
  }
}

?>
