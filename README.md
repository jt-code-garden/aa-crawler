## Building a Web Crawler using PHP

This small project is a web crawler that I built to crawl up to 6 pages of a website, given a single
entry point.

A [demo](https://crawler.englishwithjt.com) of the Web Crawler can be found at: [https://crawler.englishwithjt.com](https://crawler.englishwithjt.com).

## How do I use the Web Crawler?

Type a URL that you would like to crawl into the input box provided and click submit.

## What information does the Web Crawler provide?

The Crawler fetches the following information:

- **Number of pages crawled**
- **Number of unique images**
- **Number of unique internal links**
- **Number of unique external links**
- **Average page load in seconds**
- **Average word count**
- **Average title length**

Also, the crawler provides the list of URLs crawled and the HTTP status code for the response to each request.

## Architecture

- PHP 7.4
- Laravel 8

## Additional Thoughts

- The Web Crawler makes requests synchronously using curl.
- To make asynchronous requests would improve the performance of the Web Crawler.
