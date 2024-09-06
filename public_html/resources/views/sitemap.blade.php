@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($urls as $url)
        <sitemap>
            <loc>{{ $url }}</loc>
            <lastmod>{{ \Carbon\Carbon::now()->toDateString() }}</lastmod>
        </sitemap>
    @endforeach
</sitemapindex>