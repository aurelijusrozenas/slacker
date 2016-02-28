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

Deploying with capifony
=======================
- copy `app/config/capifony/deploy.rb.dist` to `app/config/capifony/deploy.rb`
- update values at the top of the file
- run `cap deploy`

TODO
====
- [ ] html loading splash
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
- [x] capifony
- [x] cache limit in parameters
- [x] force cache reload
