# cwp-email-delivery
Give CWP (Control Web Panel) a way to check the recent mail delivery status of their account (Similar to Track Delivery of cPanel)

To install:
- Download
- Extract wherever
- sh install.sh


Optional arguments:

--theme=original (in case your CWP installation is running some other theme, defaults to 'original')

To uninstall:

sh install.sh--uninstall

Known issues:
- No translation is available, english only
- Only showing for the main domain of an account, it's been a fight to get any other domains to show

To Do:
- Add a more detailed status to the delivery
- Allow filtering by domain, day
- Add translation
- Add SpamAssassin Score
