#!/bin/bash -v

cd ..

# /*
#  * folder to be parsed
#  */
PARSE_FOLDER=$PWD

# /*
#  * target folder (documents will be generated in this folder)
#  */
TARGET_FOLDER=$PWD/docs/documentation/

# /*
#  * title of generated documentation
#  */
TITLE="civiCRM"

# /* 
#  * parse @internal and elements marked private with @access
#  */
PRIVATE=on

# /*
#  * JavaDoc-compliant description parsing
#  */
JAVADOC_STYLE=off

# /*
#  * parse a PEAR-style repository
#  */
PEAR_STYLE=on

# /*
#  * generate highlighted sourcecode for every parced file
#  */
SOURCECODE=off

# /*
#  * output information (output:converter:templatedir)
#  */
OUTPUT=HTML:frames:phpedit

phpdoc -t $TARGET_FOLDER -o $OUTPUT -d $PARSE_FOLDER -ti "$TITLE" -pp $PRIVATE -j $JAVADOC_STYLE -p $PEAR_STYLE -s $SOURCECODE
