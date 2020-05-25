# Calculate Anything

Calculate Anything is a workflow for Alfred 4, that uses **natural language** and is able to calculate multiple things like currency, time, vat, px, em, rem, percentage, etc.

## Why

There are several workflows out there but i just needed a workflow that worked naturally for example you can open alfred and type 100 + 9 and alfred will give you a result no need for keywords or hotkeys it just works, i wanted to be able to just type 100 + 16% or 100 euros to usd or 100km to cm or 100 years to hours and a large etc. and so this workflow was created.

## Features

- **Natural language** - type 100 euros to dollars or 100 euros in usd or 100€ to $ or 100eur usd or 100 euros a dolares. It does not matter, the same result will be displayed.
- **Currency** - Up to 168 currencies
- **Cryptocurrency** - Support for 100 cryptocurrencies
- **Units** - 100 kilometers to meters or 100 km to m or simply 100km m
- **Percentages** - 100 + 16% | 100 - 16% etc.
- **PX,Em,Rem,Pt** - 12px or 12px to em or 12px pt
- **Time** - time +15 years? now plus 6 hours? or need to convert a timestamp?
- **VAT** - value added tax calculations, we all need this
- **Translations** You can create your own translations to display results in your language
- **Keywords** Extend the natural language in the queries so you can type 100 dolares a pesos and the code will see 100usd to mxn

## Download

Download directly from the releases page, make sure to download the latest release. [Download here](https://github.com/biati-digital/alfred-calculate-anything/releases/)

## Base Configuration

There's only one global configuration and is the language, this configuration will be used to display the messages in your own language. View the Translations secction for the available languaes.

Configure it with.

- **calculate configure** and select **Set base language** and enter the language for example es_ES

[View the configuration section for more info](#configuration)

Please check the translations section to see if a translation to your language is available. **Default is en_EN**

## Currency

You can use natural language or type a few characters and that's all, for example:

```
- 100 euros to dollars
- 100 euros in dollars
- 100 euros a dolares (you can also write it in your own language)
- 100 usd to mxn
- 100€ to $
- 100 usd in mxn
- 100 usd yen
- 100usd eur
- 100eur (If no target the currency will be converted to the base currencies that you configured)
```

All this examples will simply work, you can add spaces between the value and the currency or don't.
<p align="center">
<img src="./assets/currency.gif">
</p>

### Currency Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Enter**
Press enter to copy the value with format for exaple: 2,376.54
- **Command**
Press command + enter to copy the value without format for exaple: 2376.54
- **option + enter**
Press option + enter to copy the value of 1 for example: 23.76

### Currency Options

By default the workflow will use exchangerates api to make the conversion, exchangerates has only support for 32 currencies, if you need support for all the currencies that this worflow includes you need to get a FREE API Key from [https://fixer.io](https://fixer.io) it takes less than a minute.

The following options are available for the currency.  Simply launch Alfred and type **calculate configure** and select any of the options below. [View the configuration section for more info](#configuration)

- **Add base currency**
This will become your base currency, if you type 100eur it will automatically be converted to the currencies you define here. You can enter multiple currencies at once separated by comma for example: USD, EUR, MXN
- **Delete base currency**
If you no longer want a base currency you can select this option to list all configured base currencies, you can delete a currency by simply presing enter
- **Set currency locale**
Used to give format to the converted amount using the money format of your contry
- **Set Fixer API**
Set your fixer API Key for support more currencies

### Currency Symbols

You can use currency symbols in your query for example **100¥ to €** will be converted to 100JPY to EUR, here is a list of available symbols.

**If by any chance you don't remember the currency symbol or abbreviation simply type calculate list and select "List Available Currencies" (view the gift above)**

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

You can use this in conjunction with currency to convert 100 cryptocurrencies to up to 168 currencies, again you can use natural language or simply pass the currency simbol and that's all.

```
- 2 bitcoin to dollars
- 0.1 bitcoin in dollars
- 5 bitcoins in ethereum
- 1 ethereum to ¥
- 10 ethereum in mxn
- 1eth btc
- 1btc (If no target the currency will be converted to the base currency that you configured)
```

### Cryptocurrency Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Enter**
Press enter to copy the value with format for exaple: 2,376.54
- **Command**
Press command + enter to copy the value without format for exaple: 2376.54
- **option + enter**
Press option + enter to copy the value of 1 for example: 23.76

### Cryptocurrency Options

You need to get a FREE API Key from [https://coinmarketcap.com/api/pricing/](https://coinmarketcap.com/api/pricing/) it takes less than a minute.

The following options are available for cryptocurrency.  Simply launch Alfred and type **calculate configure** and select any of the options below. [View the configuration section for more info](#configuration)

- **Set Coinmarketcap API**
Select this option and paste your API key and press enter to save it.


## Units

You can write your query using natural language or just a few characters, either way this workflow will give you the result you need.

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

If you don't remember the unit abbreviation just simply type the name for example instead of "oz" you can type "ounce" or "ounces" or even use words in your own language for exaple "onza" or "onzas" in spanish.

<p align="center">
<img src="./assets/unit.gif">
</p>

Finally if you still don't remember the unit abbreviation or it's name simply type **calculate list** and select **List Available Units** you can type to filter, etc.

### Units Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Enter**
Press enter to copy the value with format for exaple: 2,834.95
- **Command**
Press command + enter to copy the value without format for exaple: 2834.95
- **option + enter**
Press option + enter to copy the value with complete format for example: 2,834.95kg

### Units Options

The following options are available.  Simply launch Alfred and type **calculate configure** and select any of the options below. [View the configuration section for more info](#configuration)

- **Set System of Measurement**
Here you can define your System of Measurement, this option is still in development but as the workflow grows this might be necessary for US users, by default the metric system will be used but you can define **imperial or metric**

Here is a list of all available units and their names just to make this Readme long.

#### Available Length units

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
ly      | LightYear
au      | Astronomical Unit
pc      | Parsec

#### Available Area units

Unit    | Unit Name
------  | -----------
m2      | Square Meter
km2     | Square Kilometer
cm2     | Square Centimeter
mm2     | Square Milimeter
ft2     | Square Foot
mi2     | Square Mile
ha      | hectare

#### Available Volume units

Unit    | Unit Name
------  | -----------
l       | Litre
ml      | Mililitre
m3      | Cubic Meter
pt      | Pint
gal     | Galon

#### Available Weight units

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
ust     | US short Ton

#### Available Speed units

Unit    | Unit Name
------  | -----------
mps     | Meters per Second
kph     | Kilometers Per Hour
mph     | Miles Per Hour
fps     | Feet per second

#### Available Rotation units

Unit    | Unit Name
------  | -----------
deg     | Degrees
rad     | Radian

#### Available Temperature units

Unit    | Unit Name
------  | -----------
k       | Kelvin
c       | Centigrade
f       | Fahrenheit

#### Available Pressure units

Unit    | Unit Name
------  | -----------
pa      | Pascal
kpa     | kilopascal
mpa     | MegaPascal
bar     | Bar
mbar    | Milibar
psi     | Pound-force per square inch

#### Available Time units

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

#### Available Energy/Power units

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

You can easily calculate percentages for example:

```
- 15% of 50 = 7.50  //7.5 equals to 15% of 50
- 120 + 30% = 156  //120 plus the 30% of 120
- 120 plus 30% = 156  //120 plus the 30% of 120
- 120 - 30% = 84  //120 minus the 30% of 120
- 120 minus 30% = 84  //120 minus the 30% of 120
- 30 % 40 = 75%  // 30 is 75% of 40.

Translations and natural language can also be used
- 120 más 30% = 156
```

<p align="center">
<img src="./assets/percent.gif">
</p>

## PX,Em,Rem,Pt

Just open alfred and type 12px you'll see the value converted to em, rem and pt that simple, check the examples below.

```
- 12px
- 12px to em
- 2 rem
- 2rem
- 2rem to pt

#use a custom px base or configure it in the workflow
- 12px in em base 17px
```

### PX,Em,Rem,Pt Options

The following options are available. Simply launch Alfred and type **calculate configure** and select any of the options below. [View the configuration section for more info](#configuration)

- **Set base pixels** Base pixels for calculations your value must be in px for example **16px**

## Time

Time is triggered by a keyword because is not often used so you can simply open alfred and type time

```
#converts the timestamp to a regular date
- time 1577836800

#gives you the time from now plus 15 days
- time +15 days
- time now plus 3 days

#gives you the time from now plus 3 working days
- time today + 3 workdays
- time now + 3 workdays
- time + 3 workdays

#number of days until specified date
- time days until 31 december

#the start date of current year
- time start of year

#the start date of specified year
- time start of 2021

#the end date of current year
- time end of year

#the end date of specified year
- time end of 2021

#it also works in your language
- time inicio de 2020
- time fin de 2020
- time dias hasta 31 diciembre

#Get information about date
- time 31 December, 2021 18:00:00
- time 31/12/2021 18:00:00
- time 12/31/2021 18:00:00

#Calculate the difference between two dates
- time 25 December, 2021 - 31 December, 2021
- time 31-11-2019 - 21-11-2019
- time 11/31/2019 - 11/21/2019
```

<p align="center">
<img src="./assets/time.gif">
</p>

### Time Options

The following options are available.  Simply launch Alfred and type **calculate configure** and select any of the options below. [View the configuration section for more info](#configuration)

- **Set base timezone** Base time zone to calculate dates in your time for example (America/Los_Angeles, Mexico/General, etc.)

- **Add date format** Configure a new date format so the date is displayed the way you want for example (j F, Y, g:i:s a) [More information about available values for date](https://www.php.net/manual/en/function.date.php)

- **Delete date format** It will show you a list of configured date formats, simply select the one you want to delete and press enter to remove it

### Example adding time date formats

You can add all the date formats you want simply launch alfred and type **calculate configure** and select the option Add date format then simply enter the format you want and press enter: [View the configuration section for more info](#configuration)

Time will use the language that you configure with **Set base language**

## VAT (value added tax)

With this you can calculate the vat of a given amount. Like time, vat is also triggered with the keyword "vat" you can change the keyword in the workflow.

<p align="center">
<img src="./assets/vat.gif">
</p>

Given the following query

vat of 400 (with 16% vat configured, you can configure your own percentage)

You will get

- VAT of 400 = 64 // VAT Amount
- 400 plus VAT = 464 // Amount plus vat
- 400 minus VAT = 344.82 // Amount minus vat

### VAT Options

The following options are available.  Simply launch Alfred and type **calculate configure** and select any of the options below. [View the configuration section for more info](#configuration)

- **Set VAT percentage** for example (16%)

## Translations

This is a list of available languages:

Language   | Code
------     | -----------
English    | en_EN
Spanish    | es_ES

You can create your own translation, just follow this steps.

1. Copy and paste /lang/en_EN.php and /lang/en_EN-keys.php in the same folder
2. Change the name of the pasted files to your country lang code for example ru_RU.php and ru_RU-keys.php
3. open and translate ru_RU.php
4. open and modify ru_RU-keys.php. **Read more about this file in the section Keywords**
5. Set your new languge with: calculate set language ru_RU
6. Share it with the world, just send a pull request or simply publish it to pastebin.com (or any other service) and send me a link to include your translation

## Keywords

Keywords are words that can be used when writing a query in natural language for example a keyword "ounces" will be converted to "oz", "kilometers" will be converted to "km" or "dollars" will be converted to "USD", this keywords allows the user to type in a more natural way and in their own language.

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

There is no limit in the keywords that you can add.

## **Stop Words**

Stop words are words that can be used in the query when using natural language for example **100km to cm** - here the stop word is **"to"**

Stop words are useful for two things

1. Allows the user to write more naturally and in their own language (e.g 100 dolares a mxn) "a" is the stop word
2. Are used to check if the query has to be processed for example:

**100km equals meters**

if the word **"equals"** is not registered in the stop_words array then it won't be processed. At the end this stop words are removed so 100km to cm becomes 100km cm.

You can modify stop words in the same keys file for example /lang/en_EN-keys.php.

```
'units' => [
    'hours' => 'hr',
    'hour' => 'hr',

    'stop_words' => ['to', '=']
    ...
],

```

## Configuration

You can easily configure the workflow simply by opening Alfred and typing **calculate configure** you will see a list of all the available options to configure the workflow, select the option you want and press enter then simply enter your value and again press enter to save it. Check the following gif if you have doubts.

<p align="center">
<img src="./assets/config.gif">
</p>

## Updates

~~Starting from version 1.0.5 automatic updates were implemente, you will be notified if a new update is available or if you prefer you can launch Alfred and type **calculate update** to check for updates.~~

Starting from version 2.0.0 there's a new way to search and install automatic updates, before it was necessary to press enter when you used "Calculate Anything" and it will trigger the updater but the "Enter" key is not regularly used as this workflow does not depends on this key for the queries so you'll probably ended up with an outdated version. If you want you can still use **calculate update** to check for updates but it's not necessary anymore, the workflow will do it automatically for you.

## Performance

For Currency, Percentages and Units this workflow will only process the query if it begins with a digit and it has at least 3 characters, it's really fast. Time and VAT have a keyword because those are not often used.

## Acknowledge

This workflow could not be possible without:

- [Convertor](https://github.com/olifolkerd/convertor) with some modifications
- [currency-converter-php](https://github.com/ojhaujjwal/currency-converter-php) for ExchangeRatesIo

## Changelog

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

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details