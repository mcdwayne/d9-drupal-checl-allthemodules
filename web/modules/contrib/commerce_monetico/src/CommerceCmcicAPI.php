<?php

namespace Drupal\commerce_cmcic;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Url;

class CommerceCmcicAPI {

  /**
   * Define server information.
   *
   * Get the URL of the bank server according to the bank type and the server
   * type (test or live) chosen in the module configuration.
   *
   * @param string $bank_type
   *   The bank type (Cr&eacute;dit Mutuel / CIC / OBC).
   * @param string $mode
   *   The server type (test or production).
   *
   * @return string
   *   The URL of the remote server.
   */
  public static function getServer($bank_type, $mode) {

    $url = '';

    switch ($bank_type) {

      case 'cm':
        if ($mode == 'test') {
          $url = 'https://paiement.creditmutuel.fr/test/';
        }
        else {
          $url = 'https://paiement.creditmutuel.fr/';
        }

        break;

      case 'cic':
        if ($mode == 'test') {
          $url = 'https://ssl.paiement.cic-banques.fr/test/';
        }
        else {
          $url = 'https://ssl.paiement.cic-banques.fr/';
        }

        break;

      case 'obc':
        if ($mode == 'test') {
          $url = 'https://ssl.paiement.banque-obc.fr/test/';
        }
        else {
          $url = 'https://ssl.paiement.banque-obc.fr/';
        }

        break;
      case 'monetico':
        if ($mode == 'test') {
          $url = 'https://p.monetico-services.com/test/';
        }
        else {
          $url = 'https://p.monetico-services.com/';
        }

        break;

    }

    return $url;

  }

  /**
   * Returns a unique invoice number based on the Order ID and timestamp.
   */
  public static function invoice($order) {
    return $order->id() . '-' . \Drupal::time()->getRequestTime();
  }

  /**
   * Saves an payment with some meta data related to local processing.
   *
   * @param array $payment_data
   *   An array with additional parameters for the order_id and Commerce
   *     Payment transaction_id associated with the IPN.
   *
   * @return int
   *   The operation performed by drupal_write_record() on save; since the
   *     payment data is received by reference, it will also contain the serial
   *     numeric mac used locally.
   */
  public static function saveData(&$payment_data) {

    // Create an array corresponding to the table columns.
    $history = array(
      'mac' => $payment_data['MAC'],
      'tpe' => $payment_data['tpe'],
      'payment_date' => $payment_data['date'],
      'amount' => $payment_data['montant'],
      'reference' => $payment_data['reference'],
      'texte_libre' => $payment_data['texte-libre'],
      'kit_version' => $payment_data['version'],
      'return_code' => $payment_data['code-retour'],
      'cvx' => $payment_data['cvx'],
      'vld' => $payment_data['vld'],
      'brand' => $payment_data['brand'],
      'status3ds' => isset($payment_data['status3d']) ? $payment_data['status3d'] : '',
      'numauto' => $payment_data['numauto'],
      'motifrefus' => isset($payment_data['motifrefus']) ? $payment_data['motifrefus'] : '',
      'originecb' => $payment_data['originecb'],
      'bincb' => $payment_data['bincb'],
      'hpancb' => $payment_data['hpancb'],
      'ipclient' => $payment_data['ipclient'],
      'originetr' => $payment_data['originetr'],
      'veres' => $payment_data['veres'],
      'pares' => $payment_data['pares'],
      'transaction_id' => $payment_data['transaction_id'],
      'order_id' => $payment_data['order_id'],
    );

    $database = \Drupal::database();

    if (self::historyLoad($history['mac'])) {
      $history['changed'] = REQUEST_TIME;

      $database->insert('commerce_cmcic')
        ->fields($history)
        ->execute();
    }
    else {
      $history['created'] = REQUEST_TIME;
      $history['changed'] = REQUEST_TIME;

      $database->insert('commerce_cmcic')
        ->fields($history)
        ->execute();
    }
  }

  /**
   * Loads a stored history payment by MAC.
   *
   * @param string $mac
   *   The MAC has code generated.
   *
   * @return array
   *   The array of the history corresponding to the mac code.
   */
  public static function historyLoad($mac) {
    return db_query(
      "SELECT * FROM {commerce_cmcic} WHERE mac = :mac",
      array(
        ':mac' => $mac,
      )
    )->fetchAssoc();
  }

  /**
   * Get settings for payment URLs.
   *
   * @param object $order
   *   The order object.
   * @param array $payment_method
   *   The array representing the payment method.
   *
   * @return array
   *   An array containing the payment URLs.
   */
  public static function getSettings($order) {//, $payment_method) {

    $settings = array(
      // Return to the previous page when payment is canceled.
      'cancel_return' => Url::fromUri('internal:/checkout/' . $order->id() . '/review', array('absolute' => TRUE))->toString(),

      // Return to the payment redirect page for processing successful payments.
      'return' => Url::fromUri('internal:/checkout/' . $order->id() . '/complete', array('absolute' => TRUE))->toString(),

      // Specify the current payment method instance ID in the notify_url.
      //'payment_method' => $payment_method['instance_id'],
    );

    return $settings;
  }

  /**
   * Return price.
   *
   * Get the real price value of the amount formatted like "XXX.YYCURCODE", where
   * CURCODE is based on 3 characters.
   *
   * @param int $amount
   *   The amount.
   *
   * @return string
   *   The real price without currency code value.
   */
  public static function getPriceValue($amount) {
    return Unicode::substr($amount, 0, Unicode::strlen($amount) - 3);
  }
}
