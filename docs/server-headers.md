

Apache:

Header always set Content-Security-Policy "default-src 'self' *.eveonline.com *.googleapis.com *.gstatic.com cdn.ravenjs.com 'unsafe-inline' 'unsafe-eval'; connect-src 'self' ws:;" 
Header always set X-Frame-Options SAMEORIGIN


csp
====
eveonline.com - nuff said
gstatic.com and googleapis.com - js and fonts
cdn.ravenhs.com - sentry
connect-src - websockets

x-frame-Options
====
Sameorigin so we can embed ourselves if we ever want to, protects agaisnt us being embedded elsewhere