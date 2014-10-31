#!/bin/bash

DB=mysql
OCWD=`pwd`
BUNDLE=$1

function header {
    echo ""
    echo -e "\e[32m======================================================\e[0m"
    echo $1
    echo -e "\e[32m======================================================\e[0m"
    echo ""
}

function comment {
    echo ""
    echo -e "\e[33"$1"\e[0m"
    echo ""
}

cat <<EOT
   _____       _        _____ __  __ ______ 
  / ____|     | |      / ____|  \/  |  ____|
 | (___  _   _| |_   _| |    | \  / | |__   
  \___ \| | | | | | | | |    | |\/| |  __|  
  ____) | |_| | | |_| | |____| |  | | |     
 |_____/ \__,_|_|\__,_|\_____|_|  |_|_|     
                                            
EOT

header "Initializing database"
./src/Sulu/Bundle/TestBundle/Resources/bin/travis.sh &> /dev/null

if [ -z $BUNDLE ]; then
    BUNDLES=`find ./src/Sulu/Bundle/* -maxdepth 1 -name "phpunit.xml.dist"`
else
    BUNDLES=`find ./src/Sulu/Bundle/$BUNDLE -maxdepth 1 -name "phpunit.xml.dist"`
fi

for BUNDLE in $BUNDLES; do

    BUNDLE_DIR=`dirname $BUNDLE`
    BUNDLE_NAME=`basename $BUNDLE_DIR`

    header $BUNDLE_NAME

    if [ -e $BUNDLE_DIR"/Tests/Resources/app/AppKernel.php" ]; then
        export KERNEL_DIR=$BUNDLE_DIR"/Tests/Resources/app"
    elif [ -e $BUNDLE_DIR"/Tests/app/AppKernel.php" ]; then
        export KERNEL_DIR=$BUNDLE_DIR"/Tests/app"
    else
        export KERNEL_DIR=""
    fi


    cd $BUNDLE_DIR

    if [[ ! -z "$KERNEL_DIR" ]]; then
        comment "Kernel: "$KERNEL_DIR
        env KERNEL_DIR=$OCWD"/"$KERNEL_DIR $OCWD/bin/console doctrine:schema:update --force
    fi

    if [ ! -e vendor ]; then
        ln -s $OCWD"/vendor" vendor
    fi

    cd -

    phpunit --configuration phpunit.travis.xml.dist --stop-on-error $BUNDLE_DIR/Tests
done
