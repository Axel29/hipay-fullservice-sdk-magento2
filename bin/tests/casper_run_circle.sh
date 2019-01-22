#!/bin/bash

if [ "$1" != "" ]; then
    BASE_URL=$1
else
    BASE_URL=$MAGENTO_URL
    if [ "$PORT_WEB" != "80" ];then
        BASE_URL=$BASE_URL:$PORT_WEB
    fi
fi


if [ "$URL_MAILCATCHER" = "" ];then
    URL_MAILCATCHER="http://smtp:1080/"
fi

cd bin/tests/000_lib
npm install
cd ../../../;

header="bin/tests/"

casperjs test ${header}000*/0_init/000*.js ${header}0[0-1][0-9]*/[0-1]*/[0-9][0-9][0-9][0-9]-*.js --url=$BASE_URL/ --url-mailcatcher=$URL_MAILCATCHER --login-backend=$LOGIN_BACKEND --pass-backend=$PASS_BACKEND --xunit=${header}result.xml --ssl-protocol=TLSv1.2 --fail-fast --engine=slimerjs --headless
