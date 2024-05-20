# omeka-ark-resolver
Resolve ARK's to corresponding Omeka S item (or itemset)


# Resolve flow

If you have registered a ARK for your organisation with your (root) domain, you need to add an extra piece of code (eg. in the 'custom' directory within the Omeke S directory) to resolve the ARK to the item (or itemset) which has the ARK as it's identifier.

An example flow:
- https://n2t.net/ark:/60537/b9MTov 
- [302] > https://www.goudatijdmachine.nl/ark:/60537/b9MTov
- [302] > https://www.goudatijdmachine.nl/omeka/custom/gtm-ark-resolver.php?ark=ark:/60537/b9MTov
- [302] > https://www.goudatijdmachine.nl/omeka/s/data/item/126

# Configuration

This isn't a proper Omeka S module, it's a custom PHP file to handle ARK resolving. The configuration consists of some constants in the gtm-ark-resolver.php file and webserver configuration.

# Resolver configuration

```
# the id of the property used for the ARK identifier (dc:identifier, sdo: identifier) in the omeka.property table
define('IDENTIFIER_PROPERTY',1082);  

# the path where your Omeka S instance resides (for https://www.goudatijdmachine.nl/omeka/s/data/ it's /omeka/)
define('OMEKA_S_PATH','/omeka/');

# the slug of your site as defined in Omeka S (for https://www.goudatijdmachine.nl/omeka/s/data/ it's /data/)
define('SITE_PATH','/data/');

# the location, relative to gtm-ark-resolver.php to the database settings file
define('DATABASE_INI','../config/database.ini');
```

# Webserver configuration

## Apache

You need to add the following to your Apache configuration:
```
# exception to the rule not to server php files) 
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule gtm-ark-resolver.php$ - [NC,C]
RewriteRule .* - [L]
# redirect requests with an ARK tot the resolver (adjust domain and path)
RewriteRule ^ark:/([0-9]+)/([a-zA-Z0-9]+)$ https://www.goudatijdmachine.nl/omeka/custom/gtm-ark-resolver.php?ark=ark:/$1/$2 [R=301,L]
```
