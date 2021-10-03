# To-do project

**Version 1.0.0** 

:computer: This project was created in the context of OpenClassRooms Symfony path. </br>
:briefcase: It is the 7th project, and the first in which Symfony was used. 
It was a very important project, as it allowed me to understand automated tests, and to manipulate someone else's code for the first time.

## Installation of the project

1.  Clone the project
> git clone https://github.com/Ludovicmanga/ToDo_Project.git

2.  Modify the .env file, according to your own configuration
> DATABASE_URL="mysql://root:@127.0.0.1:3306/snowTricks?serverVersion=mariadb-10.4.11"

3.  Install the dependencies 
> composer install

4.  Create the database
> php bin/console doctrine:database:create

5.  Generate the migrations files 
> php bin/console make:migration

6.  Execute the migrations files
> php bin/console doctrine:migrations:migrate

7.  Execute the fixtures
> php bin/console doctrine:fixtures:load

8. Execute the automated tests, by running PHP Unit
> vendor/bin/phpunit

## Documentation of the project

The documentation detailing the authentication can be found at the route : /build/html/authentication.html or in the file authentication.html, in the folder build

The general documentation of the project can be found in the file index.html, in the same build folder.

--- 

## License  copyright 
:copyright: Copyright Ludovic Manga-jocky 