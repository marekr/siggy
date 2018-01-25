

Apache:

Header always set Content-Security-Policy "default-src 'self'; style-src 'self' *.googleapis.com 'unsafe-inline' 'unsafe-eval'; script-src *.googleapis.com cdn.ravenjs.com 'self' 'unsafe-inline' 'unsafe-eval'; connect-src 'self' ws: sentry.io; report-uri https://app.getsentry.com/api/136864/csp-report/?sentry_version=5&sentry_key=b5e37673521b415c90ba0628e9dec98b; img-src 'self' data: *.eveonline.com; font-src 'self' data: fonts.googleapis.com fonts.gstatic.com;" 
	
Header always set X-Frame-Options SAMEORIGIN
Header always set X-Content-Type-Options "nosniff"
Header always set X-Xss-Protection "1; mode=block"
Header always set Strict-Transport-Security max-age=31536000

csp
====
eveonline.com - nuff said
gstatic.com and googleapis.com - js and fonts
cdn.ravenhs.com - sentry
connect-src - websockets and sentry.io for reporting

report-uri - reports csp error to sentry

x-frame-Options
====
Sameorigin so we can embed ourselves if we ever want to, protects agaisnt us being embedded elsewhere




TURN ON GZIP FOR JSON
====

AddOutputFilterByType DEFLATE application/json