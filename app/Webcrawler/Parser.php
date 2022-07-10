<?php

namespace App\Webcrawler;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

/**
 *  Manages parsing of an HTML document
 */
class Parser
{
  // A DOMDocument instance
  protected DomDocument $objDomDoc;

  /**
   *  creates an App\WebCrawler\Parser instance
   */
  public function __construct($curlResponse)
  {
    $this->objDomDoc = new DomDocument();
    @ $this->objDomDoc->loadHTML(mb_convert_encoding($curlResponse, 'HTML-ENTITIES', 'UTF-8'));
  }

  /**
  *  Retrieves title from the document - i.e., <title>
  */
  public function getTitle() : string
  {
    return $this->getElementTextContent('title')[0] ?? '';
  }

  /**
  *  Retrieves all image sources from the document - e.g., <img>, <picture>, <link rel="icon">
  */
  public function getImages() : array
  {
    // retrieve legitimate <img src="..." /> tags
    $srcImageFilter = function($img) {
      return !Str::startsWith($img, 'data:');
    };
    $srcImages = array_filter($this->getElementTextContent('img', 'src'), $srcImageFilter);

    // retrieve all <img data-src="..." /> tags
    $dataSrcImages = $this->getElementTextContent('img', 'data-src');

    // retrieve and parse all <picture><source srcset="..." /> tags
    $pictureSrcImages = [];
    $pictureSrcSets = $this->getElementTextContent('picture/source','srcset');
    foreach ($pictureSrcSets as $srcSet) {
      $temp = explode(' ', $srcSet);
      for ($i=0; $i<count($temp); $i++) {
        if ($i%2 === 0) {
          $pictureSrcImages[] = $temp[$i];
        }
      }
    }
    // retrieve all <icon> tags
    $iconImages = $this->getElementTextContent('link[@rel="icon"]','href');
    return array_merge($srcImages, $dataSrcImages, $pictureSrcImages, $iconImages);
  }

  /**
   *  Retrieves all link locations from the document - i.e., <a href="">
   */
  public function getLinks() : array
  {
    return $this->getElementTextContent('a', 'href');
  }

  /**
   *  Retrieves the (trimmed) text content of all text nodes in the document
   *  (omits <script> and <style> tags)
   */
  public function getPageText() : array
  {
    // xpath expression filters any <script> or <style> tags from the result
    $textNodes = $this->getElementTextContent('text()[not(parent::script) and not(parent::style)]');

    // trim text and remove empty nodes
    return array_map('trim', array_filter($textNodes));
  }

  /**
   *  Helper function to retrieve text content for a DOM type, attribute or matching expression
   *
   *  @param string $type - An element type, or element type expression e.g. <a>, <title>, <div>, text()
   *  @param string $attribute - Optional. An element attribute type, e.g. href, src, class
   */
  protected function getElementTextContent(string $type, string $attribute=null) : array
  {
    $domXPath = new DOMXPath($this->objDomDoc);
    $domNodes = $domXPath->query("(//{$type})");

    // array of text content for matching elements
    $elements = [];
    foreach ($domNodes as $node) {
      if (is_null($attribute)) {
        $elements[] = $node->textContent;
      }
      else if (!is_null($node->attributes->getNamedItem($attribute))) {
        $elements[] = $node->attributes->getNamedItem($attribute)->textContent;
      }
    }
    return $elements;
  }
}

?>
