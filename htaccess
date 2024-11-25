
<Files .env>
    Order allow,deny
    Deny from all
</Files>
<Files .htaccess>
    Order allow,deny
    Deny from all
</Files>

<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

<IfModule mod_rewrite.c>
    
    RewriteEngine On
    
    Options -Indexes
    ServerSignature Off
    
    RewriteCond %{HTTPS} off
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Permitted-Cross-Domain-Policies "none"
    
    RewriteBase /
    RewriteRule ^$ public/index.php [L]
    RewriteRule ^((?!public/).*)$ public/$1 [L,NC]

</IfModule>
