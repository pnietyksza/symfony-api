# symfony-api

That's a simple API using Symfony framework.

Here you've got a command to pull informations about currencies from offical bank API.
symfony console fetchdata

Endpoints: 

'(GET)/currencies - is the endpoint with whole informations about all currencies',
'(GET)/currency/code - is with informations about one currency *(code(3 string)',
'(POST)/currency - you can create your own currency *(name(255 string),code(3 string),value(float))',
'(PUT)/currency - for actualization one currency *(name(255 string),code(3 string),value(float))',
'(DELETE)/currency - for delete currency *(code(3 string))'

