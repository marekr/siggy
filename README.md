# siggy

siggy is one of the oldest wormhole mapping tools for EVE Online since 2011.

It has gone through a long storied history from a single PHP file and some JS trying to render
in the IE6 based Eve Ingame Browser to various hacked up monstriosities as I was only a programming newb years ago.

The current code base is a weird kludge of Laravel and the 12 years of tech debt.

The frontend aka the javascript is also part of that 12 years of tech debt. It has undergone evolution from
primitive JS with jquery to the current iteration of leveraging typescript though not entirely.

## Setup
Unforunately, setting up siggy is not for the newb.

Requirements are:
- PHP 7.2+
-- ZeroMQ PHP Extension
-- Redis PHP Extension
- MySQL 5.6+
- CrestScribe service (Windows service unforunately) https://github.com/marekr/CrestScribe

siggy originally ran on a Windows server that was provided long ago, while siggy should run entirely just fine on Linux. 
The CrestScribe service which handles refreshing the ESI api is still stuck to Windows.

### MySQL Settings
Off hand, these settings are required in MySQL

innodb_large_prefix=1
innodb_file_format=barracuda


### .env

The .env.sample must be copied and renamed to .env for deployment.
Configure as needed but pretty much most of those items are required.