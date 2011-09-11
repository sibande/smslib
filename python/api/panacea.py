import json
import requests

class Panacea(object):
    """Panacea HTTP API Python class"""
    
    url = 'http://api.panaceamobile.com/json'
    _action = ''
    
    def __init__(self, username=None, password=None, url=None):
        if url is not None:
            self.url = url
        self.api_data = {'username': username, 'password': password,
                         'url': self.url}
    def action(self, name, *args, **kwargs):
        """Calls the Panacea API action and returns the JSON decoded result"""
        
        actions = requests.get(self.url + '?action=list_actions')
        actions = json.loads(actions.content)
        if name not in actions['details']:
            raise TypeError('%s is not a valid action' % name)
        action_args = actions['details'][name]
        
        if action_args[1]['name'] == 'password':
            args = (self.api_data['password'],) + args
        if action_args[0]['name'] == 'username':
            args = (self.api_data['username'],) + args
        params = ''
        required_args = 0
        for (index, arg) in enumerate(action_args):
            if not arg['optional']:
                required_args += 1
            if index < len(args):
                params += '&' + arg['name'] + '=%s'
        if required_args > len(args):
            raise TypeError('%s requires %d arguments (%d given)' % (name,
                            required_args, len(args)))
        response = requests.get(self.url + '?action=' + name +  params % args)

        return json.loads(response.content)
        
    def __getattr__(self, name):
        """Handles API actions as class methods"""
        self._action = name
        def _action(*args, **kwargs):
            return self.action(name, *args, **kwargs)
        return _action
