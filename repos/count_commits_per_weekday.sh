#!/usr/bin/env bash

dir=$1

cd "$dir"

git log | grep Date |  awk '{print $2}' | sort | uniq -c | sort -rg

