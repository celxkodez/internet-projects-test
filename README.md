# Internet Project LTD Symfony Developer Test

<p align="center">
<a href="https://github.com/symfony/symfony/actions"><img src="https://github.com/symfony/symfony/actions/workflows/unit-tests.yml/badge.svg?branch=6.2" alt="Build Status"></a>
<a href="https://packagist.org/packages/symfony/symfony"><img src="https://img.shields.io/packagist/l/symfony/symfony" alt="License"></a>
</p>

## About This Project

> This Project is a Symfony project aimed at demonstration of skill level of the developer.

## Installation
<p>Clone this Repository with</p>

```bash
git clone https://github.com/celxkodez/internet-projects-test.git 
```
<p>navigate to the project directory.</p>

* Note: this project requires docker for easy setup.

#### with docker
<p>On the Project root directory, run the commands</p>


```bash
    make start
```
the above builds docker and starts your services
```bash
    make vendor
```
Log in to the container instance with
```bash
    make sh
```
then run the application migration with 
```bash
    php bin/console doctrine:migration:migrate
```
and enter 'yes' on the prompt



[//]: # (to seed the database, simply use)

[//]: # ()
[//]: # (```bash)

[//]: # (    make artisan-command p=db:seed)

[//]: # (```)

after that, visit you can view the application on your local machine
with http://127.0.0.1:8000/ or http://localhost:8000/

if everything this is successful, visiting that url show a valid web page
## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)