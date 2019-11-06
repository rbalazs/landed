#!/usr/bin/env bash

dir=$1

cd "$dir"

git log --after="2018-04-01T00:00:00" --merges --first-parent | grep Author |  awk '{print $4}' | sed "s/<//g" | sed "s/>//g" | sed "s/@[a-z]*\.[a-z]*//g" | sort | uniq -c | sort -rg

