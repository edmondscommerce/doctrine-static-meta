# Overrides - Updating Generated Code

The generated code is designed to give you a good baseline that should cover most bases.

If you need custom fields, you should consider creating new archetype fields

For some situations though, what you really need to do is to modify the generated code. However if you do this then the next time you generate the code (which should be frequent as you constantly refactor and improve) then you will lose your changes

To resolve this issue, we have implemented an override system

## How it works

The overrides system will keep a copy of the file with your customisations and will then copy this over the generated file. 

As part of a build system, this can give you a really slick workflow.

## Protecting against clobbering new code

If you refactor your entity relations, fields etc - then the generated code will be updated accordingly. However if you previoulsy overrode that piece of code, then the risk is that you will remove the code that implements your new fields etc.

To protect against this, each override file has embedded into the filename the md5 hash of the file as it was when the override was created. Before overriding a newly generated file, the md5 hash is taken and if this does not match with that of the override file then the override is blocked and you will see an error that looks like:

```
Updating overrides toProject

In FileOverrider.php line 280:
                                                                                                                     
  These file hashes were not up to date:Array                                                                        
  (                                                                                                                  
      [/home/ec/test-entities/build/overrides/src/Entity/Fields/Traits/Website/DomainFieldTrait.7c3b6873044906  
  01e3004cee73a8dc6a.php.override] => a7a9f6d11e2ec4ce6fdf5df828c3c7c9                                               
  )                                                                                                       

```

When this happens, it means that your override is out of date.

## Suggested workflow

Due to the way the overrides system works, it is important that you stick to a defined order of work. If you don't then you will likely find yourself in a loop.


### Step 1 - Regenerate Your Code

You need to be sure that the file you want to override is exactly is it is straight after generation.

If you open the file in your IDE and auto format it, then you will change the hash. If your IDE does any other formatting automatically then you will change the hash. Anything that changes the hash will break the system.

Note - you should ensure your commit all your current work before generating the code. 

Exactly how your build process works is up to you, there will be some suggested workflows documented soon.

### Step 2 - Create your Override

After you have regnerated your code then you should create your override.

```bash
./bin/doctrine d:o:c -f ./path/to/file.php
```

### Step 3 - Edit the File

Now you can start to apply your customisations. You do this to the original file, not the newly created override file.

### Step 4

Commit your modififications to the overridden file

### Step 5

Update your override file with your modifications

```bash
./bin/doctrine dsm:overrides:update -a fromProject
```

### Step 6

Run a full code generation again

### Step 7

Ensure your override applies correctly

```bash
./bin/doctrine dsm:overrides:update -a toProject
```

## Resolving Override Conflicts

Your overrides will fail to apply when the file that is being overridden does not match the hash in your override file name.

You should only ever try to apply overrides to freshly generated files

If the override fails, it then means that your generated file has changed since you made the override and you need to diff them

### Suggested Resolution Workflow:

#### Step 1

Open the newly generated PHP file in your IDE

#### Step 2

Find the override file and diff them

(In PHPSTorm, right click the override file and select "Compare File with Editor" ) 

Obviously you are expecting the files to differ, however what you are looking for are the changes in the generated file that are not in your override file. 

You now have 2 choices:


##### A - Easiest - Remove teh current override file and create a new one

Firstly - revert your current build process

```bash
git add -A
git reset --hard HEAD
```

Take the current override file and move it somewhere safe

Now run another build which wont try to override your file so should complete successfully

Now create a new override file

Then reapply the required overrides as you would normally.


##### B - Harder - Update your existing override file

You can update the override file with the new generated code that you have cherry picked across

Then you must change the override filename so that it has the new hash in there

The new hash is displayed for you in the error message 

Once you have updated your override you need to commit only that file

```bash
git reset
git add -A build
git commit -m 'updated override file for xyz.php'
```

Then you should roll back the current build

```bash
git add -A
git reset --hard HEAD
```

Then you should retry your build process to ensure the overrides apply correctly


## A Build Process

To ensure the overrides system works smoothly, it is strongly suggested you use a scripted approach such as:

```bash

#!/usr/bin/env bash

projectRoot=/path/to/project/root

if [[ $(git diff --stat) != '' ]]
then
    echo "

################################################################################################################

    ERROR - Git Repo Is Dirty

    Please commit any changes before running this script, it is highly destructive and you could lose work

################################################################################################################

    "
    exit 1
fi

echo "Dumping Composer Autoloader"
(cd $projectRoot && php $(which composer) dumpautoload)

echo "Updating the override files with the files from the project

This will fail if the project code hits an invalid state which prevents ./bin/doctrine from running.

In that situation, you should manually confirm your override files are definitely
up to date and then run this script with the extra flag :
--skip-overrides-from-project

"

if [[ "${1:-}" != "--skip-overrides-from-project" ]]
then
    ( cd $projectRoot && php ./bin/doctrine dsm:overrides:update -a fromProject )
else
    echo "

    Skipping copying overrides from project

    WARNING - you should only do this in certain cirmstances, not by default!

    "
fi

rm -rf $projectRoot/cache/*
rm -rf $projectRoot/src/Entities/*
rm -rf $projectRoot/src/Entity/*
rm -rf $projectRoot/tests/Entities/*
rm -rf $projectRoot/tests/Assets/Entity/Fixtures/*
rm -rf $projectRoot/cache/*

echo "Dumping Composer Autoloader"
(cd $projectRoot && php $(which composer) dumpautoload)

echo "Running Build"
php Process/01_build.php

echo "Running Post Process"
php Process/02_postProcess.php

echo "Dumping Composer Autoloader"
(cd $projectRoot && php $(which composer) dumpautoload)

echo "Applying Overrides"
( cd $projectRoot && php ./bin/doctrine dsm:overrides:update -a toProject )

echo "Clearing out Metadata Cache"
( cd $projectRoot && php ./bin/doctrine orm:clear-cache:metadata)

echo "Make the code look pretty"
(cd ${projectRoot} && ./bin/qa -t bf)

```

