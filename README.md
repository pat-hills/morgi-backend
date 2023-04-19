## Start docker
Before to start we have to build the image with:
```
docker-compose build
```
Once the build is finished run:
```
docker-compose up -d
```
To enter container's CLI run:
```
docker exec -it morgi-backend bash
```

####!!! DO NOT EXECUTE PHP COMMANDS OUTSIDE THE CONTAINER !!!
## Import sql dump
Go into DatabaseSeeder.php, removed commented code and run: (the process will take 1 minute)
```
php artisan db:seed
```

## GIT Branch Standards
###master
master branch is attached to production. NOBODY can push directly in master.
Before to go in master you have to push on staging, let Morgi's team test and after Cristian can
merge staging in master. THIS IS THE ONLY WAY TO GO IN PRODUCTION.

###staging
We go in staging for hotfixes and tests of new features before master.

###staging-dev
We push develop in staging-dev when we finished a release, and we need to let Morgi's team
test it.

###develop
We use develop to push features in development from feature/{name} branch.
