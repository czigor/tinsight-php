<?php

namespace Czigor\Tinsight;

class TinsightCredentials implements TinsightCredentialsInterface {

  protected $username = '';

  protected $password = '';

  protected $id = '';

  protected $token = '';

  /**
   * Constructor.
   *
   * @param array $credentials
   */
  public function __construct($credentials = []) {
    $this->username = empty($credentials['username']) ? '' : $credentials['username'];
    $this->password = empty($credentials['password']) ? '' : $credentials['password'];
    $this->id = empty($credentials['id']) ? '' : $credentials['id'];
    $this->token = empty($credentials['token']) ? '' : $credentials['token'];
  }

  /**
   * @inheritdoc
   */
  public function getUsername() {
    return $this->username;
  }

  /**
   * @inheritdoc
   */
  public function setUsername(string $username) {
    $this->username = $username;
  }

  /**
   * @inheritdoc
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * @inheritdoc
   */
  public function setPassword(string $password) {
    $this->password = $password;
  }

  /**
   * @inheritdoc
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @inheritdoc
   */
  public function setId(string $id) {
    $this->id = $id;
  }

  /**
   * @inheritdoc
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * @inheritdoc
   */
  public function setToken(string $token) {
    $this->token = $token;
  }
}
