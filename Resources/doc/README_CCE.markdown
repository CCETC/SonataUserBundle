# CCETC/SonataUserBundle - README

This bundle is a forked version of the [SonataUserBundle](https://github.com/sonata-project/SonataUserBundle).
It contains some customizations to the sonata-project bundle.
The customizations affect the installation and configuration of the bundle, so see below for installation and configuration instructions.
For full documentation of the original bundle refer to ``Resources/doc/reference``.

## Customizations
### Add/Modified Features
* added batch/single enable/disable actions
* removed/customized fields in UserAdmin class
* changes custom login/logout routes to use FOS controllers
* changed template directory structure
* modified roles in configuration to simplify user management

### Removed Features
* removed custom security controller and template - included in [CCETC/FOSUserBundle](https://github.com/CCETC/FOSUserBundle)
* removed user block template - included in [CCETC/SonataAdminBundle](https://github.com/CCETC/SonataAdminBundle)


## Installation
This bundle must be installed along the CCETC forks of the [SonataAdminBundle](https://github.com/CCETC/SonataAdminBundle) and the [FOSUserBundle](https://github.com/CCETC/FOSUserBundle).

### dist file
The UserAdmin class is included as a .dist file.  As different user entities in different Symfony projects will have different fields, this file will change from project to project.  So, to keep the git repo clean, the .dist file should remain untouched.

To get started you can simply copy the file and run as is, or copy and add your custom fields.

	cp Admin/Entity/UserAdmin.php.dist Admin/Entity/UserAdmin.php

To keep your customizations under version control, cp the files to your project and create symbolic links.

        cp Admin/Entity/UserAdmin.php.dist /MyApp/app/Application/Sonata/UserBundle/Admin/Entity/UserAdmin.php
        ln -s /MyApp/app/Application/Sonata/UserBundle/Admin/Entity/UserAdmin.php UserAdmin.php

## Documentation
All ISSUES, IDEAS, and FEATURES are documented on the [trello board](https://trello.com/board/sonatauserbundle/4f8f261e067c6a6d60013753).

## Areas for improvement / "broken windows"
### dist files
As documented above, several files exist as .dist files so that they can be customized without interferring with the repo.  Because of this, upstream changes from FOS to these files are not merged with our fork.  These files are basic files (forms, and templates) so there should never be considerable changes to manaully merge.

## Configuration
The original bundle is configured to handle separate logins for both the front and back ends.  This fork is configured for just one.  So login and logout routes are not prepended by ``admin/`` and only one firewall is configured in ``security.yml``.

Additionally, ROLE_SONATA_ADMIN, and ROLE_ALLOWED_TO_SWITCH roles were removed from the roles hierarchy.

	# app/config/security.yml

	security:
		encoders:
			Symfony\Component\Security\Core\User\User: plaintext
			"FOS\UserBundle\Model\UserInterface":
				algorithm: sha512
				encode_as_base64: false
				iterations: 1
		providers:
			fos_userbundle:
				id: fos_user.user_manager
				
		firewalls:
			# defaut login area for standard users
			main:
				pattern:      .*
				form_login:
					provider:       fos_userbundle
					login_path:     /login
					use_forward:    true
					always_use_default_target_path: false
					default_target_path: /admin/dashboard
					check_path:     /login_check
					failure_path:   null
				logout: 
					path:           /logout
					target:         /login
				anonymous:    true  
	            switch_user: true
	
		access_control:
			# URL of FOSUserBundle which need to be available to anonymous users
		   # - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
		   # - { path: ^/register$, role: IS_AUTHENTICATED_ANONYMOUSLY }
		   # - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
			
			- { path: ^/_wdt, role: IS_AUTHENTICATED_ANONYMOUSLY }
			- { path: ^/_profiler, role: IS_AUTHENTICATED_ANONYMOUSLY }
	
			# -> custom access control for the admin area of the URL
			- { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
			- { path: ^/logout$, role: IS_AUTHENTICATED_ANONYMOUSLY }
			- { path: ^/login-check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
	
			# Secured part of the site
			# This config requires being logged for the whole site and having the admin role for the admin part.
			# Change these rules to adapt them to your needs
			- { path: ^/admin, role: [ROLE_ADMIN] }
			- { path: ^/.*, role: IS_AUTHENTICATED_ANONYMOUSLY }
			
			
		role_hierarchy:
			ROLE_ADMIN:       ROLE_USER
			ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ADMIN]
        	SONATA:
                - ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT  # if you are not using acl then this line must be uncommented