<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Webcrawler\Parser;

class ParserTest extends TestCase
{
  private string $document;
  private string $testData;

  protected function setUp(): void
  {
    $this->document = <<<EOT
      <html>
      <head><title>Test Page</title></head>
      <link rel="/myicon" href="/icon-one" />
      <body>
        <picture>
          <source srcset="/set-one 900w /set-two 700w /set-three 200w" />
        </picture>
        <div>buddy
          <a href="/link-one">
            <img src="/image-one" /><img src="/image-two" /><img src="/image-three" />
          </a>
          <a href="/link-two">two</a>
          <img src="data:image/svg+xml,%3Csvg" data-src="img-lazyload-1" />
          <img src="data:image/svg+xml,%3Csvg" data-src="img-lazyload-2" />
        </div>
      </body></html>
    EOT;
  }

  /**
   * Verifies that title is returned from a document
   */
  public function test_title_extracted_from_document() : void
  {
    $parser = new Parser($this->document);
    $title = $parser->getTitle();
    $this->assertEquals('Test Page', $title);
  }

  /**
   * Verifies that links are returned from a document
   */
  public function test_links_extracted_from_document() : void
  {
    $parser = new Parser($this->document);
    $links = $parser->getLinks();
    $this->assertEquals(['/link-one', '/link-two'], $links);
  }

  /**
   * Verifies that images are returned from a document
   */
  public function test_images_extracted_from_document() : void
  {
    $parser = new Parser($this->document);
    $links = $parser->getImages();
    $this->assertEquals(['/image-one', '/image-two', '/image-three', 'img-lazyload-1',
      'img-lazyload-2', '/set-one', '/set-two', '/set-three' ], $links);
  }

  /**
   * Verifies that correct word count is returned from a document
   */
  public function test_text_words_extracted_from_document() : void
  {
    $parser = new Parser($this->document);
    $pageText = $parser->getPageText();
    $this->assertEquals(['Test Page', 'buddy', 'two'], $pageText);
  }
}

?>
