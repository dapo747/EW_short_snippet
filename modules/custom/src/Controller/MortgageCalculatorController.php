<?php

namespace Drupal\mortgage_calculator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides a Mortgage Calculator form.
 */
class MortgageCalculatorForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mortgage_calculator_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['principal'] = [
      '#type' => 'number',
      '#title' => $this->t('Loan Amount'),
      '#required' => TRUE,
      '#min' => 0,
    ];

    // Fetch interest rates from TD API
    $interest_rates = $this->getInterestRates();
    
    $form['interest_rate'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Interest Rate'),
      '#options' => $interest_rates,
      '#required' => TRUE,
    ];

    $form['years'] = [
      '#type' => 'number',
      '#title' => $this->t('Loan Term (in years)'),
      '#required' => TRUE,
      '#min' => 1,
    ];

    $form['calculate'] = [
      '#type' => 'submit',
      '#value' => $this->t('Calculate'),
    ];

    if ($form_state->get('monthly_payment')) {
      $form['result'] = [
        '#markup' => $this->t('Monthly Payment: @payment', ['@payment' => $form_state->get('monthly_payment')]),
      ];
    }

    return $form;
  }

  /**
   * Fetch interest rates from TD Bank API.
   */
  protected function getInterestRates() {
    $client = new Client();
    $interest_rates = [];

    try {
      $response = $client->request('GET', 'https://api.td.com/interest-rates', [
        'headers' => [
          'Authorization' => 'Bearer YOUR_API_KEY', // Replace with your API key
        ],
      ]);

      $data = json_decode($response->getBody(), TRUE);
      
      // Assuming the response has an array of rates
      foreach ($data['rates'] as $rate) {
        $interest_rates[$rate['id']] = $rate['rate'];
      }

    } catch (RequestException $e) {
      \Drupal::logger('mortgage_calculator')->error($e->getMessage());
      $interest_rates['default'] = $this->t('Unable to fetch interest rates');
    }

    return $interest_rates;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $principal = $form_state->getValue('principal');
    $interest_rate = $form_state->getValue('interest_rate') / 100;
    $years = $form_state->getValue('years');

    $monthly_interest_rate = $interest_rate / 12;
    $number_of_payments = $years * 12;

    $monthly_payment = $principal * ($monthly_interest_rate * pow(1 + $monthly_interest_rate, $number_of_payments)) / (pow(1 + $monthly_interest_rate, $number_of_payments) - 1);
    $form_state->set('monthly_payment', number_format($monthly_payment, 2));
  }
}
