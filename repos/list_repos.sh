#!/usr/bin/env bash

dir=$1

cd "$dir"

ls -l | grep ^d | awk '{print $9}' | sort

