<?php
set_time_limit(0);
require_once('PEAR/PackageFileManager.php');
$test = new PEAR_PackageFileManager;

$packagedir = 'C:/Web Pages/chiara/phpdoc';

$e = $test->setOptions(
array('baseinstalldir' => 'PhpDocumentor',
'version' => '1.3.0RC3',
'packagedirectory' => $packagedir,
'state' => 'beta',
'filelistgenerator' => 'cvs',
'notes' => 'PHP 5 support and more, fix bugs

This will be the last release in the 1.x series.  2.0 is next

Features added to this release include:

 * Full PHP 5 support, phpDocumentor both runs in and parses Zend Engine 2
   language constructs.  Note that you must be running phpDocumentor in
   PHP 5 in order to parse PHP 5 code
 * XML:DocBook/peardoc2:default converter now beautifies the source using
   PEAR\'s XML_Beautifier if available
 * inline {@example} tag - this works just like {@source} except that
   it displays the contents of another file.  In tutorials, it works
   like <programlisting>
 * customizable README/INSTALL/CHANGELOG files
 * phpDocumentor tries to run .ini files out of the current directory
   first, to allow you to put them anywhere you want to
 * multi-national characters are now allowed in package/subpackage names
 * images in tutorials with the <graphic> tag
 * un-modified output with <programlisting role="html">
 * html/xml source highlighting with <programlisting role="tutorial">

From both Windows and Unix, both the command-line version
of phpDocumentor and the web interface will work
out of the box by using command phpdoc - guaranteed :)

WARNING: in order to use the web interface through PEAR, you must set your
data_dir to a subdirectory of your document root.

$ pear config-set data_dir /path/to/public_html/pear

on Windows with default apache setup, it might be

C:\> pear config-set data_dir "C:\Program Files\Apache\htdocs\pear"

After this, install/upgrade phpDocumentor

$ pear upgrade phpDocumentor

and you can browse to:

http://localhost/pear/PhpDocumentor/

for the web interface

------
WARNING: The PDF Converter will not work in PHP5.  The PDF library that it relies upon
segfaults with the simplest of files.  Generation still works great in PHP4
------

- WARNING: phpDocumentor installs phpdoc in the
  scripts directory, and this will conflict with PHPDoc,
  you can\'t have both installed at the same time
- Switched to Smarty 2.6.0, now it will work in PHP 5.  Other
  changes made to the code to make it work in PHP 5, including parsing
  of private/public/static/etc. access modifiers
- fixed these bugs:
 [ 834941 ] inline @link doesn\'t work within <b>
 [ 839092 ] CHM:default:default produces bad links
 [ 839466 ] {$array[\'Key\']} in heredoc
 [ 840792 ] File Missing XML:DocBook/peardoc2:default "errors.tpl"
 [ 850731 ] No DocBlock template after page-level DocBlock?
 [ 850767 ] MHW Reference wrong
 [ 854321 ] web interface errors with template directory
 [ 856310 ] HTML:frames:DOM/earthli missing Class_logo.png image
 [ 865126 ] CHM files use hard paths
 [ 875525 ] <li> escapes <pre> and ignores paragraphs
 [ 876674 ] first line of pre and code gets left trimmed
 [ 877229 ] PHP 5 incompatibilities bork tutorial parsing
 [ 877233 ] PHP 5 incompatibilities bork docblock source highlighting
 [ 878911 ] [PHP 5 incompatibility] argv
 [ 879068 ] var arrays tripped up by comments
 [ 879151 ] HTML:frames:earthli Top row too small for IE
 [ 880070 ] PHP5 visability for member variables not working
 [ 880488 ] \'0\' file stops processing
 [ 884863 ] Multiple authors get added in wrong order.
 [ 884869 ] Wrong highligthing of object type variables
 [ 892305 ] peardoc2: summary require_once Path/File.php is PathFile.php
 [ 892306 ] peardoc2: @see of method not working
 [ 892479 ] {@link} in // comment is escaped
 [ 893470 ] __clone called directly in PackagePageElements.inc
 [ 895656 ] initialized private variables not recognized as private
 [ 904823 ] IntermediateParser fatal error
 [ 910676 ] Fatal error: Smarty error: unable to write to $compile_dir
 [ 915770 ] Classes in file not showing
 [ 924313 ] Objec access on array
',
'package' => 'PhpDocumentor',
'dir_roles' => array(
    'Documentation' => 'doc',
    'Documentation/tests' => 'test',
    'docbuilder' => 'data',
    'HTML_TreeMenu-1.1.2' => 'data',
    'tutorials' => 'doc',
    'phpDocumentor/Converters/CHM/default/templates/default/templates_c' => 'data',
    'phpDocumentor/Converters/PDF/default/templates/default/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/default/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/l0l33t/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/phpdoc.de/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/phphtmllib/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/phpedit/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/earthli/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/DOM/default/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/DOM/l0l33t/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/DOM/phpdoc.de/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/DOM/phphtmllib/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/frames/templates/DOM/earthli/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/Smarty/templates/default/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates_c' => 'data',
    'phpDocumentor/Converters/HTML/Smarty/templates/HandS/templates_c' => 'data',
    'phpDocumentor/Converters/XML/DocBook/peardoc2/templates/default/templates_c' => 'data',
    ),
'simpleoutput' => true,
'exceptions' =>
    array(
        'index.html' => 'data',
        'README' => 'doc',
        'ChangeLog' => 'doc',
        'PHPLICENSE.txt' => 'doc',
        'poweredbyphpdoc.gif' => 'data',
        'INSTALL' => 'doc',
        'FAQ' => 'doc',
        'Authors' => 'doc',
        'Release-1.2.0beta1' => 'doc',
        'Release-1.2.0beta2' => 'doc',
        'Release-1.2.0beta3' => 'doc',
        'Release-1.2.0rc1' => 'doc',
        'Release-1.2.0rc2' => 'doc',
        'Release-1.2.0' => 'doc',
        'Release-1.2.1' => 'doc',
        'Release-1.2.2' => 'doc',
        'Release-1.2.3' => 'doc',
        'Release-1.2.3.1' => 'doc',
        'Release-1.3.0' => 'doc',
        'pear-phpdoc' => 'script',
        'pear-phpdoc.bat' => 'script',
        'HTML_TreeMenu-1.1.2/TreeMenu.php' => 'php',
        'phpDocumentor/Smarty-2.6.0/libs/debug.tpl' => 'php',
        'new_phpdoc.php' => 'data',
        'phpdoc.php' => 'data',
        ),
'ignore' =>
    array('package.xml', 
          "$packagedir/phpdoc",
          'phpdoc.bat', 
          'LICENSE',
          '*docbuilder/actions.php',
          '*docbuilder/builder.php',
          '*docbuilder/config.php',
          '*docbuilder/file_dialog.php',
          '*docbuilder/top.php',
          'utilities.php',
          'Converter.inc',
          'IntermediateParser.inc',
          '*templates/PEAR/*',
          'phpDocumentor/Smarty-2.5.0/*',
          '*CSV*',
          'Setup.inc.php',
          'makedocs.ini',
          'common.inc.php',
          'publicweb-PEAR-1.2.1.patch.txt',
          ),
'installas' =>
    array('pear-phpdoc' => 'phpdoc',
          'pear-phpdoc.bat' => 'phpdoc.bat',
          'docbuilder/pear-actions.php' => 'docbuilder/actions.php',
          'docbuilder/pear-builder.php' => 'docbuilder/builder.php',
          'docbuilder/pear-config.php' => 'docbuilder/config.php',
          'docbuilder/pear-file_dialog.php' => 'docbuilder/file_dialog.php',
          'docbuilder/pear-top.php' => 'docbuilder/top.php',
          'docbuilder/includes/pear-utilities.php' => 'docbuilder/includes/utilities.php',
          'phpDocumentor/pear-IntermediateParser.inc' => 'phpDocumentor/IntermediateParser.inc',
          'phpDocumentor/pear-Converter.inc' => 'phpDocumentor/Converter.inc',
          'phpDocumentor/pear-Setup.inc.php' => 'phpDocumentor/Setup.inc.php',
          'phpDocumentor/pear-common.inc.php' => 'phpDocumentor/common.inc.php',
          'user/pear-makedocs.ini' => 'user/makedocs.ini',
          ),
'installexceptions' => array('pear-phpdoc' => '/', 'pear-phpdoc.bat' => '/', 'scripts/makedoc.sh' => '/'),
));
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addPlatformException('pear-phpdoc.bat', 'windows');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addDependency('php', '4.1.0', 'ge', 'php');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
// just to make sure people don't try to install this with a broken Archive_Tar
$e = $test->addDependency('Archive_Tar', '1.1', 'ge');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
// optional dep for peardoc2 converter
$e = $test->addDependency('XML_Beautifier', '1.1', 'ge', 'pkg', true);
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
// replace @PHP-BIN@ in this file with the path to php executable!  pretty neat
$e = $test->addReplacement('pear-phpdoc', 'pear-config', '@PHP-BIN@', 'php_bin');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@BIN-DIR@', 'bin_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@PEAR-DIR@', 'php_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-builder.php', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-file_dialog.php', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-file_dialog.php', 'pear-config', '@WEB-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-actions.php', 'pear-config', '@WEB-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-config.php', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('docbuilder/pear-config.php', 'pear-config', '@WEB-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('phpDocumentor/pear-Setup.inc.php', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('phpDocumentor/pear-Converter.inc', 'pear-config', '@DATA-DIR@', 'data_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('phpDocumentor/pear-common.inc.php', 'package-info', '@VER@', 'version');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('user/pear-makedocs.ini', 'pear-config', '@PEAR-DIR@', 'php_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('user/pear-makedocs.ini', 'pear-config', '@DOC-DIR@', 'doc_dir');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$e = $test->addReplacement('user/pear-makedocs.ini', 'package-info', '@VER@', 'version');
if (PEAR::isError($e)) {
    echo $e->getMessage();
    exit;
}
$test->addRole('inc', 'php');
$test->addRole('sh', 'script');
if (isset($_GET['make'])) {
    $e = $test->writePackageFile();
} else {
    $e = $test->debugPackageFile();
}
if (PEAR::isError($e)) {
    echo $e->getMessage();
}
if (!isset($_GET['make'])) {
    echo '<a href="' . $_SERVER['PHP_SELF'] . '?make=1">Make this file</a>';
}
?>