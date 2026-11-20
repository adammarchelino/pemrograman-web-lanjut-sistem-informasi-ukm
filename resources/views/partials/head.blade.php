<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

<script>
    // Terapkan keadaan sidebar sebelum render supaya tidak ada kedipan lebar penuh.
    try {
        if (window.localStorage.getItem('sip.sidebar') === 'collapsed') {
            document.documentElement.setAttribute('data-sidebar', 'collapsed');
        }
    } catch (error) {
        // localStorage bisa diblokir; biarkan sidebar dalam keadaan terbuka.
    }
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
