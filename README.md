#Landed
Get the statistics of multiple git projects, displayed on embeddable charts and diagrams.

![Preview](cmt_per_day_project.png)

## Setup the project
### Bootstrap the enviroment
#### scp env file
`scp .env.dist .env`
#### host file
`127.0.0.1       landed.docker`
#### docker env
`docker-compose up -d --build`

`docker-compose exec php composer install`

### Load up repositories
#### copy repository list, and set the urls
`scp repos/repo_list.json.dist repos/repo_list.json`
#### clone repositories
`cd repos && python clone_repos.py`

###Have a look
http://landed.docker/overview