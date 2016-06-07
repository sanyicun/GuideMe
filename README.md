#GuidMe
This is a test website based Laravel version 5.2.15

from: http://www.golaravel.com/download/

1. cp .env.example .env  --这也是 laravel-installer 自动完成的一步工作。

2. php artisan key:generate  --为应用生成 key 。这也是 laravel-installer 自动完成的收尾工作。

3. chmod 777 storage  --change writable of dir

make sure your webservice support route

[nginx] add following code

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

