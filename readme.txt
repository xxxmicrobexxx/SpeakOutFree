=== SpeakOut! Email Petitions ===
Contributors: 123host
Tags: petition, activism, community, email, social media
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 4.4.3
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SpeakOut! Email Petitions makes it easy to add petitions to your website and rally your community to Speak Out about a cause by using direct action.  

== Description ==  

SpeakOut! Email Petitions allows you to easily create petition forms on your site.

When visitors to your site submit the petition form, a copy of your message will be sent to the email address you specified e.g. your mayor. They can also choose to have the email BCC'd to themselves (default).  The petition message will be signed with the contact information provided by the form submitter. After signing the petition, visitors will have the option of sharing your petition page with their followers on Facebook or x. 

Signatures are stored in the WordPress database and can be easily exported to CSV format for further analysis (there is no import function). You may set a goal for the number of signatures you hope to collect and then watch as a progress bar tracks your petition's advance toward it's goal - the goal can even update automatically when a % of your goal is reached. Petitions may also be configured to stop accepting new signatures on a specified date.

More information about the plugin and how to upgrade to the fully featured Pro version can be found at the official SpeakOut! WordPress petition plugin website: https://speakoutpetitions.com

== Changelog ==

== 4.4.3 ==

* improvement: updated old changelog link at end of current changelog

== 4.4.2 ==

* improvement: updated "Tested up to" to 6.7

== 4.4.1 ==

* bug fix: Export to CSV not working - thanks Vincent R & Shawn D

== 4.4.0 ==

* bug fix: fixed XSS vulnerability in specially crafted shortcode - thanks Darius S. @ patchstack.com

== 4.3.6 ==

* bug fix: wonky CSS setting in basic theme - thanks Jordan.

== 4.3.5.2 ==

* improvement: updated "tested to" to version 6.6

== 4.3.5.1 ==

* improvement: changed CSV max_execution_time from 180 to 300 seconds and moved it so it is only applied if script is actually run.  Also reset it to system default at end of script - thanks @gideonlupine

== 4.3.4 ==

* improvement: updated compatibility to WordPress 6.5

== 4.3.3.1 ==

* bug fix: debug code removed
* bug fix: new social icons missing in free version
* bug fix: in some circumstances an error was thrown in settings page - thanks Heiko

== 4.3.3 ==

* bug fix: error being thrown by field that can't be edited in free version - thanks Giuseppe
* bug fix: somehow above error wasn't fixed in 4.3.2 - thanks Mayda

== 4.3.1 ==

* improvement: updated social icons in all style sheets

== 4.3.0 ==

* improvement: changed "twitter" to "X" - thanks Jos
* improvement: removed some superfluous code
* improvement: added Serbian language

== 4.2.6 ==

* bug fix: no error if privacy policy box not checked - thanks Mika
* improvement: SpeakOut! now has its own domain SpeakOutPetitions.com

== 4.2.3 ==

* bug fix: In certain languages if privacy was enabled to show only the first letter of the surname, it would display a `?` instead - thanks Niklas
* bug fix: signature list wouldn't display if petition ID was greater than one.  This was implemented to limit free users, but it has unintended consequences for legacy users with multiple petitions.  Thanks Jim for letting me poke around.

== 4.2.2 ==

* improvement: updated "tested to" to 6.3

== 4.2.1 == 

* bug fix: multiple email addresses in Target Email or CC Email fields would be mashed together - thanks James and someone else who reported this

== 4.2.0 ==

* bug fix: custom field 6 location wasn't being saved
* bug fix: in admin signature list, "anonymous" icon was showing HTML instead of being superscript
* improvement: added several webhooks - see https://speakoutpetitions.com/webhooks. Thanks for the idea Ben & Nick
* improvement: added email share icon to Pro version - thanks to whoever suggested it.
* improvement: displaying emails in the public signature list is now optional - with a warning that it might not be a wise idea
* improvement: translation updates

== 4.1.3.1=

* bug fix: Somehow a stray character found its way into the code which was breaking petitions. - thanks Meagan

== 4.1.2 ==

* bug fix: database creation error for new installs
* bug fix: if message was editable, formating was lost when sent to target (Pro only) - thanks Linda
* bug fix: language wasn't bein passed via URL in confirmation emails
* improvement: eliminated php warning "ob_end_flush(): failed to send buffer of zlib output compression"
* improvement: added note to when "Do not send email (only collect signatures)" is checked

== 4.1.1 ==

* bug fix: if the *petitionmessage* shortcode was used, any Markdown in the message wasn't being displayed.  Thanks Michael.
* bug fix: if email confirmation was enabled, the numbering in the public signature list reflected the total number of signatures, not just the confirmed signtures.  Thanks Rene & Martin
* bug fix: if email confirmation was enabled and public signatures spanned more than one page, scrolling didn't work as expected - thanks Rene
* bug fix: on one layout of the public signature list, the word _anonymous_ wasn't translated - thanks Myriam-Zaa
* bug fix: increased size of honorifics field in database - thanks Glen C.
* bug fix: following some actions in the admin signature list it wasn't showing the signature count
* improvement: new installs will now use the utf8mb4_general_ci character set for database text fields, instead of just utf8 - down the track I will change the character set for existing installs
* improvement: database creation now makes fields NULL instead of NOT NULL to allow for not collecting some data - down the track I will change all fields to NULL in existing installs


== 4.1.0 ==

* bug fix: Confirmation emails weren't being sent if Email From field wasn't set up properly, which was impossible.  Thanks Thomas and Rene
* bug fix: non-existant parameter being passed to function
* improvement: updated the contextual help - first time ever :o)
* improvement: clarified wording of "display signature count" and where it refers to.
* improvement: database updates so free version is ready to upgrade to Pro

== 4.0.10 ==

* improvement: changed a page title - thanks Debbie P
* bug fix: some required fields were not showing red border - thanks Debbie P
* bug fix: redirect after signing not working - thanks Debbie P

== 4.0.9 ==

* bug fix: when clicking _next_ in signature list it was displaying html - thanks Dan @tahninial

== 4.0.8 ==

* bug fix: slashes added to any apostrophes in email subject or greeting
* bug fix: missing default value in free version

== 4.0.7 ==

* bug fix: missing character would cause error in certain circumstances - thanks heiko


== 4.0.6.1 ==

* bug fix: if WordPress was installed in a subfolder, some administrator links may not have worked.  Thanks Calvin

== 4.0.6 ==

* bug fix: petition message kept adding slashes in editor when saved.  Thanks Calvin
* improvement: removed _%%Your Signature%%_ from the petition message displayed on your site.  It was causing confusion.  Thanks Razvan
* improvement: minor typo fixed
* improvement: Dutch language imrovements - thanks Michiel

== 4.0.5 ==

* bug fix: petition message losing formatting - thanks @dcbuffalo
* improvement: better data sanitization in signature list

== 4.0.4.6 ==

* bug fix: html being displayed in admin signature list
* bug fix: file was being included twice
* bug fix: reconfirming selected signatures wasn't working

== 4.0.4.5 ==

* bug fix: settings not saving due to incorrect input sanitize function
* improvement: German language update - thanks Mario

== 4.0.4.4 ==

bug fix: in some circumstances an error was thrown in settings page - thanks Heiko

== 4.0.4.2 ==

* Policy compliance: Version 3 of SpeakOut! included a method of upgrading that turned out to be a WordPress plugin policy breach (4 months later!) and some potential security issues (after 10+ years!).  This version remedies that and introduces a more cumbersome (for users), but compliant method of upgrading.
* bug fix: various fixes of things found while creating V4
* improvement: removed support for importing the original speakup plugin.  After 10 years, it's time.


[Earlier Changelog][2]

[2]: https://speakoutpetitions.com/speakout-free-changelog/ "SpeakOut! old Changelog"



== Localizations ==



* Albanian **sq_AL** Incomplete
* Arabic **ar_AR**
* Arabic **ar** (Faisal Kadri)
* Catalan **ca**  (Alberto Canals)
* Czech **cs_CZ** (Petr Å tepÃ¡n, Michal HradeckÃ½)
* Danish **da_DK** (A. L.)
* Dutch **nl_NL** (Kris Zanders, Petronella van Leusden)
* Finnish **fi_FI** 
* French **fr_FR**
* German **de_DE** (Hannes Heller, Armin Vasilico, Andreas Kumlehn, Frank Jermann)
* Hebrew **he_IL** (Oren L)
* Korean **ko_KO** (Paul Lawley-Jones)
* Icelandic **is_IS** (Hildur Sif Thorarensen)
* Italian **it_IT** ([MacItaly](http://wordpress.org/support/profile/macitaly), Davide Granti, Simone Apollo)
* Norwegian **nb_NO** (Howard Gittela)
* Polish **pl_PL** (Damian Dzieduch)
* Portuguese (Brazil) **pt_BR** (Tel Amiel)
* Romanian **ro_RO** ([Web Hosting Geeks](http://webhostinggeeks.com))
* Russian **ru_RU** ([Teplitsa](te-st.ru))
* Serbian **sr_RS** (Mikhailo Matovic)
* Slovak **sk_SK** (@Beata)
* Slovenian **sl_SI** ([MA-SEO](http://ma-seo.com))
* Spanish **es_ES**
* Swedish **sv_SE** (Susanne Nyman FurugÃ¥rd @sunyfu)


If you would like to request or contribute a specific translation not listed above, visit the [SpeakOut! Email Petitions website](http://speakoutpetitions.com/) and use the contact form.


== Installation ==

Use the automatic installer. Or...


1. Download and unzip the the plugin zip file.
2. Upload the 'speakout' folder to your '/wp-content/plugins/' directory
3. Activate SpeakOut! Email Petitions through the "Plugins" menu in the WordPress admin.


== Frequently Asked Questions ==

= Where is the FAQ? =

[https://speakoutpetitions.com/FAQ][1]
[1]: https://speakoutpetitions.com/FAQ "SpeakOut! FAQ"

== Screenshots ==

1. Public-facing petition form
2. Form for creating and editing email petitions
3. Table view of existing petitions
4. Table view of collected signatures
5. Plugin settings screen
6. Sidebar widget
7. Pop-up Petition form (widget)
8. Email confirmation screen

== Emailpetition Shortcode Attributes ==

The following attributes may be applied when using the '[emailpetition]' shortcode

= id =
The ID number of your petition (required). To display a basic petition, use this format:
'[emailpetition id="1"]'

= width =
This sets the width of the wrapper "<div>" that surrounds the petition form. Format as you would a width rule for any standard CSS selector. Values can be denominated in px, pt, em, % etc. The units marker (px, %) must be included.

To set the petition from to display at 100% of it's container, use:
'[emailpetition id="1" width="100%"]'

A petition set to display at 500 pixels wide can be achieved using:
'[emailpetition id="1" width="500px"]'

= height =
This sets the height of the petition message box (rather than the height of the entire form). Format as you would a height rule for any standard CSS selector. Values can be denominated in px, pt, em, % etc. The units marker (px, %) must be included.

A few notes on using percentages:
Using a % value only works when the "Allow messages to be edited" feature is turned offâ€”because the petition message will be displayed in a '<div>'. When "Allow  messages to be edited" is turned on, the petition message is displayed in a '<textarea>', which cannot be styled with % heights. Use px to set the height on petitions that allow message customization.

To set the message box to scale to 100% of the height of the message it contains, use any % value (setting this to 100%, 0%, 200% or any other % value has the same result). Use px if you want the box to scale to a specific height.

Examples:
'[emailpetition id="1" height="500px"]'

'[emailpetition id="1" height="100%"]'



= progresswidth =

Sets the width of the outer progress bar. The filled area of the progress bar will automatically scale proportionally with the width of the outer prgress bar. Provide a numeric value in pixels only. Do not include the px unit marker.



To display the progress bar at 300 pixels wide, use:

'[emailpetition id="1" progresswidth="300"]'



= class =

Adds an arbitrary class name to the wrapper '<div>' that surrounds the petition form. Typically used to assign the alignright, alignleft or aligncenter classes to the petition in order to float the petition form to one side of its container. To assign multiple classes, separate the class names with spaces.



Examples:

'[emailpetition id="1" class="alignright"]'

'[emailpetition id="1" class="style1 style2"]'



== Signaturelist Shortcode Attributes ==



= id =

The ID number of your petition (required). To display a basic signature list, use this format:

'[signaturelist id="1"]'



= rows =

The number of signature rows to display in the table. This will override the default value provided on the Settings page. To display 10 rows, use:

'[signaturelist id="1" rows="10"]'



= dateformat =

Format of values in the date column. Use any of the standard [PHP date formating characters](http://php.net/manual/en/function.date.php). Default is 'M d, Y'. A date such as "Sunday October 14, 2012 @ 9:42 am" can be displayed using:

'[signaturelist id="1" dateformat="l F d, Y @ g:i a"]'



= prevbuttontext =

The text that displays in the previous signatures pagination button. Default is &lt;.



= nextbuttontext =

The text that displays in the next signatures pagination button. Default is &gt;.



== signaturecount Shortcode ==

Display the number (as text) of signatures collected for a given petition:



= id =

The ID number of your petition (required).

'[signaturecount id="3"]'



== signaturegoal Shortcode ==

Display the number (as text) of goal for a given petition:



= id =

The ID number of your petition (required).

'[signaturegoal id="3"]'