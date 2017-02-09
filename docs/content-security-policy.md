

Apache:

Header set Content-Security-Policy "default-src 'self' *.eveonline.com *.googleapis.com *.gstatic.com cdn.ravenjs.com 'unsafe-inline' 'unsafe-eval'; connect-src 'self' ws:;" 


eveonline.com - nuff said
gstatic.com and googleapis.com - js and fonts
cdn.ravenhs.com - sentry
connect-src - websockets