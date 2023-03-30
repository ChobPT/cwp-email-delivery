#!/bin/bash

# Default theme name is "original"
theme="original"

# Default action is to copy files
action="copy"
chmod 644 /var/log/maillog
# Parse command-line options
while getopts "t:-:u" opt; do
  case "$opt" in
    t) theme="$OPTARG" ;;
    -) case "${OPTARG}" in
         theme=*) theme="${OPTARG#*=}" ;;
         uninstall) action="uninstall" ;;
         *) echo "Invalid option: --$OPTARG" >&2; exit 1 ;;
       esac ;;
    u) action="uninstall" ;;
    *) echo "Invalid option: -$opt" >&2; exit 1 ;;
  esac
done
shift $((OPTIND-1))

if [ "$action" == "copy" ]; then
  # Copy files
  cp email_delivery.js.twig "/usr/local/cwpsrv/var/services/users/cwp_theme/$theme/js/modules/"
  cp mod_email_delivery.html "/usr/local/cwpsrv/var/services/users/cwp_theme/$theme/"
  cp email_delivery.php /usr/local/cwpsrv/var/services/user_files/modules/
  cp email_delivery.ini /usr/local/cwpsrv/var/services/users/cwp_lang/en/

  # Add lines to menu files
  sed -i '/{% if ("mail_routing" in rmenu ) or (swmenu==1) %}/i\{% if ("email_delivery" in rmenu ) or (swmenu==1) %}        <li class="search"><a href="?module=email_delivery">E-mail Delivery</a></li>{% endif %}' "/usr/local/cwpsrv/var/services/users/cwp_theme/$theme/menu_locked.html"
  sed -i '/{% if ("mail_routing" in rmenu ) or (swmenu==1) %}/i\{% if ("email_delivery" in rmenu ) or (swmenu==1) %}        <li class="search"><a href="?module=email_delivery">E-mail Delivery</a></li>{% endif %}' "/usr/local/cwpsrv/var/services/users/cwp_theme/original/menu_left.html"
else
  # Remove files
  rm "/usr/local/cwpsrv/var/services/users/cwp_theme/$theme/js/modules/email_delivery.js.twig"
  rm "/usr/local/cwpsrv/var/services/users/cwp_theme/$theme/mod_email_delivery.html"
  rm /usr/local/cwpsrv/var/services/user_files/modules/email_delivery.php
  rm /usr/local/cwpsrv/var/services/users/cwp_lang/en/email_delivery.ini

  # Remove lines from menu files
  sed -i '/{% if ("email_delivery" in rmenu ) or (swmenu==1) %}/d' "/usr/local/cwpsrv/var/services/users/cwp_theme/$theme/menu_locked.html"
  sed -i '/{% if ("email_delivery" in rmenu ) or (swmenu==1) %}/d' "/usr/local/cwpsrv/var/services/users/cwp_theme/$theme/menu_left.html"
fi

