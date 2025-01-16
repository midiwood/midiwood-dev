=== Elementor Forms Google Sheet Connector===
Contributors: westerndeal, abdullah17, gsheetconnector
Donate link: https://www.paypal.me/WesternDeal
Author URL: https://www.gsheetconnector.com/
Tags: elementor, elementor addons, elementor forms, elementor google sheets, metform google sheet
Docs: https://support.gsheetconnector.com/kb-category/elementor-forms-gsheetconnector
Tested up to: 6.7.1
Requires at least: 5.6
Requires PHP: 7.4
Stable tag: 1.0.20
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send your Elementor Forms and MetForm submissions directly to your selected Google Sheets or Spreadsheets.

== Description ==

 Google Sheet Connector for Elementor Forms is an addon plugin, A bridge between your [WordPress](https://wordpress.org/) based [Elementor Forms](https://wordpress.org/plugins/elementor/) or [Metform Elementor Contact Form Builder](https://wordpress.org/plugins/metform/) to [Google Sheets](https://www.google.com/sheets/about/).

 Google Sheet Connector for Elementor Forms is a powerful addon plugin that acts as a bridge between your [WordPress site](https://wordpress.org/), utilizing [Elementor Forms](https://wordpress.org/plugins/elementor/) or [Metform Elementor Contact Form Builder](https://wordpress.org/plugins/metform/), and your [Google Sheets account](https://www.google.com/sheets/about/). Easily manage form submissions by seamlessly integrating them with Google Sheets for efficient data management. 
 = 🚀A Most Popular WordPress Plugin. =

When a visitor completes and submits their information through an Elementor Form (available in Elementor PRO) or MetForm, the data is automatically sent to Google Sheets in a real-time.

[Homepage](https://www.gsheetconnector.com/) | [Documentation](https://support.gsheetconnector.com/kb-category/elementor-forms-gsheetconnector) | [Support](https://www.gsheetconnector.com/support) | [Demo](https://demo.gsheetconnector.com/) | [Premium Version](https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro)

= 📝 Elementor ➜ ✍️Google Sheet =
Get rid of making mistakes while adding the sheet settings or adding the headers ( Meta Tags ) to the sheet column. We have Launched the [Googlesheet Connector PRO version](https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro) with more automated features.

**[Free Version 1.0.12 is compatible with MetForm as well](https://wordpress.org/plugins/metform/).**
If you are using Elementor PRO or MetForm then you can use this addon plugin.

= ✨[PRO Features]✨ =
➜ Custom Google API Integration Settings
➜ Allowing to Create a New Sheet from Plugin Settings
➜ Manage Fields to Display in Sheet using Enable-Disable / Edit the Fields/ Headers Name to display in Google Sheet.
➜ Syncronize Existing Entries.(If enabled Actions After Submit to Collect Submissions)
➜ Freeze Header Settings.
➜ Header Color and Row Odd/Even Colors.

Refer to the features and benefits page for more detailed information on the features of the [Elementor Google Sheet PRO Addon Plugin](https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro)


= ⚡️ Check Live Demo =
[Demo URL: Elementor Google Sheet](https://demo.gsheetconnector.com/elementor-forms-gsheetconnector-pro/)

[Google Sheet URL to Check submitted Data](https://docs.google.com/spreadsheets/d/1Ftht9knBeuzcvZlzM4Wz6L8qsV4PiDU5ukFlFq9M6PU/edit#gid=1386620327)

= ⚡️ How to Use this Plugin =

* **Step: 1 - [In Google Sheets](https://sheets.google.com/)** 
➜ Log into your Google Account and visit Google Sheets.  
➜ Create a new Sheet and name it.  
➜ Rename or keep default name of the tab on which you want to capture the data. 
➜ Copy Sheet Name, Sheet ID, Tab Name and Tab ID (Refer Screenshots)

* **Step: 2 - In WordPress Admin** 
➜ Create or Edit the Elementor Form form from which you want to capture the data. Set up the form as usual in the Form. Thereafter, go to the new "GSheetConnector" tab.  
➜ On the "Google Sheets" tab, copy-paste the Google Sheets sheet name and tab name into respective positions, and hit "Save".

* **Step: 3 - Automated Columns in Sheet**
➜ Simply Hit Save 
➜ Test your form submit and verify that the data shows up in your Google Sheet.

= 🔥 Videos to help you get started with Elementor Google Sheets Connector =

🚀How to Install, Authenticate and Integrate Elementor Form with your Google Sheet.

[youtube https://youtu.be/m7hy8lQRFrg?si=DZHq0oE3lTH_9fdB]

= Important Notes = 

➜ You must pay very careful attention to your naming. This plugin will have unpredictable results if names and spellings do not match between your Google Sheets and form settings.

👉 [Get Google Sheet Connector Elementor today](#)

== Installation ==

1. Upload `gsheetconnector-for-elementor-forms` to the `/wp-content/plugins/` directory, OR `Site Admin > Plugins > New > Search > Elementor Google Sheets Connector > Install`.  
2. Activate the plugin through the 'Plugins' screen in WordPress.  
3. Use the `Admin Panel > Elementor Form > Google Sheets` screen to connect to `Google Sheets` by entering the Access Code. You can get the Access Code by clicking the "Get Code" button. 
Enjoy!

== Screenshots ==

1. Google Sheet Integration without authentication  
2. Permission page if user is already logged-in to there account. 
3. Permission popup-1 after logged-in to your account.
4. Permission popup-2 after logged-in to your account.
5. After successful integration - Displays "Currently Active".
6. Google Sheet settings page with input box Sheet Name, Sheet Id, Tab Name, Tab Id.
7. Get Sheet and Tab Id from the URL.
8. Google Sheet headers with Special Mail Tags.

== Frequently Asked Questions ==

= Why isn't the data send to spreadsheet? Elementor Submit is just Spinning. = 
Sometimes it can take a while of spinning before it goes through. But if the entries never show up in your Sheet then one of these things might be the reason:

1. Wrong access code ( Check debug log )
2. Wrong Sheet name or tab name
3. Wrong Column name mapping ( keep in mind that not to use capital letters, number as initial and special characters like underscores, double or single code, space etc. You can only use small letters and hyphen. )

Please double-check those items and hopefully getting them right will fix the issue.

= How do I get the Google Access Code required in step 3 of Installation? =

* On the `Admin Panel > Elementor Form > Google Sheets` screen, click the "Get Code" button.
* In a popup Google will ask you to authorize the plugin to connect to your Google Sheets. Authorize it - you may have to log in to your Google account if you aren't already logged in. 
* On the next screen, you should receive the Access Code. Copy it. 
* Now you can paste this code back on the `Admin Panel > Elementor Form > Google Sheets` screen. 

== Changelog ==

= 1.0.20 = (04-01-2025)
* PRO Showcase: Date Filter for Sync - Added a date filter option to streamline syncing for users handling large data entries.
* Enhanced Form Feed Settings UI - Upgraded the user interface for improved design and usability.
* Minor Fixes - Addressed various CSS issues and made small adjustments for a smoother experience.
* Deprecated: Manual Client/Secret Key - Replaced with Google API Configuration for enhanced security and simplicity.
* Advanced Feed Settings - Introduced an improved configuration system for better usability.
* CF7 GSheetConnector Conflict Resolved - Fixed compatibility issues for seamless integration.
* Added: The "Copy Log" button has been added.
* Fixed: Undefined error when clicking the "Copy to Clipboard" button in the System Info tab.
* Fixed: The issue with the Debug Log view and the close button has been fixed.
* Fixed: Dashboard widget formatting has been improved.

= 1.0.19 = (21-08-2024)
* Fixed : Google hasn’t verified this app error.

= 1.0.18 = (15-07-2024)
* Fixed: Some fields to show in sheet, while Enabling Manual Adding Headers for Fields entering into the Google Sheet.
* Added: UI changes for showcasing PRO Features.

= 1.0.18-beta1 = (01-07-2024)
* Compatibility : Compatible with PRO Elements Plugin.

= 1.0.17 = (04-05-2024)
* Fixed : undefined array key issues.

= 1.0.16 = (12-04-2024)
- UI Changes.

= 1.0.15 = (07-03-2024)
* UI and Add links for support,docs,upgrade to pro.
* Changed UI of MetForm Settings Page.

= 1.0.14 = (12-01-2024)
* Fixed data saving issue when both free and pro versions are active. 
* Fixed plugin not getting activated for multisite setup.

= 1.0.13 = (30-12-2023)
* Fixed validate parent plugin exists or not then show alert message display issue.

= 1.0.12 = (18-12-2023)
* Compatible with metform to send metforms submissions to google sheet, option given in Elementor --> Google Sheet, Metform Tab will be seen if Metforms is installed.

= 1.0.11 = (26-10-2023)

* Updated Google API Client Library to Version-2.12.6
* Redesigned plugin Debug log, System Status and WordPress debug log view for improved functionality and user experience.
* Developed a streamlined mechanism of Debug Log View And Close.
* For users without Google Drive and Google Sheets permissions for Authentication displayed an alert with a message.
* Fixed plugin not getting activated for multisite setup.

= 1.0.10 = (14-08-2023)

* Fixed Vulnerability to ensure data security.
* UI Changes.
* Added system status tab to assists in troubleshooting.

= 1.0.9  = (05-07-2023)
* Updated Freemius SDK version to 2.5.10

= 1.0.8 = (17-05-2023)
* compatible with pro-element plugins.


= 1.0.7 = (27-04-2023)
* Fixed : Vulnerabilities issue resolved.

= 1.0.6 = (16-03-2023)
* Fixed : New tabs are not showing in google sheet tab drop-down.

= 1.0.5 = (06-03-2023)
* Fixed : Solved compatiblity issue with Elementor Forms Google Sheet Connector Pro plugins with header of google sheet.
* Fixed : Permission validation displayed with authentication of google, if not given permissions.

= 1.0.4 = (30-11-2022)
* Fixed : Solved compatiblity issue with Elementor Forms Google Sheet Connector Pro plugins.

= 1.0.3 = (10-11-2022)
* Fixed : undefined class issues.

= 1.0.2 = (31-10-2022)
* Freemius Integration.

= 1.0.1 = (29-10-2022)
* Added Screenshot

= 1.0.0 =
* First public release
* Integrated Elementor Form with Google sheets.

