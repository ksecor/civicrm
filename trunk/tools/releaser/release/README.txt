Release version 0.2
Written by Dan Coulter (dancoulter@users.sourceforge.net)
Project Homepage: http://dancoulter.com/release/
Sourceforge Project Page: http://www.sourceforge.net/projects/release/
Released under GNU Lesser General Public License (http://www.gnu.org/copyleft/lgpl.html)
For more information about this application, please visit http://dancoulter.com/release/
    or http://www.sourceforge.net/projects/release/

This little script can automate file releases on SourceForge.net.  It takes one step to
do all the stuff you used to have to do. If you're using Subversion, it'll even grab
the specified release from a tag and package the files for you, upload them, and stick
them into the release.

How to use:
    1.  Download the script from http://sourceforge.net/project/showfiles.php?group_id=166650
    2.  Unzip the file into a folder your computer or server.
    3.  Make release.php executable (in Windows, create a batch file to load the file
        with the PHP interpreter).
    4.  Configure the release.ini file. The file contains documentation on options.
    5.  Run release.php with the following syntax (in linux):
            ./release.php <version number> <password (optional)>
        For example:
            ./release.php 1.0-beta passwd12345
        If your password has a space, enclose it in quotes.  The password is only
        required if you did not specify a password in the release.ini file.

Caveats:
    1.  This is very early draft of this program.  I sincerely doubt that it will mess
        anything up, but if it does, it isn't my fault.  I am currently using it to
        create file releases for my phpFlickr project and I haven't had any problems
        yet.
    2.  The file's documentation sucks at the moment.  I will be going in to add a lot
        more documentation later, but if you want to go ahead and start hacking away at 
        the script to make it better, be my guest.  In fact, send along anything you
        come up with and I'll consider adding it into a future version.
    3.  This script is only currently designed to work with .gz, .bz2 and .zip files.
        It will categorize them as source packages when it adds them to your project.
        This can certainly be customized and I intend to add more types in the future
        and I will probably make it easier for you to select which types of files to
        use on a per-project basis.
    4.  Release is a PHP script that has been designed to run as a command-line script.
        It has been tested on Debian Sarge running PHP CLI 4.3.10.  There is no reason 
        it shouldn't run on PHP5, but will not correctly run on anything less than
        PHP version 4.3.0.  The next version should make a correction to let it run
        on a lower version of PHP.  It should also run in Windows or on Mac as long
        as you have PHP installed.  On Windows, you will need to run it in a batch file
        to load it with the PHP interpreter.
    5.  Currently the script does not alert people monitoring your project or create a
        news item.  You can expect at least the former in version 0.2.