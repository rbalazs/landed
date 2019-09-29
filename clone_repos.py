import json
import subprocess

with open('config/repositories/repo_list.json') as json_file:
    data = json.load(json_file)
    print('######################################### Repositories #########################################')
    for repo in data['repositories']:
        print('%-25s %s' % (repo['name'], repo['url']))
    print('################################################################################################')
    print('')
    subprocess.check_call("cd repos", shell=True)
    for repo in data['repositories']:
        subprocess.check_call("git clone " + repo['url'] + " repos/" + repo['name'], shell=True)
        print('')