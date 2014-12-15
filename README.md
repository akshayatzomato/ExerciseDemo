# Hotel Deals
Hotel Deals is a web application primarily built for supporting third party apis. At the moment it shows the latest upcoming deals for hotels. Having said that it can easily be scaled into a portal serving data fetched from the number of different apis.

## Table of contents    
* [Setup] (#Setup)
* [Assumptions] (#Assumptions)

## Setup
1. After successfully setting up an account on Heroku, I deployed the folder named `exercise` on their server.
2. In the process of deployment I had to simply set up a git repository on my local machine, through which I could simply issue push commands to their remote repository.
3. Application structure has been divided into a number of components. `includes/` directory comprises of core modules and helper files used in handling the HTTP request. 
4. Entry point of the application is `index.php`. Resources files (css + js) are present under `css/` and `javascript/` directories respectively.

## Assumptions
1. I assume that this application is requested through a regular browser interface, and not crawled with the help of a robot. Although the application will run just fine under those circumstances as well, but ideally some check for the number of requests should be in place for such scenarios.
2. Since there is no concept of logging and maintaining sessions at the moment; cookies set to track the actions of a user are generated.
3. Other major assumption is that due to the lack of the documentation for deals api I could not really figure how pagination would be supported on the server side at the moment. So I have implemented a basic (inefficient) scenario of sending all the data to client in a single request. (no ajax calls)
