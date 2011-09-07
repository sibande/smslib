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
    def action(self, *args, **kwargs):
        """Calls the Panacea API action"""
        r = requests.get('http://api.panaceamobile.com/json')
        
    def __getattr__(self, name):
        self._action = name
        return self.action

if __name__ == '__main__':
    api = Panacea()
    api.messages_get(0)
    
