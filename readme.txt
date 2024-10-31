=== POUCO Import Users ===
Contributors: Pouco, Morgan JOURDIN
Tags: users, csv, import, create, update
Requires at least: 4.9
Tested up to: 5.0.2
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2

Importing users into WordPress has never been easier. Don't waste your time creating users by hand anymore.


== Description ==

POUCO Import Users is the simple and efficient tool for importing users.

You can now use it in WordPress. After activating it, you can go to the users menu => import and load your CSV file from all your users.

= What is CSV file? =

A CSV file is an export from an Excel or Open Office or Libre Office table.
Excel: [tutorial](https://support.office.com/en-us/article/Import-or-export-text-txt-or-csv-files-5250ac4c-663c-47ce-937b-339e391393ba)
Open Office: [tutorial](https://wiki.openoffice.org/wiki/CSV_export)
Free office: [tutorial](https://help.libreoffice.org/Calc/Importing_and_Exporting_CSV_E)

= Why use POUCO Import Users to import your users? =

Have you ever wanted to import users?  No problem, you will love using in only 2 clicks you can import all your users or update them.

POUCO Import Users can import your users directly, you will no longer have to waste time creating or updating them by hand one by one.

The import is done in 2 steps:
- Creating the CSV file correctly formatted (user_login and user_email is required)
- Importing the CSV file

POUCO Import Users will take care of the rest. It will show you the import progress reports and errors if any.

= What our users think of POUCO Import Users?

> "@poucoimportuser Nice plugin, it is easy and straightforward to update my datas!" -[Geraud Henrion](https://twitter.com/GeraudHenrion/status/1086234092591636480)

= Is POUCO Import Users Free? =

POUCO Import Users is completely free.

= Who we are? =

We are a small agency "[Pouco](https://agence.pouco.ooo/)" with a biting edge and want to make WordPress even more fun.

= Get in touch! =

* Website: [Pouco](https://agence.pouco.ooo/)

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Installation ==

= WordPress Admin Method =

1. Go to you administration area in WordPress `Plugins > Add`
2. Look for `POUCO Import Users` (use search form)
3. Click on Install and activate the plugin
4. Optional: find the settings page through `Users > Import`

= FTP Method =

1. Upload the complete `POUCO Import Users` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Optional: find the settings page through `Users > Import`


== Frequently Asked Questions ==

= What is CSV file? =

A CSV file is an export from an Excel or Open Office or Libre Office table.
Excel: [tutorial](https://support.office.com/en-us/article/Import-or-export-text-txt-or-csv-files-5250ac4c-663c-47ce-937b-339e391393ba)
Open Office: [tutorial](https://wiki.openoffice.org/wiki/CSV_export)
Free office: [tutorial](https://help.libreoffice.org/Calc/Importing_and_Exporting_CSV_E)

= How to format the CSV file ? =

- You can download an exemple format when the plugin is active (See `Users > Import`).
- When you format, use UTF-8 format
- The CSV file use a separator for separate the datas. POUCO Import Users use the semicolons for the separation.

= What are the required columns in the CSV file? =

- User Email
- User Login

= How update an user or create an user? =

In your CSV file, you will add all the users you want to create or update (no specific order). POUCO Import Users will automatically sort for you.
When you update an user, you can don't change the password but you can change the others meta user.
When you create an user, POUCO Import Users will automatically send an email so that the user can create his password.

= Warning  =

If you are a big CSV file with new's users, we advise you to use a plugin for sending mass mail as for example: `mailjet` or `malchimp`.
These plugins go through a server dedicated to sending mass mail and it will avoid that the mail falls into your spam or that your mail server is blacklisted.


== Screenshots ==

1. Setting page
2. Import users
3. Display errors

== Changelog ==

= 1.0.0 =
* The new version

== Upgrade Notice ==

No upgrade for the moment
