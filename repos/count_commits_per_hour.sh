#!/usr/bin/env bash

dir=$1

cd "$dir"

git log --no-merges --after="2018-04-01T00:00:00" | grep Date |  awk '{print $5}' | awk -F ":" '{print $1}' | sort | uniq -c

