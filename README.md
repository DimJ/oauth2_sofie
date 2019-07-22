oauth2-server-php (extended for SOFIE project)
==============================================

Dolumentation for original library [complete documentation](https://bshaffer.github.io/oauth2-server-php-docs/)

SOFIE project: https://www.sofie-iot.eu/

Implementation of the models from  WF-IoT (https://mm.aueb.gr/publications/2019-WF-IoT.pdf).

First unzip the OAuth2BlockChainKeys.zip and set this path to the following files:
	- tokenScenario1.php
	- tokenScenario2.php
	- cborToken.php

Use CWT and JWS tokens with the OAuth2 protocol:

- Scenario 1 with JWS:

curl -u theClient:thePassword http://localhost/oauth2_sofie/tokenScenario1.php -d 'grant_type=client_credentials'

- Scenario 2 with JWS:

curl -u theClient:thePassword http://localhost/oauth2_sofie/tokenScenario2.php -d 'grant_type=client_credentials'

- Scenario 1 with CWT:

curl http://localhost/oauth2_sofie/cborToken.php?typeOfScenario=scenario1 -d 'grant_type=client_credentials'

- Scenario 2 with CWT:

curl http://localhost/oauth2_sofie/cborToken.php?typeOfScenario=scenario2 -d 'grant_type=client_credentials'
