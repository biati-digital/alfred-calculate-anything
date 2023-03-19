# Calculate Anything

Calculate Anything is a workflow for Alfred 4 and Alfred 5, that uses **natural language** and is able to calculate multiple things like currency, cryptocurrency, time, vat, px, em, rem, percentage, and more.

<p align="center">
<img src="https://i.ibb.co/LZnQcCL/calculate-anything-gh.jpg">
</p>

## Why

There are several conversion workflows out there but I wanted a workflow that worked naturally. For example, you can open Alfred, type `100 + 9` and get a result. No need for keywords or hotkeys -- it just works. With Calculate Anything you can do the same, simply type `100 + 16%`, `100 euros to usd`, `100km in cm` or `100 years to hours` and many more.

## Features

- **Natural language** - type `100 euros to dollars`, `100 euros in usd`, `100€ to $`, `100eur usd`, `100 euros a dolares` -- it does not matter, the same result will be displayed
- **Multiple languages** - the workflow has support for natural language in English, Spanish and Swedish
- **Currency** - Up to 168 currencies
- **Cryptocurrency** - Support for 5,000 cryptocurrencies
- **Units** - `100 kilometers to meters` or `100 km to m` or simply `100km m`
- **Data Storage** - `100 gigabytes in megabytes`, `2 gb to mb`, `400MiB to kib`, `2tb gb` etc.
- **Percentages** - `100 + 16%`, `100 - 16%`, `40 as a % of 50`, `20 is what % of 50` etc.
- **px,em,rem,pt** - `12px` or `12px to em` or `12px pt`
- **Time** - `time +15 years`, `time now plus 6 hours` or convert a timestamp
- **VAT** - value added tax calculations, we all need this
- **Translations** You can create your own translations to display results in your language
- **Keywords** Extend the natural language support so you can type `100 dolares a pesos` and see the result of `100usd to mxn`

## Requirements for Mac OS Monterey Users and up

Starting from Mac OS Monterey Apple removed PHP so you have to install it manually, that can easily be done with [Homebrew](https://brew.sh/), just open your terminal and paste the commands below:

1.- Install Homebrew

```
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

2.- Install PHP

```
brew install php
```

That's it, you need to have the latest version of Alfred and will automatically find the PHP version that you install.

Mac OS Big Sur users and below do not need to do anything as your Mac OS already includes PHP.

## Download

Make sure to download the latest released directly from the releases page. [Download here](https://github.com/biati-digital/alfred-calculate-anything/releases/).

## Configuration

If you are using Alfred 4 please check the [README for Alfred 4](https://github.com/biati-digital/alfred-calculate-anything/blob/master/README-ALFRED4.md).

If you are using Alfred 5 you can use the new workflow configuration panel.

<p align="center">
<img src="https://i.ibb.co/k65g7v7/config.webp">
</p>

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

### Currency API Key (not required but will provide a lot more currencies)

By default the workflow will use exchangeratehost api to make the conversion. exchangeratehost only support 32 currencies; if you need support for additional currencies supported by Calculate Anything, you need to get a _free_ API key from [https://fixer.io](https://fixer.io) -- it takes less than a minute! You can configure the API key in the new workflow configuration window.

### Currency Symbols

You can use currency symbols in your query. For example `100¥ to €` will be converted from 100 Japanese yen to the equivalent in Euros. Here is a list of available symbols:

**Please note: This list only shows symbols that can be used instead of the currency code, the workflow supports up to 168 currency codes.**

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
₹          | INR            | Indian rupee
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
₺          | TRY            | Turkish Lira
TT$        | TTD            | Trinidad and Tobago dollar
TT$        | TTD            | Trinidad and Tobago dollar
₴          | UAH            | Ukrainian hryvnia

## Cryptocurrency

Calculate Anything can convert between 5,000 cryptocurrencies and you can define your own. Again, you can use natural language or simply pass the currency symbol.

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

### Cryptocurrency Api Key

You need to get a free API key from [https://coinmarketcap.com/api/pricing/](https://coinmarketcap.com/api/pricing/). This takes less than a minute.

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

### Unit Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Return**
Press Return to copy the value with format, for example 2,376.54
- **Command + Return**
Press Command + Return to copy the value without formatting, for example 2376.54
- **Option + Return**
Press Option + Return to copy the value of a single unit, for example 23.76

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
nmi     | Nautical Mile
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

Do you prefer **1 MB = 1024 KB**? No problem, you can configure it using the Configure Workflow window.

### Data Storage Modifiers

When a result is displayed you can use action modifiers to copy the value in different formats:

- **Return**
Press Return to copy the value with format, for example 2,376.54
- **Command + Return**
Press Command + Return to copy the value without formatting, for example 2376.54

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

## Languages

This is a list of available languages:

Language   | Code
------     | -----------
English    | en_EN
Spanish    | es_ES
Swedish    | sv_SE

To create your own translation, just follow these steps:

1. Copy and paste `/lang/en_EN.php` and `/lang/en_EN-keys.php` into the same folder
2. Change the name of the pasted files to your country lang code, for example `ru_RU.php` and `ru_RU-keys.php`
3. Open and translate `ru_RU.php`
4. Open and modify `ru_RU-keys.php`. **Read more about this file in the section Keywords**.
5. Share it with the world -- and me! (I welcome pull requests or links to services like pastebin.com)

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

## Performance

For currency, percentage and unit conversions, Calculate Anything will only process the query if it begins with a digit and has at least 3 characters. Time and VAT conversions need a keyword because they are not often used.

## Deleting cache

The workflow stores information about currency and cryptocurrency rates, to clear the cache open alfred and type `_caclear`

## Changelog

The Changelog is available [here](https://github.com/biati-digital/alfred-calculate-anything/blob/master/CHANGELOG.md).
