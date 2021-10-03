Authentication system
======================

How authentication works on Symfony
-----------------------------------

This documentation's purpose is to explain how the authentication system works on symfony. For this project, we implemented the new Authenticator-based Security system (https://symfony.com/doc/current/security/authenticator_manager.html).

For this project, we used the security component of Symfony, that used firewalls to protect the website.

When a request points to a secured area of the website, we need to set a listener, called an"authenticator". Its job will be to extract the user’s credentials from the current Symfony\Component\HttpFoundation\Request object, it should create a token, containing these credentials.

The next thing the listener should do is ask the authentication manager to validate the given token, and return an authenticated token if the supplied credentials were found to be valid. The listener should then store the authenticated token using the token storage.

Where the users are stored?
~~~~~~~~~~~~~~~~~~~~~~~~~~~

- Storage of users in the database :
    Users are first stored in a database. The user provider then uses Doctrine to retrieve them. The entity provider can only query from one specific field, specified by the property config key. In our case, we chose the username.

- Storage in the session :
    The logged user is stored inside a token, using the token storage.

At the end of every request (unless your firewall is stateless), your User object is serialized to the session. At the beginning of the next request, it’s deserialized and then passed to your user provider to “refresh” it (e.g. Doctrine queries for a fresh user). Then, the two User objects (the original from the session and the refreshed User object) are “compared” to see if they are “equal”. By default, the core AbstractToken class compares the return values of the getPassword(), getSalt() and getUserIdentifier() methods. If any of these are different, your user will be logged out. This is a security measure to make sure that malicious users can be de-authenticated if core user data changes.

What file should be modified and why to implement authentication in Symfony
---------------------------------------------------------------------------

Implementing the security component
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The first thing that must be done in order to implement authentication, if it is not already done, is to install the symfony's security component by entering into the ACL "composer require symfony/security-bundle".

Creating a valid user class
~~~~~~~~~~~~~~~~~~~~~~~~~~~

We then need to create a user class that implements the UserInterface. In order to implement it, some function must be created:
    - getRoles()
    - getPassword()
    - getSalt()
    - eraseCredentials()

That is what we did with the class App\Entity\User.

Properly setting security.yaml file
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The next thing that should be done is setting the package\security.yaml file. We first need to set the parameter enable_authenticator_manager to true. It will activate the new authentication system.

We then must set the PasswordHasher algorithm, which is the algorithm that Symfony will use to hash the passwords of users so that a hacker cannot know the actual passwords even if the database is hacked.
In our case, we chose bcrypt::

    password_hashers:
        App\Entity\User: 
            algorithm: bcrypt

We then need to Define a user provider::

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: username


The firewalls must then be set. In our case, we set the "main" firewall. It is recommended to only use one firewall (appart from the dev firewall).

Implement a Login Authenticator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

We then must implement an authentication listener for this firewall. This listener has the role of performing some actions whenever a user tries to login. If we want it, we could create several authenticators, and write the path to them by setting the parameter custom_authenticators.
What we did is to create the class App\Security\LoginFormAuthenticator, that extends the class Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator.

Any authenticator (authentication listener) must implement the AuthenticatorInterface, that is the case for AbstractLoginFormAuthenticator.

Once again, some methods must be implemented :

- Supports() : 
            Its mission is to determine whether the authenticator support the given Request.
            If this returns false, the authenticator will be skipped. Otherwise, the function authenticate() will be called.
            In our case, the authentication should be made using the route 'app_login', which leads to the URL /login, and to the login form.
            If the request is supported, the function authenticate() is called.

- Authenticate() :
            It creates a "passport" for the current request with key information about the user, such as the password.
            If the user is found, the function createAuthenticatedToken() will be created, using the information from the "passport".

- createAuthenticatedToken() :
            It will create an "authenticated token" for the given user.

- onAuthenticationSuccess() :
            If the login is successfull, this function will be called.
            It will define what happens after a successful login

- onAuthenticationFailure() :
            If the login is unsuccessfull, this function will be called.
            It will define what happens after an unsuccessful login

As we said earlier, if the authentication is successful, the user token will then be stored into the session, that will be accessible from many files of the app, such as controllers.

