# Calculate Anything

Calculate Anything is a workflow for Alfred 4, that uses **natural language** and is able to calculate multiple things like currency, time, vat, px, em, rem, percentage, etc.

## Why

There are several conversion workflows out there but I wanted a workflow that worked naturally. For example, with Calculate Anything, you can open Alfred, type `100 + 9` and get a result. No need for keywords or hotkeys -- it just works. Type `100 + 16%`, `100 euros to usd`, `100km to cm` or `100 years to hours` and many more.

## Features

- **Natural language** - type `100 euros to dollars`, `100 euros in usd`, `100€ to $`, `100eur usd`, `100 euros a dolares` -- it does not matter, the same result will be displayed
- **Currency** - Up to 168 currencies
- **Cryptocurrency** - Support for 100 cryptocurrencies
- **Units** - `100 kilometers to meters` or `100 km to m` or simply `100km m`
- **Percentages** - `100 + 16%`, `100 - 16%`, `40 as a % of 50`, `20 is what % of 50` etc.
- **px,em,rem,pt** - `12px` or `12px to em` or `12px pt`
- **Time** - `time +15 years`, `time now plus 6 hours` or convert a timestamp
- **VAT** - value added tax calculations, we all need this
- **Translations** You can create your own translations to display results in your language
- **Keywords** Extend the natural language support so you can type `100 dolares a pesos` and see the result of `100usd to mxn`

## Download

Make sure to download the latest released directly from the releases page. [Download here](https://github.com/biati-digital/alfred-calculate-anything/releases/).

## Base Configuration

There's only one global setting and that's the language. This setting will be used to display the messages in your own language. View the Translations section for the available languages.

Configure it with:

- **calculate configure** and select **Set base language** and enter the language for example `es_ES`

[View the configuration section for more info](#configuration).

Please check the translations section to see if a translation to your language is available. **Default is en_EN**.

## Currency

You can use natural language or a few characters -- that's all! For example:

```
- 100 euros to dollars
- 100 euros in dollars
- 100 euros a dolares (you can also write it in your own language)
- 100 usd to mxn
- 100€ to $
- 100 usd in mxn
- 100 usd yen
- 100usd eur
- 100eur (if no target the currency will be converted to the base currencies that you configured)
```

You can set a base currencies so if you type for example **120 euros** it will be automatically converted to the currencies that you configured.

All these examples just work. You can add spaces between the value and the currency or not.
<p align="center">
<img src="https://i.ibb.co/W5thssY/currency.gif">
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

By default the workflow will use exchangerates api to make the conversion. exchangerates only support 32 currencies; if you need support for additional currencies supported by Calculate Anything, you need to get a _free_ API key from [https://fixer.io](https://fixer.io) -- it takes less than a minute!

The following options are available for each currency. Simply launch Alfred, type `calculate configure` and select any of the options below. [View the configuration section for more info](#configuration).

- **Add base currency**
This will become your base currency, if you type `100eur` it will automatically be converted to the currencies you define here. You can enter multiple currencies at once separated by comma for example: USD, EUR, MXN
- **Delete base currency**
If you no longer want a base currency you can select this option to list all configured base currencies, you can delete a currency by simply presing Return
- **Set currency locale**
Used to format the converted amount using the appropriate currency format for your country
- **Set Fixer API**
Set your fixer API Key for support more currencies

### Currency Symbols

You can use currency symbols in your query. For example `100¥ to €` will be converted from 100 Japanese yen to the equivalent in Euros. Here is a list of available symbols:

**If by any chance you don't remember the currency symbol or abbreviation simply type `calculate list` and select "List Available Currencies" (view the gift above)**

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

Calculate Anything can convert between 100 cryptocurrencies and 168 currencies. Again, you can use natural language or simply pass the currency symbol.

```
- 2 bitcoin to dollars
- 0.1 bitcoin in dollars
- 5 bitcoins in ethereum
- 1 ethereum to ¥
- 10 ethereum in mxn
- 1eth btc
- 1btc (if no target currency is provided, the configured base currency will be used)
```

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

The following options are available for cryptocurrency conversions. Simply launch Alfred, type `calculate configure` and select any of the options below. [View the configuration section for more info](#configuration).

- **Set Coinmarketcap API**
Select this option, paste your API key and press Return to save it.


## Units

You can write your query using natural language or just a few characters. Either way works!

```
- 100 ounces to kilograms
- 100oz to kg
- 100oz = kg
- 100oz kg

- 10 years to months
- 10years to seconds
- 1 year to sec
- 1hr s
- 10 días a horas (use your own language)
```

If you don't remember the unit abbreviation, simply type the name of the unit. For example, instead of "oz" you can type "ounce" or "ounces" or even use words in your own language like "onza" or "onzas" in Spanish.

<p align="center">
<img src="https://i.ibb.co/WPKvDLL/unit.gif">
</p>

Finally, if you still don't remember the unit's abbreviation or name simply type `calculate list` and select **List Available Units**. From there you can type to filter, etc.

### Unit Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Return**
Press Return to copy the value with format, for example 2,376.54
- **Command + Return**
Press Command + Return to copy the value without formatting, for example 2376.54
- **Option + Return**
Press Option + Return to copy the value of a single unit, for example 23.76

### Unit Options

The following options are available. Simply launch Alfred, type `calculate configure` and select any of the options below. [View the configuration section for more info](#configuration).

- **Set System of Measurement**
Here you can define your system of measurement. This option is still in development but as the workflow grows this might be necessary for U.S. users who prefer Imperial units. You can define **imperial or metric**.

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
pt      | Pint
gal     | Galon

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
<img src="https://i.ibb.co/SrwMTJR/percent.gif">
</p>

## px,em,rem,pt

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

The following options are available. Simply launch Alfred, type `calculate configure` and select any of the options below. [View the configuration section for more info](#configuration).

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
<img src="https://i.ibb.co/syHQtKg/time.gif">
</p>

### Time Options

The following options are available.  Simply launch Alfred, type `calculate configure` and select any of the options below. [View the configuration section for more info](#configuration).

- **Set base timezone** Base time zone used to calculate dates in your time zone, for example **America/Los_Angeles**, **Mexico/General**, etc.

- **Add date format** Configure a new date format so the date is displayed the way you want, for example **j F, Y, g:i:s a** ([more information about available values for date](https://www.php.net/manual/en/function.date.php))

- **Delete date format** It will show you a list of configured date formats, simply select the one you want to delete and press Return to remove it

### Example: Adding Date and Time Formats

You can add formats for your dates and times. Simply launch Alfred, type `calculate configure` and select the option **Add date format**. Enter the format you want and press Return. [View the configuration section for more info](#configuration).

Time will use the language that you configure with **Set base language**.

## VAT (Value Added Tax)

With this you can calculate the VAT for a given amount. Like time, VAT is also triggered with a keyword. By default, the keyword is "vat" but you can change the keyword in the workflow.

<p align="center">
<img src="https://i.ibb.co/HVWx7wq/vat.gif">
</p>

Given the following query

```
vat of 400 (with a 16% VAT configured, a percentage you can configure)
```

Calculate Anything will provide the following conversions:

- VAT of 400 = 64 (the VAT amount)
- 400 plus VAT = 464 (the Amount plus VAT)
- 400 minus VAT = 344.82 (the Amount minus VAT)

### VAT Options

The following options are available. Simply launch Alfred, type `calculate configure` and select any of the options below. [View the configuration section for more info](#configuration).

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
5. Set your new languge with `calculate set language ru_RU`
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
2. Check the query has to be processed

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

You can easily configure the Calculate Anything workflow. Simply open Alfred, type `calculate configure` and you will see a list of all the available options to configure the workflow. Select the option you want, press Return and then enter the value you wish to set. Press Return again to save it. Check the following gif if you have doubts.

<p align="center">
<img src="https://i.ibb.co/Y0qQJhf/config.gif">
</p>

### Cache

The workflow stores some data about currency and other values in a cache. You can delete the cache by opening Alfred and typing `calculate clear`. You can decide between deleting the cache, delete stored settings or both.

## Updates

~~Starting from version 1.0.5 automatic updates were implemented and you will be notified if a new update is available or if you prefer you can launch Alfred and type `calculate update` to check for updates.~~

Starting from version 2.0.0, there's a new way to search and install automatic updates. It was previously necessary to press Return when triggerung the updater but since Return is not regularly used it was easy to end up with an outdated version. If you want you can still type `calculate update` to check for updates but it's not necessary any more. The workflow will do it for you automatically.

## Performance

For currency, percentage and unit conversions, Calculate Anything will only process the query if it begins with a digit and has at least 3 characters. Time and VAT conversions need a keyword because they are not often used.

## Acknowledgements

This workflow would not be possible without:

- [Convertor](https://github.com/olifolkerd/convertor) with some modifications
- [currency-converter-php](https://github.com/ojhaujjwal/currency-converter-php) for ExchangeRatesIo

## Changelog

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
