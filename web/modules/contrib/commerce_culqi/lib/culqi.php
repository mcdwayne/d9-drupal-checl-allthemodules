<?php
define('CULQI_SDK_VERSION', '1.1.0');
class UrlAESCipher { protected $key;
                    protected $cipher = MCRYPT_RIJNDAEL_128;
                    protected $mode = MCRYPT_MODE_CBC; function __construct($spfff864 = null) { $this->setBase64Key($spfff864); }
  public function setBase64Key($spfff864) { $this->key = base64_decode($spfff864); }
  private function spf2f55d() {
    if ($this->key != null) { return true; }
    else { return false; } }
  private function sp15bdd1() { return mcrypt_create_iv(16, MCRYPT_RAND); }
  public function urlBase64Encrypt($sp663bda) { if ($this->spf2f55d())
    { $spc46841 = mcrypt_get_block_size($this->cipher, $this->mode);
      $sp2dff03 = UrlAESCipher::pkcs5_pad($sp663bda, $spc46841);
      $sp22e643 = $this->sp15bdd1();
    return trim(UrlAESCipher::base64_encode_url($sp22e643 . mcrypt_encrypt($this->cipher, $this->key, $sp2dff03, $this->mode, $sp22e643))); }
    else { throw new Exception('Invlid params!'); } }
  public function urlBase64Decrypt($sp663bda) {
    if ($this->spf2f55d())
    { $sp8cead0 = UrlAESCipher::base64_decode_url($sp663bda);
      $sp22e643 = substr($sp8cead0, 0, 16);
      $sp56e722 = substr($sp8cead0, 16);
  return trim(UrlAESCipher::pkcs5_unpad(mcrypt_decrypt($this->cipher, $this->key, $sp56e722, $this->mode, $sp22e643))); }
  else { throw new Exception('Invlid params!'); } }
  public static function pkcs5_pad($spfaa380, $spc46841) { $sp572de5 = $spc46841 - strlen($spfaa380) % $spc46841;
  return $spfaa380 . str_repeat(chr($sp572de5), $sp572de5); }
  public static function pkcs5_unpad($spfaa380)
            { $sp572de5 = ord($spfaa380[strlen($spfaa380) - 1]);
              if ($sp572de5 > strlen($spfaa380))
              { return false; }
              if (strspn($spfaa380, chr($sp572de5), strlen($spfaa380) - $sp572de5) != $sp572de5)
              { return false; }
              return substr($spfaa380, 0, -1 * $sp572de5); }
              protected function base64_encode_url($spe1ff85) {
              return strtr(base64_encode($spe1ff85), '+/', '-_'); }
              protected function base64_decode_url($spe1ff85) {
              return base64_decode(strtr($spe1ff85, '-_', '+/')); } }

class Culqi { public static $llaveSecreta;
              public static $codigoComercio;
              public static $servidorBase = 'https://pago.culqi.com';
              public static function cifrar($spe32b84) { $spd86527 = new UrlAESCipher();
                                          $spd86527->setBase64Key(Culqi::$llaveSecreta);
              return $spd86527->urlBase64Encrypt($spe32b84); }
              public static function decifrar($spe32b84) { $spd86527 = new UrlAESCipher();
                $spd86527->setBase64Key(Culqi::$llaveSecreta);
              return $spd86527->urlBase64Decrypt($spe32b84); } }
class Pago { const URL_VALIDACION_AUTORIZACION = '/api/v1/web/crear/';
            const URL_ANULACION = '/api/v1/anular/';
            const URL_CONSULTA = '/api/v1/consultar/';
            const PARAM_COD_COMERCIO = 'codigo_comercio';
            const PARAM_EXTRA = 'extra';
            const PARAM_SDK_INFO = 'sdk';
            const PARAM_NUM_PEDIDO = 'numero_pedido';
            const PARAM_MONTO = 'monto';
            const PARAM_MONEDA = 'moneda';
            const PARAM_DESCRIPCION = 'descripcion';
            const PARAM_COD_PAIS = 'cod_pais';
            const PARAM_CIUDAD = 'ciudad';
            const PARAM_DIRECCION = 'direccion';
            const PARAM_NUM_TEL = 'num_tel';
            const PARAM_INFO_VENTA = 'informacion_venta';
            const PARAM_TICKET = 'ticket';
            const PARAM_VIGENCIA = 'vigencia';
            const PARAM_CORREO_ELECTRONICO = 'correo_electronico';
            const PARAM_NOMBRES = 'nombres';
            const PARAM_APELLIDOS = 'apellidos';
            const PARAM_ID_USUARIO_COMERCIO = 'id_usuario_comercio';
            private static function getSdkInfo()
              { return array('v' => CULQI_SDK_VERSION, 'lng_n' => 'php', 'lng_v' => phpversion(), 'os_n' => PHP_OS, 'os_v' => php_uname()); }
            public static function crearDatospago($sp29e1f9, $spb6b844 = null)
              { Pago::validateParams($sp29e1f9); $sp0f9123 = Pago::getCipherData($sp29e1f9, $spb6b844);
                $sp6e29a7 = array(Pago::PARAM_COD_COMERCIO => Culqi::$codigoComercio, Pago::PARAM_INFO_VENTA => $sp0f9123);
                $spf05b2e = Pago::validateAuth($sp6e29a7);
                if (!empty($spf05b2e) && array_key_exists(Pago::PARAM_TICKET, $spf05b2e))
                { $spcaa02c = array(Pago::PARAM_COD_COMERCIO => $spf05b2e[Pago::PARAM_COD_COMERCIO],
                  Pago::PARAM_TICKET => $spf05b2e[Pago::PARAM_TICKET]);
                  $spf05b2e[Pago::PARAM_INFO_VENTA] = Culqi::cifrar(json_encode($spcaa02c)); }
            return $spf05b2e; }
            public static function consultar($spcdeba9) { $sp0f9123 = Pago::getCipherData(array(Pago::PARAM_TICKET => $spcdeba9));
              $sp29e1f9 = array(Pago::PARAM_COD_COMERCIO => Culqi::$codigoComercio, Pago::PARAM_INFO_VENTA => $sp0f9123);
            return Pago::postJson(Culqi::$servidorBase . Pago::URL_CONSULTA, $sp29e1f9); }
            public static function anular($spcdeba9) { $sp0f9123 = Pago::getCipherData(array(Pago::PARAM_TICKET => $spcdeba9));
                          $sp29e1f9 = array(Pago::PARAM_COD_COMERCIO => Culqi::$codigoComercio, Pago::PARAM_INFO_VENTA => $sp0f9123);
            return Pago::postJson(Culqi::$servidorBase . Pago::URL_ANULACION, $sp29e1f9); }
            private static function getCipherData($sp29e1f9, $spb6b844 = null) { $sp923812 = array_merge(array(Pago::PARAM_COD_COMERCIO => Culqi::$codigoComercio), $sp29e1f9);
              if (!empty($spb6b844)) { $sp923812[Pago::PARAM_EXTRA] = $spb6b844; } $sp923812[Pago::PARAM_SDK_INFO] = Pago::getSdkInfo(); $sp684082 = json_encode($sp923812);
              return Culqi::cifrar($sp684082); }
            private static function validateAuth($sp29e1f9) { return Pago::postJson(Culqi::$servidorBase . Pago::URL_VALIDACION_AUTORIZACION, $sp29e1f9); }
            private static function validateParams($sp29e1f9) { if (!isset($sp29e1f9[Pago::PARAM_MONEDA])
              or empty($sp29e1f9[Pago::PARAM_MONEDA]))
              { throw new InvalidParamsException('[Error] Debe existir una moneda'); }
              else { if (strlen(trim($sp29e1f9[Pago::PARAM_MONEDA])) != 3)
                { throw new InvalidParamsException('[Error] La moneda debe contener exactamente 3 caracteres.'); } }
                if (!isset($sp29e1f9[Pago::PARAM_MONTO]) or empty($sp29e1f9[Pago::PARAM_MONTO]))
                { throw new InvalidParamsException('[Error] Debe existir un monto'); }
                else { if (is_numeric($sp29e1f9[Pago::PARAM_MONTO])) {
                  if (!ctype_digit($sp29e1f9[Pago::PARAM_MONTO]))
                  { throw new InvalidParamsException('[Error] El monto debe ser un número entero, no flotante.'); } }
                  else { throw new InvalidParamsException('[Error] El monto debe ser un número entero.'); } } }
                private static function postJson($sp342647, $sp29e1f9) { $sp22cf0a = array('http' => array('header' => "Content-Type: application/json\r\n" . "User-Agent: php-context\r\n", 'method' => 'POST', 'content' => json_encode($sp29e1f9), 'ignore_errors' => true));
                  $spbac785 = stream_context_create($sp22cf0a);
                  $spf05b2e = file_get_contents($sp342647, false, $spbac785);
                  $sp93bd22 = Culqi::decifrar($spf05b2e);
              return json_decode($sp93bd22, true); } }
    class InvalidParamsException extends Exception { }
