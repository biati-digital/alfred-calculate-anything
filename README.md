# Calculate Anything

Calculate Anything is a workflow for Alfred 4, that uses **natural language** and is able to calculate multiple things like currency, time, vat, px, em, rem, percentage, and more.

<p align="center">
<img src="https://i.ibb.co/LZnQcCL/calculate-anything-gh.jpg">
</p>

## Why

There are several conversion workflows out there but I wanted a workflow that worked naturally. For example, you can open Alfred, type `100 + 9` and get a result. No need for keywords or hotkeys -- it just works. With Calculate Anything you can do the same, simply type `100 + 16%`, `100 euros to usd`, `100km in cm` or `100 years to hours` and many more.

## Features

- **Natural language** - type `100 euros to dollars`, `100 euros in usd`, `100€ to $`, `100eur usd`, `100 euros a dolares` -- it does not matter, the same result will be displayed
- **Currency** - Up to 168 currencies
- **Cryptocurrency** - Support for 100 cryptocurrencies
- **Units** - `100 kilometers to meters` or `100 km to m` or simply `100km m`
- **Data Storage** - `100 gigabytes in megabytes`, `2 gb to mb`, `400MiB to kib`, `2tb gb` etc.
- **Percentages** - `100 + 16%`, `100 - 16%`, `40 as a % of 50`, `20 is what % of 50` etc.
- **px,em,rem,pt** - `12px` or `12px to em` or `12px pt`
- **Time** - `time +15 years`, `time now plus 6 hours` or convert a timestamp
- **VAT** - value added tax calculations, we all need this
- **Translations** You can create your own translations to display results in your language
- **Keywords** Extend the natural language support so you can type `100 dolares a pesos` and see the result of `100usd to mxn`

## Download

Make sure to download the latest released directly from the releases page. [Download here](https://github.com/biati-digital/alfred-calculate-anything/releases/).

## Base Configuration

There's only one global setting and that's the language. This setting will allow you to type in Alfred using your own language and also will change the notifications and texts. View the Translations section for the available languages.

Configure it with:

- In Alfred type `ca`, select **Set base language** and select the language you want to use. You can also type `ca lang`

[View the configuration section for more info](#configuration).

## Currency

You can use natural language or a few characters -- that's all! For example:

```
- 100 us dollars in mexican pesos
- 100 canadian dollars in argentinian peso
- 100 euros to dollars
- 100 euros in dollars
- 100 euros as dollars
- 100 euros a dolares (you can also write it in your own language)
- 100 usd to mxn
- 100 usd mxn
- 100usd yen
- 100€ to $
- 100 ¥ in €
- 100¥ €
- 100eur (if no target the currency will be converted to the base currencies that you configured)
```

You can set a base currencies so if you type for example **120 euros** it will be automatically converted to the currencies that you configured.

All these examples just work. You can add spaces between the value and the currency or not.
<p align="center">
<img src="/assets/gifts/currency-v3.gif?raw=true">
</p>

### Currency Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Return**
Press Return to copy the value with format, for example 2,376.54
- **Command + Return**
Press Command + Return to copy the value without formatting, for example 2376.54
- **Option + Return**
Press Option + Return to copy the value of a single unit, for example 23.76

### Currency Options

By default the workflow will use exchangeratehost api to make the conversion. exchangeratehost only support 32 currencies; if you need support for additional currencies supported by Calculate Anything, you need to get a _free_ API key from [https://fixer.io](https://fixer.io) -- it takes less than a minute!

The following options are available for each currency. Simply launch Alfred, type `ca` and select any of the options below. [View the configuration section for more info](#configuration).

- **Add base currency**
This will become your base currency, if you type `100eur` it will automatically be converted to the currencies you define here. You can define multiple currencies by repeting the process.
- **Delete base currency**
If you no longer want a base currency you can select this option to list all configured base currencies, you can delete a currency by simply pressing Return
- **Set currency format**
Used to format the converted amount, you will see a list of formats to choose from, simply press return to select a format.
- **Set Fixer API**
Set your fixer API Key for support more currencies, after you select this option just copy paste your Key and press Return to save it.

### Currency Symbols

You can use currency symbols in your query. For example `100¥ to €` will be converted from 100 Japanese yen to the equivalent in Euros. Here is a list of available symbols:

**If by any chance you don't remember the currency symbol or abbreviation simply type `ca list` and select "List Available Currencies"**

Symbol     | Currency       | Value
------     | -----------    | -----------
€          | EUR.           | Euro.
¥          | JPY.           | Japanese yen.
$          | USD.           | United States dollar.
R$         | BRL.           | Brazilian real
лв         | BGN            | Bulgarian lev
៛          | KHR            | Cambodian riel
C¥         | CNY            | Renminbi|Chinese yuan
C¥         | CNY            | Renminbi|Chinese yuan
₡          | CRC            | Costa Rican colon
₱          | CUP            | Cuban peso
Kč         | CZK            | Czech koruna
kr         | DKK            | Danish krone
RD$        | DOP            | Dominican peso
£          | GBP            | Pound sterling
¢          | GHS            | Ghanaian cedi
Q          | GTQ            | Guatemalan quetzal
L          | HNL            | Honduran lempira
Ft         | HUF            | Hungarian forint
Rp         | IDR            | Indonesian rupiah
﷼          | IRR            | Iranian rial
₪          | ILS            | Israeli new shekel
J$         | JMD            | Jamaican dollar
₩          | KRW            | South Korean won
ден        | MKD            | Macedonian denar
RM         | MYR            | Malaysian ringgit
MT         | MZN            | Mozambican metical
ƒ          | ANG            | Netherlands Antillean guilder
C$         | NIO            | Nicaraguan córdoba
₦          | NGN            | Nigerian naira
B/.        | PAB            | Panamanian balboa
Gs         | PYG            | Paraguayan guaraní
S/.        | PEN            | Peruvian Sol
TT$        | TTD            | Trinidad and Tobago dollar
TT$        | TTD            | Trinidad and Tobago dollar
₴          | UAH            | Ukrainian hryvnia


## Cryptocurrency

Calculate Anything can convert between 100 cryptocurrencies. Again, you can use natural language or simply pass the currency symbol.

```
- 2 bitcoins to dollars
- 0.1 bitcoin in dollars
- 5 bitcoins in ethereum
- 1 ethereum to ¥
- 10 ethereum in mxn
- 1eth btc
- 1btc (if no target currency is provided, the configured base currency will be used)
```

<p align="center">
<img src="/assets/gifts/crypto-v3.gif?raw=true">
</p>

### Cryptocurrency Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Return**
Press Return to copy the value with format, for example 2,376.54
- **Command + Return**
Press Command + Return to copy the value without formatting, for example 2376.54
- **Option + Return**
Press Option + Return to copy the value of a single unit, for example 23.76

### Cryptocurrency Options

You need to get a free API key from [https://coinmarketcap.com/api/pricing/](https://coinmarketcap.com/api/pricing/). This takes less than a minute.

The following options are available for cryptocurrency conversions. Simply launch Alfred, type `ca` and select any of the options below. [View the configuration section for more info](#configuration).

- **Set Coinmarketcap API**
Select this option, paste your API key and press Return to save it.


## Units

You can write your query using natural language or just a few characters. Either way works!

```
- 100 kilometers to meters
- 100 km in meters
- 100 km m
- 100km m

- 100 miles as meters
- 100miles in meters
- 100 miles to m

- 100 ounces to kilograms
- 100oz to kg
- 100oz = kg
- 100oz kg

- 10 years to months
- 10years to seconds
- 10 years hr
- 1 year to sec
- 1hr secods
- 1hr s
- 10 days hr
- 10 días a horas (use your own language)
```

If you don't remember the unit abbreviation, simply type the name of the unit. For example, instead of "oz" you can type "ounce" or "ounces" or even use words in your own language like "onza" or "onzas" in Spanish.

<p align="center">
<img src="/assets/gifts/units-v3.gif?raw=true">
</p>

Finally, if you still don't remember the unit's abbreviation or name simply type `ca list` and select **List Available Units**. From there you can type to filter, etc.

### Unit Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Return**
Press Return to copy the value with format, for example 2,376.54
- **Command + Return**
Press Command + Return to copy the value without formatting, for example 2376.54
- **Option + Return**
Press Option + Return to copy the value of a single unit, for example 23.76

### Unit Options

**There are no options available for now.**

Here is a list of all available units and their names just to make this README even longer.

#### Available Length Units

Unit    | Unit Name
------  | -----------
m       | Meter
km      | Kilometer
dm      | Decimeter
cm      | Centimeter
mm      | Milimeter
μm      | Micrometer
nm      | Nanometer
pm      | Picometer
in      | Inch
ft      | Foot
yd      | Yard
mi      | Mile
h       | Hand
ly      | Lightyear
au      | Astronomical Unit
pc      | Parsec

#### Available Area Units

Unit    | Unit Name
------  | -----------
m2      | Square Meter
km2     | Square Kilometer
cm2     | Square Centimeter
mm2     | Square Milimeter
ft2     | Square Foot
mi2     | Square Mile
ha      | Hectare

#### Available Volume Units

Unit    | Unit Name
------  | -----------
l       | Litre
ml      | Mililitre
m3      | Cubic Meter
kl      | kilolitre
hl      | hectolitre
qt      | Quart
pt      | Pint (US Pint)
ukpt    | Pint (UK Pint)
gal     | Gallon (US Gallon)
ukgal   | Gallon (UK Gallon)
floz    | Fluid ounce

#### Available Weight Units

Unit    | Unit Name
------  | -----------
kg      | Kilogram
gl      | Gram
mg      | Miligram
N       | Newton
st      | Stone
lb      | Pound
oz      | Ounce
t       | Metric Tonne
ukt     | UK Long Ton
ust     | US Short Ton

#### Available Speed Units

Unit    | Unit Name
------  | -----------
mps     | Meters Per Second
kph     | Kilometers Per Hour
mph     | Miles Per Hour
fps     | Feet Per Second

#### Available Rotation Units

Unit    | Unit Name
------  | -----------
deg     | Degrees
rad     | Radian

#### Available Temperature Units

Unit    | Unit Name
------  | -----------
k       | Kelvin
c       | Centigrade
f       | Fahrenheit

#### Available Pressure Units

Unit    | Unit Name
------  | -----------
pa      | Pascal
kpa     | Kilopascal
mpa     | Megapascal
bar     | Bar
mbar    | Milibar
psi     | Pound-force Per Square Inch

#### Available Time Units

Unit    | Unit Name
------  | -----------
s       | Second
year    | Year
month   | Month
week    | Week
day     | Day
hr      | Hour
min     | Minute
ms      | Milisecond
μs      | Microsecond
ns      | Nanosecond

#### Available Energy/Power Units

Unit    | Unit Name
------  | -----------
j       | Joule
kj      | Kilojoule
mj      | Megajoule
cal     | Calorie
Nm      | Newton Meter
ftlb    | Foot Pound
whr     | Watt Hour
kwhr    | Kilowatt Hour
mwhr    | Megawatt Hour
mev     | Mega Electron Volt


## Data Storage

You can write your query using natural language or just a few characters. Either way works!

```
- 100 gigabytes in megabytes
- 100 gigas in megas
- 100 Mebibytes in Kibibytes
- 100 gb to mb
- 100gb to kb
- 100gb mb
- 400MiB in kib
- 2tb gb
- 1b kb
```

<p align="center">
<img src="/assets/gifts/datastorage-v3.gif?raw=true">
</p>

Please note, this workflow follows the **IEC Standard (International Electrotechnical Commission)** as it had been adopted by the IEEE, EU, and NIST. That means that if you type `1MB in KB` you will get `1000 KB` but if you type `1MiB in KiB` you will get `1024 KB`, you can read more about it here [Multiple-byte_units](https://en.wikipedia.org/wiki/Byte#Multiple-byte_units)

Do you prefer **1 MB = 1024 KB**? No problem, you can configure it, checkout **Data Storage Options**.

### Data Storage Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Return**
Press Return to copy the value with format, for example 2,376.54
- **Command + Return**
Press Command + Return to copy the value without formatting, for example 2376.54

### Data Storage Options

There's no exactly "options" it's more of a way for people to overwrite the conversion and **use always the Binary mode so `1 MB = 1024 KB`**. To enable this you need to [Set a Workflow Environment Variables](https://www.alfredapp.com/help/workflows/advanced/variables/). The name should be `datastorage_force_binary` and the value should be `true`.

#### Data Storage Available Units

Unit     | Unit Name
------   | -----------
B        | Byte
kB       | Kilobyte
MB       | Megabyte
GB       | Gigabyte
TB       | Terabyte
PB       | Petabyte
EB       | Exabyte
ZB       | Zettabyte
YB       | Yottabyte
bit      | bit
KiB      | Kibibyte
MiB      | Mebibyte
GiB      | Gibibyte
TiB      | Tebibyte
PiB      | Pebibyte
EiB      | Exbibyte
ZiB      | Zebibyte
YiB      | Yobibyte


## Percentages

You can easily calculate percentages. For example:

```
- 40 as a % of 50  // 40 is 80% of 50
- 20 is what % of 50 // 20 is 40% of 50
- 15% of 50 = 7.50  // 7.5 equals to 15% of 50
- 120 + 30% = 156  // 120 plus the 30% of 120
- 120 plus 30% = 156  // 120 plus the 30% of 120
- 120 - 30% = 84  // 120 minus the 30% of 120
- 120 minus 30% = 84  // 120 minus the 30% of 120
- 30 % 40 = 75%  // 30 is 75% of 40

Translations and natural language can also be used
- 120 más 30% = 156
- 120 menos 10% = 108
- 40 como % de 50 = // 40 es 80% de 50
```

<p align="center">
<img src="/assets/gifts/percentages-v3.gif?raw=true">
</p>

## PX, EM, REM, PT

Open Alfred, type `12px` and you'll see the value converted to em, rem and pt. It's that simple. Check the examples below.

```
- 12px
- 12px to em
- 2 rem
- 2rem
- 2rem to pt

# use a custom px base or configure it in the workflow
- 12px in em base 17px
```

### px,em,rem,pt Options

The following options are available. Simply launch Alfred, type `ca` and select any of the options below. [View the configuration section for more info](#configuration).

- **Set base pixels** The base pixels for calculations (the value must be in px), for example **16px**

## Time

Given its less frequently used, time conversions require the use of the keyword **time**.

```
# converts the timestamp to a regular date
- time 1577836800

# gives you the time from now plus 15 days
- time +15 days
- time now plus 3 days

# gives you the time from now plus 3 working days
- time today + 3 workdays
- time now + 3 workdays
- time + 3 workdays

# number of days until specified date
- time days until 31 december

# the start date of current year
- time start of year

# the start date of specified year
- time start of 2021

# the end date of current year
- time end of year

# the end date of specified year
- time end of 2021

# it also works in your language
- time inicio de 2020
- time fin de 2020
- time dias hasta 31 diciembre

# get information about date
- time 31 December, 2021 18:00:00
- time 31/12/2021 18:00:00
- time 12/31/2021 18:00:00

# calculate the difference between two dates
- time 25 December, 2021 - 31 December, 2021
- time 31-11-2019 - 21-11-2019
- time 11/31/2019 - 11/21/2019
```

<p align="center">
<img src="/assets/gifts/time-v3.gif?raw=true">
</p>

### Time Options

The following options are available.  Simply launch Alfred, type `ca` and select any of the options below. [View the configuration section for more info](#configuration).

- **Set base timezone** Base time zone used to calculate dates in your time zone, search and select your zone from the list and press Return to save it.

- **Add date format** Configure a new date format so the date is displayed the way you want, for example **j F, Y, g:i:s a** ([more information about available values for date](https://www.php.net/manual/en/function.date.php))

- **Delete date format** It will show you a list of configured date formats, simply select the one you want to delete and press Return to remove it

### Example: Adding Date and Time Formats

You can add formats for your dates and times. Simply launch Alfred, type `ca` and select the option **Add date format**. Enter the format you want and press Return. [View the configuration section for more info](#configuration).

Time will use the language that you configure with **Set base language**.

## VAT (Value Added Tax)

With this you can calculate the VAT for a given amount. Like time, VAT is also triggered with a keyword. By default, the keyword is "vat" but you can change the keyword in the workflow.

<p align="center">
<img src="/assets/gifts/vat-v3.gif?raw=true">
</p>

Given the following query

```
vat of 400 (with a 16% VAT configured, a percentage you can configure)
```

Calculate Anything will provide the following conversions:

- VAT of 400 = 64 (the VAT amount)
- 400 plus VAT = 464 (the Amount plus VAT)
- 400 minus VAT = 344.82 (the Amount minus VAT, useful if you have a final amount and want to know the VAT applied)

### VAT Options

The following options are available. Simply launch Alfred, type `ca` and select any of the options below. [View the configuration section for more info](#configuration).

- **Set VAT percentage** the VAT percentage to apply,for example 16%

## Translations

This is a list of available languages:

Language   | Code
------     | -----------
English    | en_EN
Spanish    | es_ES

To create your own translation, just follow these steps:

1. Copy and paste `/lang/en_EN.php` and `/lang/en_EN-keys.php` into the same folder
2. Change the name of the pasted files to your country lang code, for example `ru_RU.php` and `ru_RU-keys.php`
3. Open and translate `ru_RU.php`
4. Open and modify `ru_RU-keys.php`. **Read more about this file in the section Keywords**.
5. Set your new language with `ca lang` (Your new language should be detected automatically)
6. Share it with the world -- and me! (I welcome pull requests or links to services like pastebin.com)

## Keywords

Keywords are words that can be used when writing a query in natural language. For example, by default, the keyword "ounces" will be converted to "oz", "kilometers" will be converted to "km" and "dollars" will be converted to "USD". Keywords allow the user to type in a more natural way and in their own language.

```
'units' => [
    'hours' => 'hr',
    'hour' => 'hr',
    'kilometers' => 'km',
    'ounces' => 'oz',
    'ounce' => 'oz',
    'hakunamatata' => 'year',  // try adding this
    ...
],

If the user the types:
1 hakunamatata to months, the result will be 12

```

There is no limit to the keywords that you can add.

## **Stop Words**

Stop words are words that can be used in the query when using natural language, for example `100km to cm`. Here the stop word is "to".

Stop words are useful for two things:

1. Allow the user to write more naturally and in their own language (e.g 100 dolares a mxn) "a" is the stop word
2. Check if the query has to be processed

For example:

`100km equals meters`

If the word "equals" is not registered in the `stop_words` array then it won't be processed. After processing, stop words are removed so `100km to cm` is understood as `100km cm`.

You can modify stop words in the same keys file, for example `/lang/en_EN-keys.php`.

```
'units' => [
    'hours' => 'hr',
    'hour' => 'hr',

    'stop_words' => ['to', '=']
    ...
],

```

## Configuration

You can easily configure the Calculate Anything workflow. Simply open Alfred, type `ca` and you will see a list of all the available options to configure the workflow. You can also filter the options for example launch Alfred and start typing `ca fixer` and you will automatically see the options filtered. To select an option just press Return.

<p align="center">
<img src="/assets/gifts/config-v3-01.gif?raw=true">
</p>

### Cache

The workflow stores some data about currency in the workflow data folder. You can delete the cache by opening Alfred and typing `ca clear`. You can decide between deleting the cache, delete stored settings or both.

## Updates

The workflow will check for updates in the background every 15 days and will notify you when a new update is available. If you want to check for updates manually, launch Alfred and type `ca update`.

<p align="center">
<img src="https://i.ibb.co/BZsmvgk/update-v3.jpg">
</p>

## Performance

For currency, percentage and unit conversions, Calculate Anything will only process the query if it begins with a digit and has at least 3 characters. Time and VAT conversions need a keyword because they are not often used.

## Acknowledgements

This workflow would not be possible without:

- [Convertor](https://github.com/olifolkerd/convertor) with some modifications

## Changelog

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
