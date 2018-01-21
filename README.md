# tmschool
Tool to send out school assignment forms via Viber


First thing, you have to create an Google Spreadsheet that has first (which actually means second) column with names of people who has assignments and another column named(First string  = PhoneNumbers) with global phone numbers (must include country code but without '+' sign).<br />
Weeks must be in the following format: m/d/yyyy (e.g. 4/12/2017). It has to be Monday.<br />
Example of the Spreadsheet: [<a href="http://bit.ly/2DVpEzC">link</a>] <br />
Second thing is creating a directory with your website. <br />
Third: <code>composer update</code><br />
Fourth: get your credentials from Google Dev Console and move credentials file to the parent folder of your website's root.<br />
Fifth: Here's when something works and something doesn't. Now you go to the code and change <code>switch case</code> because it uses cyrillic letters "А" for assistant and "Ч" for reader. A message you will send also needs to be edited. And $range too. It depends on the range used in your spreadsheet. In my example table it is equal to <code>'2018'!A1:BD13</code> Note: 2018 is for List(Page) name.  <br />

Seem to be done!<br />
