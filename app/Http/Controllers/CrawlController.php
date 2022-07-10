<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Webcrawler\Crawler;


class CrawlController extends Controller
{
  /**
   *  Retrieves the initial Web Crawler user interface
   */
  public function init()
  {
    return view('app');
  }

  /**
   *  Creates a web crawl and retrieves output for the given URL
   *
   *  @param Illuminate\Http\Request $request - Laravel request object containing url to crawl
   */
  public function crawl(Request $request)
  {
    $crawler = new Crawler($request->input('url') ?? '');
    $crawler->crawl();
    return view('app', ['data' => $crawler]);
  }
}
