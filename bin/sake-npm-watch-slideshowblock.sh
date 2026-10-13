#!/bin/bash

source ~/.nvm/nvm.sh
nvm use node

echo '------------------------------'
echo ' run watch'
echo '------------------------------'
vendor/bin/webpack watch vendor/sunnysideup/slideshowblock/client/ --no-jquery
echo '------------------------------'
