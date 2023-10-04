---
name: Currency conversion error
about: If you have problems with currency conversion use this template
title: ''
labels: ''
assignees: ''

---

**Before you create an issue make sure to follow this steps so you can provide concise information**

1.- **Did you configure your Coinmarket API key?**

The workflow requires an API key for currency conversion, you need to configure it first. Please check the readme for more information.

2.- **If you have added an API key but it's not working**

If the workflow does not display rates then it's not able to get the currency data. You need to test that your API key is working and that you are able to connect to the service. To test your API key follow this steps:

- Open the Terminal.app 
- Enter the following command, replace `APIKEY_HERE` with your own key and press enter.

```
curl "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?CMC_PRO_API_KEY=APIKEYHERE"
```
- If your API key is woking and you are able to connect to the service you will see a lot of text, all currencies with their value. 

If the command works in the terminal but not in the workflow you can procede and create an issue.
**If the command in the terminal does not work then it's not the workflow, it can be your API key or your own system (maybe you are using a proxy). DO NOT CREATE AN ISSUE, there's nothing i can do to help. Check your system, proxy, vpn, etc. and try the terminal command again until you are able to connect to the service**

**Describe the bug**
A clear and concise description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:
1. Open Alfred '...'
1. Type '...'
2. See error

**Expected behavior**
A clear and concise description of what you expected to happen.

**Debug Output**
![Debug](https://user-images.githubusercontent.com/1219228/82741985-23988800-9d1e-11ea-84d0-151b9bd1db09.png "Debug")

Please enable debug (see image) open Alfred and type the conversion that it's not working, you will see that the debug window is populated with a lot of text, please paste that text in here.

**System information:**
 - OS: [e.g. Mac OS 10.15.15]
 - Alfred Version [e.g. 4]
- PHP Version - To find out the version open the Terminal.app and type: **php -v**

**Additional context**
Add any other context about the problem here.
