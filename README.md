# F&P Sushi-Bar Aufgabe

 - Author: Rene Schöner-Catanese
 - Date: 03.03.2023
 - Frontend: Jquery, Bootstrap and Font Awesome
 - Framework: Laravel
 - <a href="https://github.com/CodeCaprese/fp-sushi-bar" target="blank">Repository</a>
 - <a href="https://sushibar.schoener-catanese.de" target="_blank">Live demo</a>

## Prerequisites

 - \>= PHP 8.0
 - composer
 - database (for example MySQL or MariaDB)
 - Created database with name "sushibar", grant user all privileges.

## Local development 

 - change in `.env` file the database values `DB_USERNAME` and `DB_PASSWORD` with user that got granted privileges 
 - Open CMD in Project Folder (same level like app and bootstrap folder)
 - execute `composer install`
 - execute `php artisan migrate`
 - execute `php artisan serve`, now the php webserver startet.
 - open Browser and navigate to `localhost:8000`

## XAMPP, WAMP or LAMP

 - Windows - C:/xampp/apache/conf/extra/httpd-vhosts.conf

    `<VirtualHost *:80>`<br />
    `DocumentRoot "C:/xampp/htdocs/PROJECT_NAME/public"`<br />
    `ServerName PROJECT_NAME.test`<br />
    `</VirtualHost>`

- Mac - /opt/lampp/etc/extra/httpd-vhosts.conf

  `<VirtualHost *:80>`<br />
  `DocumentRoot /opt/lampp/htdocs/PROJECT_NAME/public`<br />
  `ServerName PROJECT_NAME.test`<br />
  `ServerAlias www.PROJECT_NAME.test`<br />
  `</VirtualHost>`

- change in `.env` file the database values `DB_USERNAME` and `DB_PASSWORD` with user that got granted privileges
- Open CMD in Project Folder (same level like app and bootstrap folder)
- execute `composer install`
- execute `php artisan migrate`
- open Browser and navigate to `ServerName` resp. `ServerAlias`

## View

For an easy view, I used a bootstrap table design, where all used seats are shown in green. Also a group number is
written to identify which group came together. An icon to bill the group is also included.

## Tests

To execute tests use command `php artisan test --testsuite=Feature`. **Note**: Tests are using same database
as mentioned in `.env`. 

## Which Files did I changed?

There are two commits in the git repository, the first was an initial basic Laravel commit.
The second is contains all created, modified and deleted files.

## Where is the "main logic"?

 - In `app\Http\Controllers\TableController` is the logic where the seating, check for empty spaces and check which space 
   the best option to seat customer.
 - Validation can be found in `app/Http/Requests`. 
 - The routes including the name `routes/web.php`
 - Tests are in `tests/Feature`

## Usage

First you have to specify how many seats at the table are. Afterwards you can set how many customer want
to seat down. An alert message modal will return useful information.
