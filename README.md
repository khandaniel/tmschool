# tmschool
Tool to send out school assignment forms via Viber


First thing, you have to create an Google Spreadsheet which has first column with names of people who has assignments and another column named(First string  = PhoneNumbers) with global phone numbers (must include country code but without '+' sign).<br />
Weeks must be in the following format: m/d/yyyy (e.g. 4/12/2017). It has to be Monday.<br />
Example of the Spreadsheet: [link] <br />
Second thing is creating a directory with your website. <br />
Third: <code>composer update</code><br />
Fourth: get your credentials from Google Dev Console and move credentials file to the parent folder of your website's root.<br />
Fifth: Seem to be done!<br />
