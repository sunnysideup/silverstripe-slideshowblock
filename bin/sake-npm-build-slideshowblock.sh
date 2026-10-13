#!/bin/bash

source ~/.nvm/nvm.sh
nvm use node

echo '------------------------------'
echo ' run build'
echo '------------------------------'
vendor/bin/webpack build vendor/sunnysideup/slideshowblock/client/ --no-jquery
echo '------------------------------'
