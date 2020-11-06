# magento2-cancel-old-expired-orders

Script to be used as a CRON job in your server to get all orders from X days ago and cancel all of them using Magento 2 API. 

All you have to do is change the value of the following variables: 

- $token (To give access to your Magento 2 API)
- $url (Your Web Site URL)
- $daysAgo 


Attention: The ENDPOINT that this script uses to access the Magento 2 API is '/rest/V1/orders', if yours is different, just change the $endpoint variable.
