#!/usr/bin/env bash
# Infection Configs

## Due to requiring to track random values, we can only run tests in a single thread
export numberOfCores=1

## Totally Disabling Infection for now
export useInfection=0

#Setting custom coding standard
export phpcsCodingStandardsNameOrPath="$projectConfigPath/codingStandard"