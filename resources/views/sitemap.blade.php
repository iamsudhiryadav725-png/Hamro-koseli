{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Static pages --}}
    <url><loc>{{ url('/') }}</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
    <url><loc>{{ url('/shop') }}</loc><changefreq>daily</changefreq><priority>0.9</priority></url>
    <url><loc>{{ url('/categories') }}</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>
    <url><loc>{{ url('/new-arrivals') }}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>
    <url><loc>{{ url('/todays-deals') }}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>
    <url><loc>{{ url('/top-sellers') }}</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>
    <url><loc>{{ url('/about-us') }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
    <url><loc>{{ url('/contact-us') }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
    <url><loc>{{ url('/privacypolicy') }}</loc><changefreq>yearly</changefreq><priority>0.3</priority></url>

    {{-- Dynamic categories --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ url('/categories/' . $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- Dynamic products --}}
    @foreach($products as $product)
    <url>
        <loc>{{ url('/product/' . $product->slug) }}</loc>
        <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

</urlset>