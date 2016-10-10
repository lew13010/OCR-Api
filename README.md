# OCR-Api
[Projet] Offrez vos services via une API

## REQUIREMENTS

* [stofDoctrineExtensionsBundle](https://github.com/stof/StofDoctrineExtensionsBundle)
* Account ApiGoogleMaps

## DOWNLOAD

```
$ git clone https://github.com/lew13010/OCR-Api.git
```

OR

Download [master.zip](https://github.com/lew13010/OCR-Api/archive/master.zip), and unzip in your folder
## INSTALLATION

#### Enable the bundle

 * Enable the bundle in your *app/AppKernel.php*
 
 ```
 class AppKernel extends Kernel
 {
     public function registerBundles()
     {
         $bundles = [
         ...
             new Api\AdvertBundle\ApiAdvertBundle()
         ]
 ```
 
#### Routes
 * Routes *(app/config/routing.yml)*:
 
  ```
  api_advert:
      resource: "@ApiAdvertBundle/Resources/config/routing.yml"
      prefix:   /
  ```
#### Configuration and Parameters

 * Activate Sluggable *(app/config/config.yml)*:
 ```
 stof_doctrine_extensions:
     orm:
         default:
             sluggable: true
 ```
 
 * Key ApiGoogleMap :
 ```
 parameters:
    key_google_api: xxx
 ```
 
#### Database 
 ```
 $ php bin/console doctrine:database:create
 ```
 ```
 $ php bin/console doctrine:schema:update --force
 ```
 
## USE

* IMPORTANT
 
 Create in first one category and one city before your first advert. 