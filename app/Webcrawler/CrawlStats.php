<?php

namespace App\Webcrawler;

class CrawlStats
{
   /**
   *  public attributes required here
   *  realized this was necessary for visibility in Laravel Blade Templates
   *  TODO (suggestion): make class accessor methods visible in Blade Templates
   */
  // number of pages crawled
  public int $numberOfPages;
  // number of unique images found
  public int $uniqueImageCount;
  // number of unique internal links found
  public int $uniqueInternalLinkCount;
  // number of unique external links found
  public int $uniqueExternalLinkCount;
  // average page load time (seconds)
  public float $avgLoadTime;
  // average page word count
  public int $avgWordCount;
  // average title length
  public int $avgTitleLength;

  /**
   * creates an App\WebCrawler\CrawlStats instance
   */
  public function __construct(array $pages)
  {
    $this->numberOfPages = count($pages);

    // creates a collection from the array of Pages
    $pageCollection = collect($pages);

    // leverages collection->pluck to easily pull a property from many arrays at a time
    $this->setUniqueImagesCount($pageCollection->pluck('images')->all());
    $this->setUniqueLinkCounts($pageCollection->pluck('internalLinks')->all(), 'internal');
    $this->setUniqueLinkCounts($pageCollection->pluck('externalLinks')->all(), 'external');
    $this->setAvgPageLoadSeconds($pageCollection->pluck('loadTime')->all());
    $this->setAvgWordCount($pageCollection->pluck('wordCount')->all());
    $this->setAvgTitleLength($pageCollection->pluck('title')->all());
  }

  /**
   *  Sets the average load time
   *
   *  @param array $loadTimes - A list of crawled load times
   */
  protected function setAvgPageLoadSeconds(array $loadTimes) : void
  {
    $this->avgLoadTime = array_sum($loadTimes)/count($loadTimes);
  }

  /**
   *  Sets the average word count
   *
   *  @param array $wordCounts - A list of crawled word counts
   */
  protected function setAvgWordCount(array $wordCounts) : void
  {
    $this->avgWordCount = array_sum($wordCounts)/count($wordCounts);
  }

  /**
   *  Sets the average title length
   *
   *  @param array $titles - A list of crawled titles
   */
  protected function setAvgTitleLength(array $titles) : void
  {
    $itemLength = function($item) {
      return strlen($item);
    };
    $titleLengths = array_map($itemLength, $titles);
    $this->avgTitleLength = array_sum($titleLengths)/count($titleLengths);
  }

  /**
   *  Sets the unique link count for the given link type
   *
   *  @param array $links - A list of crawled links
   *  @param string $linkType - 'internal', for internal links, 'external' for external links
   */
  protected function setUniqueLinkCounts(array $links, string $linkType) : void
  {
    // handle if proper linkType not provided
    if (!in_array($linkType, ['internal', 'external'])) {
      $linkType = 'internal';
    }
    // sets uniqueInternalLinkCount or uniqueExternalLinkCount
    $propName = 'unique' . ucfirst($linkType) . 'LinkCount';
    $this->$propName = $this->getUniqueElementCount($links);
  }

  /**
   *  Sets the unique image count
   *
   *  @param array $images - A list of crawled images
   */
  protected function setUniqueImagesCount(array $images) : void
  {
    $this->uniqueImageCount = $this->getUniqueElementCount($images);
  }

  /**
   *  Helper function gets the unique count of many arrays of provided assets
   *
   *  @param array $assets - array of array of assets (such as images and links)
   */
  protected function getUniqueElementCount(array $assets)
  {
    return count(array_values(array_unique(array_merge(...$assets))));
  }
}

?>
