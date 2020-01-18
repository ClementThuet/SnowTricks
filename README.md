# ClementsBlog
A collaborative website to introduce and learn snowboard tricks, made with Symfony 4 during my formation with OpenClassrooms in september 2019 - january 2020.<br>
The goals are to get a list of tricks than any register user can contribuate to. <br>
It's possible to update/delete tricks, add them medias (pictures or embeds video) and to comment under each trick.<br>
Users have the possilibity to register, an email is sent to activate their account. They can also use the "forgotten password" feature to reset their password and create a new one.

Code quality reviewed by Codacy :
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b5a6e9f4471949b4a44d05646647d122)](https://www.codacy.com/manual/ClementThuet/SnowTricks?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ClementThuet/SnowTricks&amp;utm_campaign=Badge_Grade)

## Getting Started

### Requirements
To install the project you will need :
* An Apache server >=2.4
* PHP >= 7.1
* MySQL or another database of your choice<br> 

I recommend to use WampServer as I did.

### Installing
You can get the project by using git clone (If you don't know how to do it, more info here : https://git-scm.com/book/it/v2/Git-Basics-Getting-a-Git-Repository)
```
$ git clone https://github.com/ClementThuet/SnowTricks.git
```
Then you need to execute `composer install` into the project folder to install the dependencies.<br>
If you don't have composer you can get it here https://getcomposer.org/doc/00-intro.md

I defined a virtualhost which point to my public folder in a way to get URLs like "http://snowtricks/figure/360".<br>
With wampserver you can configure it by going to : Wampserver icon (in the taskbar) => Your virtualhosts => virtualhosts management. <br>
You should have this line after configuration :
```
ServerName : snowtricks - Directory : c:/wamp64/www/snowtricks/public
```

### Database import 
You can find the database structure to import in the folder "Database structure".<br>
Then you can simply import snowtricks.sql.

### Database configuration
Configure your database according to your personal configuration in .env. For me:

```
DATABASE_URL=mysql://root:@127.0.0.1:3306/SnowTricks
```

You can now load fixtures to get nice tricks you can edit the way you like. It provides 10 tricks and the user whom added theim.

```
php bin/console doctrine:fixtures:load
```

### Sending email to activate and reset account's passwords
I use symfony google mailer (more info here : https://symfony.com/doc/current/components/mailer.html). <br>
You must configure it with your gmail's credentials in .env file :
```
###> symfony/google-mailer ###
GMAIL_USERNAME=clement*******@gmail.com
GMAIL_PASSWORD=************
MAILER_DSN=smtp://$GMAIL_USERNAME:$GMAIL_PASSWORD@gmail
###< symfony/google-mailer ###
```
Don't forget to allow the use of less secure applications in your google's parameters (https://myaccount.google.com/lesssecureapps?pli=1).

That's all, you can now access to SnowTricks and sign in to add your first trick. Enjoy !


## Author
**Clément Thuet**
* https://www.linkedin.com/in/cl%C3%A9ment-thuet/
* https://github.com/ClementThuet/

