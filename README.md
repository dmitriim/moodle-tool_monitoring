# Moodle monitoring admin tool
A simple plugin does checks of critical service dependencies (cron, DB, filesystem).

# Using
Install the plugin normally and navigate to http://site.example.com/admin/tool/monitoring/index.php?password=opensesame to get results.
This may be useful for Nagios system to hit URL and parse result.

#Settings

Site administration > Plugins > Admin Tools > Monitoring

* Password for remote access: If this is left empty, no password is required.
* Use JSON: If enabled the output will be in JSON format
* Display debug messages: If enabled debug messages will be dispalyed

By default password, JSON and debug messages are enabled.

# Installation
Install the plugin to admin/tool/monitoring

# Output examples

* JSON enabled

{"cron":{"result":"error","info":"The maintenance script has not been run for at least 24 hours."},"db":{"result":"error","info":"Error writing to databaseERROR: duplicate key value violates unique constraint \"mdl_conf_nam_uix\"\nDETAIL: Key (name)=(dbhealth) already exists.\nINSERT INTO mdl_config (name,value) VALUES($1,$2) RETURNING id\n[array (\n 'name' => 'dbhealth',\n 'value' => 1430189003,\n)]"},"dataroot":{"result":"good"},"overall":{"result":"down"}}

* JSON disabled

Cron: error
info: The maintenance script has not been run for at least 24 hours.
Database: error
info: Error writing to databaseERROR: duplicate key value violates unique constraint "mdl_conf_nam_uix" DETAIL: Key (name)=(dbhealth) already exists. INSERT INTO mdl_config (name,value) VALUES($1,$2) RETURNING id [array ( 'name' => 'dbhealth', 'value' => 1430188957, )]
Dataroot: good
Overall: down

* Incorrect password

Access Password Wrong


