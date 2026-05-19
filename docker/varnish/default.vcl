vcl 4.1;

backend default {
    .host = "web";
    .port = "80";
}

sub vcl_recv {
    # Bypass Varnish cache for POST requests or anything with cookies (like Laravel sessions)
    if (req.method == "POST") {
        return (pass);
    }
    return (hash);
}

sub vcl_backend_response {
    # Cache responses for 10 minutes by default
    set beresp.ttl = 10m;
}

sub vcl_deliver {
    # Add a header indicating if it was a cache hit or miss
    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT";
    } else {
        set resp.http.X-Cache = "MISS";
    }
}
