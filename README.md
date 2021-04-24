# College Event Website
The goal of this project is to create a website that allows students from different universities to create and host events for their communities. These events can be private, public, or specific to specific RSOs that are created through the application. There are three tiers of users on the website: normal users, who can join/create rsos and join events available to them; admins, who can create events for RSOs that they are the admin of; and super-admins, who control the university.
Users are also able to comment on events that they are part of, and features default PHP password encryption.

- Inlcuded In this Repository:
	- cop4710 - Website Contents and MySQLi code
	- DemoVideo.zip - A video showing website operations
	- COP4710_ProjectReport.pdf - A report going over functionality of the website/database

## Technical Specifications
The web-based application was created using a MySQLi backend with InnoDB to support functional dependencies. PHP is used for server-user connections, and the front-end was created using a combination of HTML, CSS, and JS.

The project was created and tested locally using WAMP, which is a Windows based application that automatically integrates Apache, MySQLi, and PHP for easy and quick development. Firefox and Microsoft Edge were both used to validate front-end visuals.

## Installation Instructions
Download WAMP: https://sourceforge.net/projects/wampserver/

Once setup with WAMP is complete, find the www directory and place the folder "cop4710" wihtin it.
Start the WAMP server, and on an internet browser navigate to the following url:

localhost/phpmyadmin

Go through the inital setup, if necessary, and then go to the SQL tab.
Once there, find the setup.sql file within the cop4710 folder, and copy its
contents to the SQL input on the SQL tab.
This will init the database.

Finally, navigate to the following url to access the site:

localhost/cop4710