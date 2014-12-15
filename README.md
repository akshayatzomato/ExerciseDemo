# Hotel Deals
Hotel Deals is a web application primarily built for supporting third party apis. At the moment it shows the latest upcoming deals for hotels. Having said that it can easily be scaled into a portal serving data fetched from the number of different apis.

## Table of contents 
* [Description](#description)
* [Setup Instructions](#setup)
* [Assumptions](#assumptions)

## Description
1. After successfully setting up an account on Heroku, I deployed the folder named `exercise` on their server.
2. In the process of deployment I had to simply set up a git repository on my local machine, through which I could simply issue push commands to their remote repository.
3. Application structure has been divided into a number of components. `includes/` comprises of core modules and helper files used in handling the HTTP request. A number of utility classes (and functions) have also been borrowed from the mediawiki-core repository.
4. Entry point of the application is `index.php`. Resource files (css + js) are put under `css/` and `javascript/` directories respectively. As the name suggests `test` contains the `simpletest` library (for unit testing in PHP) and other test cases specific to this application.

## Setup Instructions
1. Clone this repository on to your local machine. You will end up with a folder named `exercise` on your hard drive.
2. Copy that folder into your apache's `www` directory or under the `DocumentRoot` specified in your httpd-vhosts.conf file. You can follow these steps to ensure you can access your application properly through a browser:
`<VirtualHost *:80>                                                                 
    DocumentRoot "DEVELOPMENT_DIRECTORY"                                       
    ServerName USERNAME.local                                                        
    ErrorLog "/private/var/log/apache2/USERNAME.local-error_log"                     
    CustomLog "/private/var/log/apache2/USERNAME.local-access_log" common            
    <Directory "/Users/USERNAME/Development">                                        
        Require all granted                                                        
    </Directory>                                                                   
</VirtualHost>`
Replace USERNAME with anything that is more meaningful for you. After defining this, you can access your application by the following url : http://USERNAME.local/exercise/index.php

## Assumptions
1. I assume that this application is requested through a regular browser interface, and not crawled with the help of a bot. Although the application will run just fine under those circumstances as well, but ideally some check for the number of requests should be in place for such scenarios.
2. Since there is no concept of logging and maintaining sessions at the moment; cookies are not set to track the actions of a user.
3. Other major assumption is that due to the lack of the documentation for deals api, I could not really figure out how pagination would be supported on the server side at the moment. So I have implemented a basic (inefficient) scenario of sending all the data to client in a single request. (no ajax calls)
