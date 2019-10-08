#!/usr/bin/env bash

dir=$1

cd "$dir"

git log --no-merges | grep Author |  awk '{print $4}' | sed "s/<//g" | sed "s/>//g" | sed "s/@[a-z]*\.[a-z]*//g" | sort | uniq -c | sort -rg

