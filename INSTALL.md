# Glaze Installation
* Download Glaze from [getglaze.com](http://getglaze.com/) and decompress to your `/var/www/glaze` folder, or anywhere else you want to place Glaze. 
* Open up the `config.ini` and configure your installation.
* Create the cache folder and give read/write permissions to your web server user:

```
cd /var/www/glaze
mkdir cache
chmod 777 cache
```

That's it, installation complete!

## Webserver configuration
nginx is the default supported platform for Glaze. You can view the optimal configuration below. 

### nginx glaze.server.conf

```
server {
    server_name glaze.MYSERVER.com;
    access_log /var/log/nginx/glaze.access_log main;
    error_log /var/log/nginx/glaze.error_log debug_http;

    root /var/www/DIR;
    index index.php;

#   auth_basic "Restricted";
#   auth_basic_user_file rhtpasswd;

    location = /robots.txt {
        allow all;
        log_not_found off;
        access_log off;
    }

    location ~* ^/index.php.*$ {
       fastcgi_pass 127.0.0.1:9000;
       include fastcgi.conf;
    }

    location / {
        try_files $uri @glaze;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
    add_header Vary "Accept-Encoding";
        expires max;
        try_files $uri @glaze;
        tcp_nodelay off;
        tcp_nopush on;
    }

#   location ~* \.(git|svn|patch|htaccess|log|route|plist|inc|json|pl|po|sh|ini|sample|kdev4)$ {
#       deny all;
#   }

    location @glaze {
        rewrite ^/.*$ /index.php;
    }
}
```