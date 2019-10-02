#!/usr/bin/env bash

dir=$1

cd "$dir"

git log | grep Author |  awk '{print $4}' | sed "s/<//g" | sed "s/>//g" | sort | uniq -c | sort -rg

