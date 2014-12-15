# Hotel Deals
Hotel Deals shows up the latest deals from Expedia.

## Table of contents    
* [Setup] (#Setup)
* [Assumptions] (#Assumptions)

## Setup
1. After setting up an account on heroku, added a exercise named app 
2. Main entry point of the application is `index.php`
3. Other important files are put under the `includes/` directory.
4. Resources files (css + js) are present under `css/` and `javascript/` directories respectively.

## Assumptions
1. I assume that this application is requested through a regular browser interface, and not crwaled with the help of a robot. Although the application will run just fine under those circumstances as well, but ideally some check for the number of requests should be in place for such scenarios.
2. Since there is no concept of logging and maintaining sessions at the moment; cookies set to track the actions of a user are generated.
3. 
