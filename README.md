# Hotel Deals
Hotel Deals is a web application primarily built for supporting third party apis. At the moment it shows the latest upcoming deals for hotels. Having said that it can easily be scaled into a portal serving data fetched from the number of different apis.

## Table of contents 
* [Description](#description)
* [Setup](#setup)
* [Assumptions](#assumptions)

## Description
This is a brief overview of the design.<br/>
Primary Classes:<br/>
*Application*<br/>
    The core class for handling all HTTP requests. Depending upon the type of request specific modules are loaded to meet the specific requirements.
    
*OutputPage*<br/>
    The wrapper class for handling all output related events. Contains all the utility functions related to HTML and HTTP headers.
    
*WebResponse*<br/>
    A simple abstraction of printing out headers and cookies for HTTP responses.
    
*Template*<br/>
    Parent template for structuring the content on the page. Every new template (child) should extend this class to avail all its features.
    
Application structure has been divided into a number of reusable components and modules. `includes/` comprises of core modules and helper files used in handling the HTTP request. A number of utility classes (and functions) have also been borrowed from the mediawiki-core repository.<br/>
Entry point of the application is `index.php`. Resource files (css + js) are put under `css/` and `javascript/` directories respectively. PHPUnit test files are present under `tests` directory. There are many components involved in getting the response for a given service method, so instead of focusing on the service I have primarily concentrated on individual parts of that service. For eg. `testDataObject()` in `ApplicationTest.php` looks to find out if the proper template has been loaded for a particular type of request or not.

## Setup
This is assuming that you have apache and php already set up on your local machine, if that is the case just follow along the steps to make sure have a working application.

Clone a copy of the main Exercise git repo by running:

```bash
git clone git@github.com:akshayatzomato/Exercise.git
```

Now you need to tell apache where to look for your application in case someone issues a HTTP request to your apache server. So here is how we do it:

##### Default configuration 

Just place your `exercise` folder under your apache's `www` directory and access your application via this URL :
[http://localhost/exercise/index.php]()
        
##### Custom configuration 

You can point your apache to a different directory by simply defining a new Virtual Host as follows. Place this below snippet in your httpd-vhosts.conf file on your machine. And now you would need to put your application under the DocumentRoot folder.<br/>
```
<VirtualHost *:80>                                                                 
    DocumentRoot "DEVELOPMENT_DIRECTORY"                                       
    ServerName USERNAME.local                                                        
    ErrorLog "/private/var/log/apache2/USERNAME.local-error_log"                     
    CustomLog "/private/var/log/apache2/USERNAME.local-access_log" common            
    <Directory "DEVELOPMENT_DIRECTORY">                                      
        Require all granted                                                        
    </Directory>                                                                   
</VirtualHost>
```

`DEVELOPMENT_DIRECTORY` - Where your code resides.<br/>
`USERNAME` - anything meaningful.<br/>
After defining this, you can access your application by the following url :                                 [http://USERNAME.local/exercise/index.php]()

## Assumptions
1. I assume that this application is requested through a regular browser interface, and not crawled with the help of a bot. Although the application will run just fine under those circumstances as well, but ideally some check for the number of requests should be in place for such scenarios.
2. Since there is no concept of logging and maintaining sessions at the moment; cookies are not set to track the actions of a user.
3. Other major assumption is that due to the lack of the documentation for deals api, I could not really figure out how pagination would be supported on the server side at the moment. So I have implemented a basic (inefficient) scenario of sending all the data to client in a single request. (no ajax calls)
