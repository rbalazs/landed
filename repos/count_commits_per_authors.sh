#!/usr/bin/env bash

dir=$1

cd "$dir"

git log --no-merges --after="2018-04-01T00:00:00" | grep Author |  awk '{print $4}' | sed "s/<//g" | sed "s/>//g" | sed "s/@[a-z]*\.[a-z]*//g" | sort | uniq -c | sort -rg

