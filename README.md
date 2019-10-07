# Goal
Lets see the statistics of multiple git repositories on embeddable charts and diagrams.

![Preview](cmt_per_day_project.png)

Most frequently changed files, commits distribution per weekday or per hour, number of changed lines per commmit, etc. The diagrams shall be embeddable image files at the end of the day.

## Setup the project
### Bootstrap the enviroment
#### scp env file
`scp .env.dist .env`
#### host file
`127.0.0.1       landed.docker`
#### docker env
`docker-compose up -d --build`

`docker-compose exec php composer install`

Mind, that for cloning private repositories, the host machine has to provide user configuration, and a valid SSH context. 
```
      - /etc/group:/etc/group:ro
      - /etc/passwd:/etc/passwd:ro
      - /etc/shadow:/etc/shadow:ro
      - $SSH_AUTH_SOCK:/ssh-agent:ro
      - $HOME/.ssh/known_hosts:$HOME/.ssh/known_hosts:ro
```

### Load up repositories
#### Copy repository list, and set the url list
`scp config/repositories/repo_list.json.dist config/repositories/repo_list.json`

**_[Example](https://github.com/rbalazs/landed/blob/70711a3a8108d5e0cf27d62ece7ac2972b83711a/repos/repo_list.json.dist)_**
 - #### clone repositories
`python clone_repos.py`

 - #### update already cloned repositories
`python update_repos.py`

### Have a look
http://landed.docker/overview
