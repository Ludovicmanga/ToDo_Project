How to contribute to the project
================================

Pull requests
-----------------------

This project could be improved by other developers. In order to suggest changes to the project, developers must proced by pull requests.
First of all, every developer must clone the project : https://github.com/Ludovicmanga/ToDo_Project.git

Every developer must then suggest changes by using git.

``git push``


Coding standards
----------------

This project was made using Symfony. The contributors must thus respect Symfony coding standards. These standards are detailed here : https://symfony.com/doc/current/contributing/code/standards.html

Community Reviews
-----------------
This project is opensource. It is crucial that it be reviewed by contributors.
The review process concerns mostly pull requests.

Be Constructive
~~~~~~~~~~~~~~~
In these reviews, it is crucial to keep constructive in the reviews. By keeping in mind that it is the result of somebody's hard work.

The Pull Request Review Process
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Pick a pull request from the PRs in need of review and follow these steps:

1. Check if the PR is complete

Every pull request must contain a header that gives some basic information about the PR.

2. Is the Base Branch Correct?

During the review process, the base branch must be checked.

3. Read the issue that the pull request is supposed to fix. Reproduce the problem on a new project created with the Symfony skeleton (or the Symfony website skeleton) and try to understand why it exists. If the linked issue already contains such a project, install it and run it on your system.

4. Review the Code

5. Read the code of the pull request and check it against some common criteria:

- Does the code address the issue the PR is intended to fix/implement?
- Does the PR stay within scope to address only that issue?
- Does the PR contain automated tests? Do those tests cover all relevant edge cases?
- Does the PR contain sufficient comments to understand its code?
- Does the code break backward compatibility? If yes, does the PR header say so?
- Does the PR contain deprecations? If yes, does the PR header say so? Does the code contain trigger_deprecation() statements for all deprecated features?
- Are all deprecations and backward compatibility breaks documented in the latest UPGRADE-X.X.md file? Do those explanations contain “Before”/”After” examples with clear upgrade instructions?

6. Test the Code

7. Update the PR Status

At last, add a comment to the PR. Thank the contributor for working on the PR. Include the line Status: <status> in your comment to trigger our Carson Bot which updates the status label of the issue. You can set the status to one of the following:
