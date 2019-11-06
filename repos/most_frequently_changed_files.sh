#!/usr/bin/env bash

dir=$1

cd "$dir"

git log --no-merges --after="2018-04-01T00:00:00" --pretty=format: --name-only | sort | uniq -c | sort -rg | head -20

