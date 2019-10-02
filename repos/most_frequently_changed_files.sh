#!/usr/bin/env bash

dir=$1

cd "$dir"

git log --pretty=format: --name-only | sort | uniq -c | sort -rg | head -20

