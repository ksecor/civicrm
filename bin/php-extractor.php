#!/opt/php5/bin/php
<?php



/**
 * ts() calls extractor
 *
 * Drupal's t() extractor from http://drupal.org/project/drupal-pot
 * modified to suit CiviCRM's ts() calls
 *
 * Extracts translatable strings from specified function calls, plus adds some
 * file specific strings. Only literal strings with no embedded variables can
 * be extracted. Outputs a POT file on STDOUT, errors on STDERR
 *
 * @author Jacobo Tarrio <jtarrio [at] alfa21.com>
 * @author Gabor Hojtsy <goba [at] php.net>
 * @author Piotr Szotkowski <shot@caltha.pl>
 * @copyright 2003, 2004 Alfa21 Outsourcing
 * @license http://www.gnu.org/licenses/gpl.html  GNU General Public License
 */



/**
 * tsCallType return values
 */
define('TS_CALL_TYPE_INVALID', 0);
define('TS_CALL_TYPE_SINGLE', 1);
define('TS_CALL_TYPE_PLURAL', 2);



/**
 * Checks the type of the ts() call
 *
 * TS_CALL_TYPE_SINGLE  for a call resulting in calling gettext() (singular)
 * TS_CALL_TYPE_PLURAL  for a call resulting in calling ngettext() (plural)
 * TS_CALL_TYPE_INVALID for an invalid call
 *
 * @param array $tokens  the array with tokens from token_get_all()
 *
 * @return int  the integer representing the type of the call
 */
function tsCallType($tokens)
{

    // $tokens[0] == 'ts', $tokens[1] == '('
    $mid = $tokens[2];
    $rig = $tokens[3];

    // $mid has to be a T_CONSTANT_ENCAPSED_STRING
    if (!is_array($mid) or ($mid[0] != T_CONSTANT_ENCAPSED_STRING)) {
        return TS_CALL_TYPE_INVALID;
    }

    // if $rig is a closing paren, it's a valid call with no variables,
    // else $rig has to be a comma
    if ($rig == ')') {
        return TS_CALL_TYPE_SINGLE;
    } elseif ($rig != ',') {
        return TS_CALL_TYPE_INVALID;
    }

    // if $rig is a comma the next token must be a T_ARRAY call
    // and the next one must be an opening paren
    if ($tokens[4][0] != T_ARRAY or $tokens[5] != '(') {
        return TS_CALL_TYPE_INVALID;
    }

    // if there's an array, it cannot be empty
    // i.e. no ts('string', array()) calls
    if ($tokens[6] == ')') {
        return TS_CALL_TYPE_INVALID;
    }

    // let's iterate through the ts()'s array(...) contents
    $i = 6;
    $haveCount = false;
    $havePlural = false;

    while($i < count($tokens)) {
        $key = $tokens[$i];
        $doubleArrow = $tokens[$i + 1];
        $value = $tokens[$i + 2];

        // if it's not a => in the middle, it's not an array, really
        if ($doubleArrow[0] != T_DOUBLE_ARROW) {
            return TS_CALL_TYPE_INVALID;
        }

        if ($key[1] == "'count'" or $key[1] == '"count"') {
            // no double count declarations
            if ($haveCount) {
                return TS_CALL_TYPE_INVALID;
            }
            $haveCount = true;

        } elseif ($key[1] == "'plural'" or $key[1] == '"plural"') {
            // no double plural declarations
            if ($havePlural) {
                return TS_CALL_TYPE_INVALID;
            }
            $havePlural = true;
            // plural value must be a string
            if ($value[0] != T_CONSTANT_ENCAPSED_STRING) {
                return TS_CALL_TYPE_INVALID;
            }

        // no non-number keys (except count and plural, above)
        } elseif ($key[0] != T_LNUMBER) {
            return TS_CALL_TYPE_INVALID;

        }

        // what should we do next...
        if ($tokens[$i + 3] == ')' or ($tokens[$i + 3] == ',' and $tokens[$i + 4] == ')')) {
            // ...we've reached the last element of the ts()'s array(...)
            break;
        } else {
            // ...let's go to the next element of the ts()'s array(...)
            $i += 4;
        }

    }

    // both present - we have a plural!
    if ($haveCount and $havePlural) {
        return TS_CALL_TYPE_PLURAL;

    // only one present - no deal
    } elseif ($haveCount or $havePlural) {
        return TS_CALL_TYPE_INVALID;

    // all of the array's keys are of type T_LNUMBER - it's a single call
    } else {
        return TS_CALL_TYPE_SINGLE;

    }

}



/**
 * Gets the plural string from the ts()'s array
 *
 * @param array $tokens  the array with tokens from token_get_all()
 *
 * @return string  the string containing the "plural" string from the ts()'s array
 */
function getPluralString($tokens)
{
    $plural = "";
    if (tsCallType($tokens) == TS_CALL_TYPE_PLURAL) {
        $i = 6;
        while($i < count($tokens)) {
            $key = $tokens[$i];
            $value = $tokens[$i + 2];
            if ($key[1] == "'plural'" or $key[1] == '"plural"') {
                $plural = $value[1];
                break;
            }
            $i += 4;
        }
    }
    return $plural;
}



/**
 * Find all of the ts() calls
 *
 * @param array  $tokens  the array with tokens from token_get_all()
 * @param string $file    the string containing the file name
 *
 * @return void
 */
function find_ts_calls($tokens, $file)
{

    global $strings;

    // iterate through all the tokens while there's still a chance for
    // a ts() call
    while (count($tokens) > 3) {

        list($ctok, $par, $mid, $rig, $arr) = $tokens;

        // the first token has to be a T_STRING (with a function name)
        if (!is_array($ctok)) {
            array_shift($tokens);
            continue;
        }

        // check whether we're at ts(
        list($type, $string, $line) = $ctok;
        if (($type == T_STRING) && ($string == 'ts') && ($par == '(')) {

            switch (tsCallType($tokens)) {

            case TS_CALL_TYPE_SINGLE:
                $strings[format_quoted_string($mid[1])][$file][] = $line;
                break;

            case TS_CALL_TYPE_PLURAL:
                $plural = getPluralString($tokens);
                $strings[format_quoted_string($mid[1]) . "\0" . format_quoted_string($plural)][$file][] = $line;
                break;

            case TS_CALL_TYPE_INVALID:
                marker_error($file, $line, 'ts', $tokens);
                break;

            default:
                break;

            }

        }

        array_shift($tokens);

    }

}



  set_time_limit(0);
  if (!defined("STDERR")) {
    define("STDERR", fopen("php://stderr", "w"));
  }
  
  $argv = $GLOBALS['argv'];
  array_shift ($argv);
  if (!count($argv)) {
    print "Usage: extractor.php file1 [file2 [...]]\n\n";
    return 1;
  }


  $strings = $file_versions = array();

  foreach ($argv as $file) {
    $code = file_get_contents($file);
    
    // Extract raw tokens
    $raw_tokens = token_get_all($code);

    // Remove whitespace and HTML
    $tokens = array();
    $lineno = 1;
    foreach ($raw_tokens as $tok) {
      if ((!is_array($tok)) || (($tok[0] != T_WHITESPACE) && ($tok[0] != T_INLINE_HTML))) {
        if (is_array($tok)) {
          $tok[] = $lineno;
        }
        $tokens[] = $tok;
      }
      if (is_array($tok)) {
        $lineno += count(split("\n", $tok[1])) - 1;
      } else {
        $lineno += count(split("\n", $tok)) - 1;
      }
    }
    
    //find_t_calls($tokens, $file);
    //find_watchdog_calls($tokens, $file);
    //find_format_plural_calls($tokens, $file);

    find_ts_calls($tokens, $file);
    
    find_perm_hook($code, $file);
    find_node_types_hook($code, $file);
    find_module_name($code, $file);
    find_language_names($code, $file);
    find_version_number($code, $file);
    
    add_date_strings($file);
    add_format_interval_strings($file);
  }


  foreach ($strings as $str => $fileinfo) {
    $occured = $filelist = array();
    foreach ($fileinfo as $file => $lines) {
      $occured[] = "$file:" . join(";", $lines);
      if (isset($file_versions[$file])) {
        $filelist[] = $file_versions[$file];
      }
    }
    
    $output = "#: " . join(" ", $occured) . "\n";
    $filename = ((count($occured) > 1) ? 'general' : $file);

    if (strpos($str, "\0") === FALSE) {
      $output .= "msgid \"$str\"\n";
      $output .= "msgstr \"\"\n";
    }
    else {
      list ($singular, $plural) = explode("\0", $str);
      $output .= "msgid \"$singular\"\n";
      $output .= "msgid_plural \"$plural\"\n";
      $output .= "msgstr[0] \"\"\n";
      $output .= "msgstr[1] \"\"\n";
    }
    $output .= "\n";

    store($filename, $output, $filelist);
  }

  write_files();

  function write_files() {
    $output = store(0, 0, array(), 1);
    foreach ($output as $file => $content) {
      if (count($content) <= 11 && $file != 'general') {
        @$output['general'][1] = array_unique(array_merge($output['general'][1], $content[1]));
        if (!isset($output['general'][0])) {
          $output['general'][0] = $content[0];
        }
        unset($content[0]);
        unset($content[1]);
        foreach ($content as $msgid) {
          $output['general'][] = $msgid;
        }
        unset($output[$file]);
      }
    }
    foreach ($output as $file => $content) {
      $tmp = preg_replace('<[/]?([a-z]*/)*>', '', $file);
      $file = str_replace('.', '-', $tmp) .'.pot';
      $filelist = $content[1]; unset($content[1]);
      if (count($filelist) > 1) {
        $filelist = "Generated from files:\n#  " . join("\n#  ", $filelist);
      }
      elseif (count($filelist) == 1) {
        $filelist = "Generated from file: " . join("", $filelist);
      }
      else {
        $filelist = "No version information was available in the source files.";
      }
      $fp = fopen($file, 'w');
      fwrite($fp, str_replace("--VERSIONS--", $filelist, join("", $content)));
      fclose($fp);
    }
  }

  function store($file = 0, $input = 0, $filelist = array(), $get = 0) {
    static $storage = array();
    if (!$get) {
      if (isset($storage[$file])) {
       $storage[$file][1] = array_unique(array_merge($storage[$file][1], $filelist));
       $storage[$file][] = $input;
      }
      else {
        $storage[$file] = array();
        $storage[$file][0] = write_header($file);
        $storage[$file][1] = $filelist;
        $storage[$file][2] = $input;
      }
    }
    else {
      return $storage;
    }
  }

  function write_header($file) {
    $output  = "# LANGUAGE translation of Drupal (". $file .")\n";
    $output .= "# Copyright YEAR NAME <EMAIL@ADDRESS>\n";
    $output .= "# --VERSIONS--\n";
    $output .= "#\n";
    $output .= "#, fuzzy\n";
    $output .= "msgid \"\"\n";
    $output .= "msgstr \"\"\n";
    $output .= "\"Project-Id-Version: PROJECT VERSION\\n\"\n";
    $output .= "\"POT-Creation-Date: " . date("Y-m-d H:iO") . "\\n\"\n";
    $output .= "\"PO-Revision-Date: YYYY-mm-DD HH:MM+ZZZZ\\n\"\n";
    $output .= "\"Last-Translator: NAME <EMAIL@ADDRESS>\\n\"\n";
    $output .= "\"Language-Team: LANGUAGE <EMAIL@ADDRESS>\\n\"\n";
    $output .= "\"MIME-Version: 1.0\\n\"\n";
    $output .= "\"Content-Type: text/plain; charset=utf-8\\n\"\n";
    $output .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
    $output .= "\"Plural-Forms: nplurals=INTEGER; plural=EXPRESSION;\\n\"\n\n";

    return $output;
  }

  function format_quoted_string($str) {
    $quo = substr($str, 0, 1);
    $str = substr($str, 1, -1);
    if ($quo == '"') {
      $str = stripcslashes($str);
    } else {
      $str = strtr($str, array("\\'" => "'", "\\\\" => "\\"));
    }
    return addcslashes($str, "\0..\37\\\"");
  }
  
  function marker_error($file, $line, $marker, $tokens) {
    fwrite(STDERR, "Invalid marker content in $file:$line\n* $marker(");
    array_shift($tokens); array_shift($tokens);
    $par = 1;
    while (count($tokens) && $par) {
      if (is_array($tokens[0])) {
        fwrite(STDERR, $tokens[0][1]);
      } else {
        fwrite(STDERR, $tokens[0]);
        if ($tokens[0] == "(") {
          $par++;
        }
        if ($tokens[0] == ")") {
          $par--;
        }
      }
      array_shift($tokens);
    }
    fwrite(STDERR, "\n\n");
  }
  
  /*
    Detect all occurances of one of these sequences:
      T_STRING("t") + "(" + T_CONSTANT_ENCAPSED_STRING + ")"
      T_STRING("t") + "(" + T_CONSTANT_ENCAPSED_STRING + ","
  */
  function find_t_calls($tokens, $file) {
    global $strings;
    
    while (count($tokens) > 3) {
      
      list($ctok, $par, $mid, $rig) = $tokens;
      if (!is_array($ctok)) {
        array_shift($tokens);
        continue;
      }
      list($type, $string, $line) = $ctok;
      
      if (($type == T_STRING) && ($string == "t") && ($par == "(")) {
        
        if (in_array($rig, array(")", ","))
            && (is_array($mid) && ($mid[0] == T_CONSTANT_ENCAPSED_STRING))) {

          $strings[format_quoted_string($mid[1])][$file][] = $line;
        }
        
        // t() found, but inside is something which is not a string literal
        else {
          marker_error($file, $line, "t", $tokens);
        }
      }
      array_shift($tokens);
    }
  }



  
  /*
    Detect all occurances this sequence:
      T_STRING("format_plural") + "(" + ..anything (might be more tokens).. +
      "," + T_CONSTANT_ENCAPSED_STRING +
      "," + T_CONSTANT_ENCAPSED_STRING + ")"
  */
  function find_format_plural_calls($tokens, $file) {
    global $strings;
    
    while (count($tokens) > 7) {
      
      list($ctok, $par1) = $tokens;
      if (!is_array($ctok)) {
        array_shift($tokens);
        continue;
      }
      list($type, $string, $line) = $ctok;
      
      if (($type == T_STRING) && ($string == "format_plural") && ($par1 == "(")) {
        
        // Eat up everything that is used as the first parameter
        $nt = $tokens;
        array_shift($nt); array_shift($nt);
        $depth = 0;
        while (!($nt[0] == "," && $depth == 0)) {
          if ($nt[0] == "(") {
            $depth++;
          }
          elseif ($nt[0] == ")") {
            $depth--;
          }
          array_shift($nt);
        }
        
        // Get further parameters
        list($comma1, $singular, $comma2, $plural, $par2) = $nt;
        
        if (($comma2 == ",") && ($par2 == ")") &&
            (is_array($singular) && ($singular[0] == T_CONSTANT_ENCAPSED_STRING)) &&
            (is_array($plural) && ($plural[0] == T_CONSTANT_ENCAPSED_STRING))) {

          $strings[format_quoted_string($singular[1]) .
          "\0" .
          format_quoted_string($plural[1])][$file][] = $line;
        }
        
        // format_plural() found, but the parameters are not correct
        else {
          marker_error($file, $line, "format_plural", $tokens);
        }
      }
      array_shift($tokens);
    }
  }
  
  /*
    Detect all occurances of this sequence:
      T_STRING("watchdog") + "(" + T_CONSTANT_ENCAPSED_STRING + ","
  */
  function find_watchdog_calls($tokens, $file) {
    global $strings;
    
    while (count($tokens) > 3) {
      
      list($ctok, $par, $mid, $rig) = $tokens;
      if (!is_array($ctok)) {
        array_shift($tokens);
        continue;
      }
      list($type, $string, $line) = $ctok;
      
      if (($type == T_STRING) && ($string == "watchdog") && ($par == "(")) {
        
        if (($rig == ",")
            && (is_array($mid) && ($mid[0] == T_CONSTANT_ENCAPSED_STRING))) {

          $strings[format_quoted_string($mid[1])][$file][] = $line;
        }
        
        // watchdog() found, but inside is something which is not a string literal
        else {
          marker_error($file, $line, "watchdog", $tokens);
        }
      }
      array_shift($tokens);
    }
  }
  
  // This will get confused if a similar pattern is found in a comment...
  function find_perm_hook($code, $file) {
    global $strings;
    
    if (preg_match('!^(.+function \\w+_perm\\(\\) \\{\s+return)([^\\}]+)\\}!Us', $code, $hook_code)) {
      $lines = substr_count($hook_code[1], "\n") + 1;
      preg_match_all('!(["\'])([a-z ]+)\1!', $hook_code[2], $items, PREG_PATTERN_ORDER);
      foreach ($items[2] as $item) {
        $strings[$item][$file][] = $lines;
      }
    }
  }
  
  // This will also get confused if a similar pattern is found in a comment...
  function find_node_types_hook($code, $file) {
    global $strings;
    
    if (preg_match('!^(.+function \\w+_node_types\\(\\) \\{\s+return)([^\\}]+)\\}!Us', $code, $hook_code)) {
      $lines = substr_count($hook_code[1], "\n") + 1;
      preg_match_all('!(["\'])([0-9a-z-]+)\1!', $hook_code[2], $items, PREG_PATTERN_ORDER);
      foreach ($items[2] as $item) {
        $strings[$item][$file][] = $lines;
      }
    }
  }
  
  // This will get confused if a similar pattern is found in a comment...
  function find_module_name($code, $file) {
    global $strings;

    if (preg_match('!function (\\w+)_help\\(!', $code, $module_name) &&
        $module_name[1] != 'menu_get_active') {
      $strings[$module_name[1]][$file][] = 0;
    }
  }
  
  function find_language_names($code, $file) {
    global $strings;
    
    if (preg_match("!locale\\.inc$!", $file) &&
        preg_match("!^(.+function _locale_get_iso639_list\\(\\) {)([^\\}]+)\\}!Us", $code, $langcodes)) {
      $lines = substr_count($langcodes[1], "\n") + 1;
      preg_match_all('!array\\((["\'])([^\'"]+)\1!', $langcodes[2], $items, PREG_PATTERN_ORDER);
      foreach ($items[2] as $item) {
        $strings[$item][$file][] = $lines;
      }
    }
  }
  
  // Get the exact version number from the file, so we can push that into the pot
  function find_version_number($code, $file) {
    global $file_versions;

    // Prevent CVS from replacing this pattern with actual info
    if (preg_match('!\\$I' . 'd: ([^\\$]+) Exp \\$!', $code, $version_info)) {
      $file_versions[$file] = $version_info[1];
    }
  }
  
  // Add date strings if locale.module is parsed
  function add_date_strings($file) {
    global $strings;
  
    if (preg_match('!(^|/)locale.module$!', $file)) {
      for ($i = 1; $i <= 12; $i++) {
        $stamp = mktime(0, 0, 0, $i, 1, 1971);
        $strings[date("F", $stamp)][$file][] = 0;
        $strings[date("M", $stamp)][$file][] = 0;
      }

      for ($i = 0; $i <= 7; $i++) {
        $stamp = $i * 86400;
        $strings[date("D", $stamp)][$file][] = 0;
        $strings[date("l", $stamp)][$file][] = 0;
      }
    }
  }
  
  // Add format_interval special strings if common.inc is parsed
  function add_format_interval_strings($file) {
    global $strings;
  
    if (preg_match('!(^|/)common.inc$!', $file)) {
      $components = array(
        '1 year' => '%count years',
        '1 week' => '%count weeks',
        '1 day'  => '%count days',
        '1 hour' => '%count hours',
        '1 min'  => '%count min',
        '1 sec'  => '%count sec');
      
      foreach($components as $singular => $plural) {
        $strings[$singular."\0".$plural][$file][] = 0;
      }
    }
  }
  
  return;

  // These are never executed, you can run extractor.php on itself to test it
  $a = ts("Test string 1" );
  //$b = ts("Test string 2 %string", array("%string" => "how do you do"));
  $c = ts('Test string 3');
  $d = ts("Special\ncharacters");
  $e = ts('Special\ncharacters');
  //$f = ts("Embedded $variable");
  $g = ts('Embedded $variable');
  $h = ts("more \$special characters");
  $i = ts('even more \$special characters');
  $j = ts("Mixed 'quote' \"marks\"");
  $k = ts('Mixed "quote" \'marks\'');
  $l = ts('This is some repeating text');
  $m = ts("This is some repeating text");
  //$n = ts(embedded_function_call());
  $o = format_plural($days, "one day", "%count days");
  $p = format_plural(embedded_function_call($count), "one day", "%count days");

  $s1 = ts('Shot’s test with a %1 variable, and %2 another one', array(1 => 'one', 2 => 'two'));
  $s2 = ts('%3 – Shot’s plural test, %count frog', array('count' => 7, "plural" => 'Shot’s plural test, %count frogs', 3 => 'three'));
  //$s3 = ts('Shot’s test – no count', array('plural' => 'No count here'));
  //$s4 = ts('Shot’s test – no plural', array('count' => 42));
  
  function embedded_function_call() { return 12; }
  
  function extractor_perm() {
    return array("access extrator data", 'administer extractor data');
  }
  
  function extractor_help($section = 'default') {
    watchdog('help', ts('Help called'));
    return ts('This is some help');
  }
  
  function extractor_node_types() {
    return array("extractor-cooltype", "extractor-evencooler");
  }
  
?>
