#!/usr/bin/env bash

dir=$1

cd "$dir"

git log | grep Date |  awk '{print $5}' | awk -F ":" '{print $1}' | sort | uniq -c | sort -g

