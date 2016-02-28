Slacker
=======
Displays Slack team's message counts for channels.

Installation 
============
- git clone repository
- run `composer install -o`
- set parameters (when composer requests)
    - Slack token can be created in https://api.slack.com/docs/oauth-test-tokens
- thats it!

TODO
====
- [ ] force cache reload
- [ ] cache limit in parameters
- [ ] deleting messages:
    - [ ] allow setting date limit
    - [ ] select channels
    - [ ] remember selected channels
    - [ ] display information when deleting messages
    - [ ] display progress
    - [ ] allow cancel
    - [ ] estimate time
- [ ] cron for deleting messages
- [ ] secure login/access token
