# Hide App Dock Icon

This script allows you to hide the Dock icon of an App (tested til MacOS Sierra).

## Warning

This script removes the settings of the newly hidden App (like if you newly
installed it). E.g. in Twitter-Apps you have to relogin and reconfigure the app.

Do a backup of the app to be safe, the code signing process can break some apps
(a copy of the original Info.plist file can also be found in the `/tmp` folder
with the name `APPNAME-Info-TIME.plist~backup`, in case you forgot to backup and
something went wrong in the Info.plist).

## Run

You need `sudo` to rewrite some Info.plist files and to sign the App. Run the
PHP script by writing the following command into your Terminal:  
`sudo php path/hideDockIcon.php`  
(you can also write `sudo php ` and drag the `hideDockIcon.php` file into the
Terminal window to add the absolute path.)

After running the script you will be asked for the App name. Enter the name
(like `Twitter`) if the app is in the Applications folder or paste the full
(absolute) app path to hide the Dock icon.

After finishing restart the app. You can do this in the Terminal as well by
typing:

```bash
killall APPNAME
open -a APPNAME
```

By running the script a second time on the same app you can undo the hide process
and reshow the Dock icon.

## Why should I use this?

For apps which are running all the time, maybe even in the background and you
never click on their Dock icon (e.g. the official Twitter app or Tweetbot), or
apps which are running for a short time just for a cron job (e.g. if you use
my [Apple Reminders to Todoist](https://github.com/FirePanther/Apple-Reminders-to-Todoist)
script which is running an Apple script to read out reminders from the native
Reminders app just to transfer them to Todoist).  
To have a nice, empty, clean Dock you can just hide all the apps whose app icons
you never click on.

![Preview: Some hidden Dock icons](http://i.dv.tl/Screenshot_2016-11-21_at_16.20.21.jpg)

## License

*(but if you want you can add my name or repo URL in your script :D)*

```
        DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE 
                    Version 2, December 2004 

 Copyright (C) 2004 Sam Hocevar <sam@hocevar.net> 

 Everyone is permitted to copy and distribute verbatim or modified 
 copies of this license document, and changing it is allowed as long 
 as the name is changed. 

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE 
   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION 

  0. You just DO WHAT THE FUCK YOU WANT TO.
```
