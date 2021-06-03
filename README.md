# Cafe24 Template App

### Introduction
Start building your own Cafe24 store apps using our own template app. The template app includes basic functionalities you'll need to interact with the Cafe24 API specifically products searches and order searches. From there you can modify the code to suit your specific needs.

 Features Includes:
  - Carousel banner in Store Front to display 'marked/selected' products
  - Admin side: Product search w/ filters
  - Admin side: Order search w/ filters
  - Admin side: Mark products from Product search result and Order Product list
  - Admin side: Easy install + uninstall scripttags ( enable/disable functionality )

  
### Installation
Before getting started, it is a good idea to:
 - Sign-up in our [Cafe24 Integrated Membership page](https://user.cafe24.com/kr)
   > *__note:__ after signing up a shopping mall will be created for you where you can test the app you'll be developing. The shopping mall ID will be the same as the Cafe24 member ID.* 
 - Log-In at [Cafe24 developer center](https://developer.cafe24.com/developer/front/login)
   > Accept developer registration terms > Enter developer information > Developer registration completion information.
   >
   > *__note:__ Once successfully registering an app, take note of your CLIENT_ID and CLIENT_SECRET. Set the proper permissions that your app will be using at the developer center as well, it should match the APP_SCOPE variable in your .env as well.*
   
 - have a working local http server (Apache2, XAMPP, WAMP)
 - HTTP server should be configured to use HTTPS (TLS, formerly SSL) it's required for Cafe24 API calls 
 - PHP installed preferably v7.4.11 and up
 - Composer installed  
 - a local Redis DB installed

Step 1:
Clone the template app from repository  
https://github.com/cafe24github/cafe24-template-app.git
````
git clone https://github.com/cafe24github/cafe24-template-app.git
````

Step 2:
Go to the newly created local repository 'cafe24-template-app'
````
cd cafe24-template-app
````

Step 3:
Under the directory /cafe24-template-app run composer install
````
composer install
````

Step 4:
Create a .env file for the Cafe24 Template App and put it inside the root directory of your app.
Copy these configurations for your .env file  
````
APP_NAME=TemplateApp
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=https://cafe24-template-app.local.com
API_URL=https://cafe24-template-app-api.local.com

REDIRECT_URI=/token
LOG_CHANNEL=stack

CLIENT_ID=<CLIENT_ID from developer center>
CLIENT_SECRET=<CLIENT_SECRET from developer center>

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CAFE24_API_VERSION=<API version from developer center ex. '2021-03-01'>
CAFE24_API_URL=https://

#these should match the permissions set at the developer center 
APP_SCOPE="mall.read_product, mall.read_application, mall.write_application, mall.read_category"

BANNER_LIMIT=10
DISPLAY_LOCATION = 'MAIN'

SESSION_SAME_SITE="none"
SESSION_SECURE_COOKIE=true
````

Step 5:
Generate an encryption Key for your app
````
php artisan key:generate
````

Step 6:
Under the directory /public/js open and update the file template-app.js.
Update the value for **oSelf.sUrl** field.

````
this.initData = function () {
    oSelf.sMallId = CAFE24API.MALL_ID;
    oSelf.iShopNo = CAFE24API.SHOP_NO;
    // Developer should fill up this field
    // Url needs to be match on the domain of the app
    oSelf.sUrl = '<YOUR APP DOMAIN>';
    oSelf.sField = '&product_no,product_name,detail_image';
    oSelf.sDefaultImg = '//img.echosting.cafe24.com/thumb/104x104_1.gif';
    oSelf.oCurrency = SHOP_CURRENCY_FORMAT.getInputFormat();
    oSelf.bPlayFlag = true;
    oSelf.oHttpParam = {
        'mall_id' : oSelf.sMallId,
        'shop_no' : oSelf.iShopNo
    };
};
````

Update the **client_id** field with the client id from developer center.
````
this.initCafe24Api = function () {
            // Developer should fill up this field
            // Client id should should be based on the application's client id
            // from developer center
            (CAFE24API.init({
                version: '2021-03-01',
                client_id: '<YOUR CLIENT ID>'
            }));
        };
````

Step 7:
Using the browser of your choice type in the URL entry field:  
https://cafe24-template-app.local.com/?mall_id=<Cafe24_Member_ID>&user_id=<Cafe24_Member_ID>&user_type=P&shop_no=1

### NOTE
#### Links
[Cafe 24 Developer Guidelines](https://developer.cafe24.com/app/front/develop)  
[Composer Installer](https://getcomposer.org/download/)  
[Redis Installer](https://redis.io/download)

 
#### Contributors

- John Jhomar Egualada <john03@cafe24corp.com.ph>
- Marican Cabrera <marican@cafe24corp.com.ph>
- Eric Kristopher Paras Valdez <eric@cafe24corp.com.ph>
- Jean Paul Marquez <jean@cafe24corp.com.ph>
