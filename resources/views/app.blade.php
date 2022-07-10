<!DOCTYPE html>
<html lang='en-US'>
  <head>
    <title></title>

    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}" rel="stylesheet" />
  </head>
  <body>

  <form id="urlEntryForm" method="POST" action="/">
      @csrf
      <label for="url">Enter a URL</label>
      <input name="url" type="text" class="@error('url') is-invalid @enderror">

      @error('url')
          <div class="alert alert-danger">{{ $message }}</div>
      @enderror

      <button type="submit">
        Submit
      </button>
  </form>

  @isset($data)
    <div class="sectionHeader">Crawler Statistics</div>
    <table>
      <tr>
        <td>Number of pages crawled</td>
        <td>{{ $data->aggStats->numberOfPages }}</td>
      </tr>
      <tr>
        <td>Number of unique images</td>
        <td>{{ $data->aggStats->uniqueImageCount }}</td>
      </tr>
      <tr>
        <td>Number of unique internal links</td>
        <td>{{ $data->aggStats->uniqueInternalLinkCount }}</td>
      </tr>
      <tr>
        <td>Number of unique external links</td>
        <td>{{ $data->aggStats->uniqueExternalLinkCount }}</td>
      </tr>
      <tr>
        <td>Average page load (seconds)</td>
        <td>{{ round($data->aggStats->avgLoadTime, 4) }}s</td>
      </tr>
      <tr>
        <td>Average word count</td>
        <td>{{ $data->aggStats->avgWordCount }}</td>
      </tr>
      <tr>
        <td>Average title length</td>
        <td>{{ $data->aggStats->avgTitleLength }}</td>
      </tr>
    </table>

    <div class="sectionHeader">Pages Crawled</div>
    <table>
      <tr>
        <thead>
          <th>Request (URL)</th>
          <th>Response</th>
          <th>Status code</th>
        </thead>
      </tr>
    @foreach ($data->pages as $page)
      <tr>
        <td>{{ $page->reqUrl }}</td>
        <td>{{ $page->respUrl }}</td>
        <td>{{ ($page->status !== 0) ? $page->status : '404 (no reply)'  }}</td>
      </tr>
    @endforeach
    </table>
  @endisset

  </body>
</html>
