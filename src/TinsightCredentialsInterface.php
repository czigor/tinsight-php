<?php

namespace Czigor\Tinsight;

interface TinsightCredentialsInterface {

  protected $username = '';

  protected $password = '';

  protected $id = '';

  protected $token = '';

  /**
   * Username getter.
   */
  public function getUsername();

  /**
   * Username setter.
   *
   * @param string $username
   */
  public function setUsername(string $username);

  /**
   * Password getter.
   */
  public function getPassword();

  /**
   * Password setter.
   *
   * @param string $password
   */
  public function setPassword(string $password);

  /**
   * Id getter.
   *
   */
  public function getId();

  /**
   * Id setter.
   */
  public function setId(string $id);

  /**
   * Token getter.
   */
  public function getToken();

  /**
   * Token setter.
   *
   * @param string $token
   */
  public function setToken(string $token);

}
