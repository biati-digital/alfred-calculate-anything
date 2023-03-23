# Changelog

### v4.0.1

- Improved: Workflow description now includes more examples
- Fixed: Not able to convert to multiple default currencies if the first default currency is the same as the input currency
- Fixed: Some users were not able to use currency conversion, depending on their PHP installation
- Fixed: Time keyword not working in PHP 8.2+
- Fixed: Time keyword not grabbing the new config value for Alfred 5

### v4.0.0

- New: Now the workflow uses the new Alfred 5 Configuration window
- New: Now you can convert from fiat currency to crypto currency
- New: Now you can convert from crypto currency to fiat currency
- New: Now you can use negative numbers for example `-25F to C`
- New: Now you can use numbers starting with dot for example `.250km to meters`
- New: Added new language: Swedish
- New: Added new option to configure the decimals separator to support multiple inputs
- New: Added new option to configure the number of decimals in currency conversions
- New: Added new option to configure the number of decimals in cryptocurrency conversions
- New: Added new option to configure the output format of numbers
- New: Added keyword _clear to clear all workflow cache
- Improved: Unit conversion works a lot better and the code was reduced a lot
- Fixed: Time keyword not working with certain PHP versions
- Fixed: Binary mode not working for data storage conversions
- Removed: Auto updater was removed as Alfred 5 will handle future updates
- Removed: The old key `ca` to configure the workflow is removed
- Removed: Convertor library was removed
- Many fixes and improvements to list them all.
  
### v3.5.0

- Changed: Currency conversion is prioritized over cryptocurrencies, there`s thousands of useless cryptocurrencies and some use the same code, for example British Pound and Crypto Good Boy Points both use the code GBP, now if you try to convert 100 GBP it will default to British Pound, you can still configure it to use the crypto if you prefer it that way
- Fixed: Crytocurrency to currency noy working correctly when the currency cache was expired

### 3.4.0

- New: You can now provide a workflow variable to define the duration of the currencies cache
- New: Support for more cryptocurrencies, up to 5,000
- New: You can also define custom cryptocurrencies
- Updated: Fixer.io API now uses API Layer, the workflow will check your API key to make sure the correct endpoint is used.
- Improved: Now the workflow shows a message when it's updating currency and cryptocurrencies rates
- Improved: Replaced file_get_contents with curl for some operations
- Fixed: datastorage_force_binary was not working as expected
- Fixed: No output in cryptocurrencies when "from" and "to" were the same
- Fixed: Error on updater not working correctly with some PHP versions

### 3.3.0

- Updated dependencies

### 3.2.0

- New: Added Power units w, kw, ps, and hp.
- New: Added knot
- Improved: Fixer API error handling
- Fixed: Time errors caused by workflow missing user configuration
- Fixed: Currency format error

### 3.1.1

- Improved: Mac OS Monterey support
- Fixed: Support vat percentage with decimals
- Fixed: Spelling error

### 3.0.2

- Changed: Time keyword changed to "argument required" to not display results when not required
- Improved: Configuration menu, when entering to a submenu now you can go back to the main menu instead of closing Alfred
- Improved: Updates message in results
- Improved: Previous settings migration
- Fixed: Round units conversion

### 3.0.1

- New: Quart now defaults to US Quart (0.946353 Liters) to mimic the same results as other converters. You can sill type `1 uk quart in liter` or `1 ukqt in liter` or `1 us quart in liter`
- Improved: Decimals formatting
- Improved: Added eurs keyword
- Fixed: Temperature conversion not working
- Fixed: Cache folder not created for new installations
- Fixed: Forgot to change the name of a env variable after a PR in time conversions

### 3.0.0

- New: Now the Workflow settings are saved in the workflow variables, if you configure alfred to use Dropbox now the settings of the workflow will also be synced. Existing config will be converted automatically on update.
- New: Added support for data storage: Byte, Kilobyte, Megabyte, Gigabyte, Terabyte, Petabyte, Exabyte, Zettabyte, Yottabyte, bit, Kibibyte, Mebibyte, Gibibyte, Tebibyte, Pebibyte, Exbibyte, Zebibyte, Yobibyte.
- New: Added US Gallon in Volume conversions
- New: Added Fluid Ounces in Volume conversions
- New: Gallon now defaults to US Gallon (3.78541 Liters) to mimic the same results as other converters. You can sill type `1 uk gal in liter` or `1 ukgal in liter` `1 uk gallon in liter` or `1 us gal in liter`
- New: Pint now defaults to US Pint (0.473176 Liters) to mimic the same results as other converters. You can sill type `1 uk pint in liter` or `1 uk pint in liter` `1 uk pt in liter` or `1 us pint in liter`
- New: Added stopword `as` so you can type `1 kilometer as meters` or `1km as m`, etc.
- New: Added stopword `en` (spanish) so you can type `1 kilometro en metros` or `1km en m`, etc.
- New: When downloading rates the workflow will rerun it's query to update the results
- New: The workflow was restructured and some parts were rewritten
- New: Added millas, milla, miles, mile, keywords so you can type `4 miles in feet`
- New: Updated documentation and examples
- Improved: Updater, now displays notifications using alfred and will also notify you in the results
- Improved: Updated translations
- Improved: Added space between number and unit/currency for better readability
- Improved: Removed some dependencies
- Fixed: Subtitle not formatted according the currency locale in crypto conversions
- Fixed: Error in subtitle for time conversions (milliseconds)
- Fixed: Incorrect Time Unit Conversion
- Fixed: Incorrect crypto conversion when downloading updated currency rates
- Fixed: crypto currencies result now displays default workflow icon instead of an empty space

### 2.1.0

- New: Removed exchangeratesapi as it now requires an API Key
- New: Added exchangerate.host
- New: Improved configuration. Now displays a list of time zones to choose from
- New: Improved configuration. Now displays a list of currencies to choose from
- New: Improved configuration. Now displays a list of languages to choose from
- Fixed: Unable to run/save configuration on certain cases

### 2.0.4

- New: Improved natural language for percentages 40 as a % of 50 = 80%, 20 is what % of 50 = 40%
- Fixed: Modifier keys for units (command key to copy value without format)
- Fixed: Modifier keys for percentages (command key to copy value without format)
- Fixed: Percentages now working even if there are no spaces in the query (6000-10%)
- Fixed: An error in vat calculation that when pressing enter the wrong value was copied to clipboard

### 2.0.3

- New: Improved currency conversion speed.
- New: You can use quick calculations for VAT.
- New: Added calculate clear to easily clear the workflow cache, settings or both.
- Changed: Vat action modifiers to be the same as currency, etc.
- Fixed: months to year calculation
- Fixed: updater error causing the workflow to not work

### 2.0.2

- Changed: standard output for percentage calculations
- Fixed: Error that prevented the conversion of some units
- Fixed: unit conversion displaying null in Alfred
- Fixed: vat calculations not able to use natural language
- Improved: Detection of units
- Improved: Detection of percentages
- Improved: Language translations

### 2.0.1

- New: Improved actions modifiers, more info in the docs.
- Fixed: Error when saving base currencies

### 2.0.0

- New: Complete rewrite to be more maintainable and extendable
- New: Added cryptocurrencies support
- New: Actions mofidiers, you can press CMD or option to copy the value in different formats
- New: Display exchange rate conversions in multiple currencies at once
- New: Added new workflow updater

### 1.0.7

- Fixed a bug where some configuration was saves in lowercase causing some problems with dates and currency formatting

### 1.0.6

- New: pixels, em, rem, pt calculations example: 12px or 12px to em or 12px to rem or 12px to pt
- New: Added today + X workdays
- Updated translations
- Fixed currency now works correctly with decimals and commas
- Fixed some units conversion not working

### 1.0.5

- New: Added new way to configure the workflow
- New: Added OneUpdated for automatic updates
- Fixed percentage calculation error
- Improved decimal places to ignore last 0 for example 17.50 becomes 17.5
- Improved the workflow, cleaning and removing nodes and code

### 1.0.4

- Fixed speed calculations not working

### 1.0.3

- Changed currency cache expiration to 2 hours for fixer.io and 12 hours for exchangerates

### 1.0.2

- Added support for fixer.io
- Some cleanup

### 1.0.1

- FIXED Decimal pints to display values correcly from currencies
- FIXED currency conversions to base currency always displayed the $ symbol
- FIXED currency conversions from base currency eur to EUR triggered error

### 1.0.0

- Initial release

## License

This project is licensed under the MIT License. See [LICENSE.md](LICENSE.md) for details.
