# EW_short_snippet
Evaluation for Dimitar

This code is only for evaluation purposes for Evolving Web. It is a sampel code for Drupal to add the ability to select an interest rate and fetch the current interest rates from TD Bank using an API. It also includes a dropdown for selecting the interest rate and incorporates an API call to retrieve the latest rates from TD Bank.

#---------------------------- STEPS TO EXECUTE IT---------------------------#

Step 1 Install Guzzle HTTP Client

Make sure the Guzzle HTTP client is available in your Drupal installation. If it is not already installed, you can add it using Composer:

composer require guzzlehttp/guzzle


Step 2 Configure Your API Key

In the getInterestRates() method, replace 'YOUR_API_KEY' with your actual TD Bank API key. Make sure you have access to the TD interest rates API and that you follow their API documentation for proper endpoint usage.

Step 3 Clear Cache

After making these changes, clear the Drupal cache:

drush cr

Access it from here:
You can now access your mortgage calculator at your-drupal-site/mortgage-calculator. The dropdown for interest rates will be populated with the latest rates fetched from the TD Bank API.

