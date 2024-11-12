# TecWeb

## Local Development
To start the local development environment, you'll need to have installed [docker](https://www.docker.com/), after that run the following command inside the repo directory:
```bash
docker-compose up
```

<details>
 <summary>After that you should see something like this:</summary>

  ```bash
  [+] Running 2/0
  ✔ Container tecweb-mariadb-1  Created                                                                                                                                                                                                                                                        0.0s
  ✔ Container tecweb-web-1      Created                                                                                                                                                                                                                                                        0.0s
  Attaching to mariadb-1, web-1
  mariadb-1  | 2024-11-05 10:21:57+00:00 [Note] [Entrypoint]: Entrypoint script for MariaDB Server 1:10.6.7+maria~focal started.
  mariadb-1  | 2024-11-05 10:21:57+00:00 [Note] [Entrypoint]: Switching to dedicated user 'mysql'
  mariadb-1  | 2024-11-05 10:21:57+00:00 [Note] [Entrypoint]: Entrypoint script for MariaDB Server 1:10.6.7+maria~focal started.
  mariadb-1  | 2024-11-05 10:21:57+00:00 [Note] [Entrypoint]: MariaDB upgrade not required
  mariadb-1  | 2024-11-05 10:21:57 0 [Note] mariadbd (server 10.6.7-MariaDB-1:10.6.7+maria~focal) starting as process 1 ...
  mariadb-1  | 2024-11-05 10:21:57 0 [Note] InnoDB: Compressed tables use zlib 1.2.11
  mariadb-1  | 2024-11-05 10:21:57 0 [Note] InnoDB: Number of pools: 1
  mariadb-1  | 2024-11-05 10:21:57 0 [Note] InnoDB: Using crc32 + pclmulqdq instructions
  mariadb-1  | 2024-11-05 10:21:57 0 [Note] InnoDB: Using Linux native AIO
  mariadb-1  | 2024-11-05 10:21:57 0 [Note] InnoDB: Initializing buffer pool, total size = 134217728, chunk size = 134217728
  mariadb-1  | 2024-11-05 10:21:57 0 [Note] InnoDB: Completed initialization of buffer pool
  mariadb-1  | 2024-11-05 10:21:57 0 [Note] InnoDB: Starting crash recovery from checkpoint LSN=87144,87144
  web-1      | AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.21.0.3. Set the 'ServerName' directive globally to suppress this message
  web-1      | AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.21.0.3. Set the 'ServerName' directive globally to suppress this message
  web-1      | PHP Warning:  Module "mysqli" is already loaded in Unknown on line 0
  web-1      | PHP Warning:  Module "pdo_mysql" is already loaded in Unknown on line 0
  web-1      | [Tue Nov 05 10:21:57.904725 2024] [mpm_prefork:notice] [pid 1:tid 1] AH00163: Apache/2.4.62 (Debian) PHP/8.1.30 configured -- resuming normal operations
  web-1      | [Tue Nov 05 10:21:57.904742 2024] [core:notice] [pid 1:tid 1] AH00094: Command line: 'apache2 -D FOREGROUND'
  ```
</details>

Then you can open your browser and see the website at http://localhost:8800

## Project Structure
```bash
├── db.sql              # Database schema
├── docker-compose.yml  # Docker compose file for local development
├── Dockerfile          # Custom Dockerfile for the php-apache image
├── docs                # Documentation
├── fix_perms.sh        # Script to fix permissions on the project files (not sure if we'll need this)
├── html                # Root directory for the web server which points at /var/www/html
│   ├── *.php          # PHP files, each one is a page/route
│   ├── favicon.ico
│   ├── fonts          # Fonts
│   ├── images         # Images
│   ├── index.php      # Main page
│   ├── php-test.php   # PHP test page to check if PHP is working and the curret settings (to be removed in production)
│   ├── scripts        # JavaScript files
│   └── styles         # CSS files
├── LICENSE
├── php.ini             # Custom php.ini file for the php-apache image configuration
├── README.md
└── src                 # PHP logic not exposed to the public
    ├── config.php      # Configuration file for variables that are used throughout the project
    ├── controllers     # Controllers, which are the classes that handle the logic of the application and interact with the views and models
    ├── models          # Models, which are the classes that interact with the database and data manipulation
    ├── templates       # Templates, aka the pure html files that need to be rendered from the views
    └── views           # Views, which inject the necessary data into the templates and render them
```
