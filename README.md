DCSOAuthBundle
==============

The DCSOAuthBundle integrates HWIOAuthBundle with FOSUserBundle in Symfony2 and allows users to login/register to your site using third party authentication.

This bundle uses an Entity, related to the FOS user table, to store the user's data retrieved by the authentication service. The data will be loaded in the user's session while the user is authenticated.

This bundle does not modify the FOSUserBundle login/registration flow. When a new user authenticates trough the DCSOAuthBundle, a new record is created in the FOS user table, but standard user data (username, password, email) will be blank.