import json
import subprocess

with open('repo_list.json') as json_file:
    data = json.load(json_file)
    print('######################################### Repositories #########################################')
    for repo in data['repositories']:
        print('%-25s %s' % (repo['name'], repo['url']))
    print('################################################################################################')
    print('')
    for repo in data['repositories']:
        subprocess.check_call("cd " + repo['name'] + " && git reset --hard origin/master && git pull && cd ..", shell=True)
        print('')