<?php
  class Session {
    private array $messages;
    private int $timeout_duration = 300; //time in seconds

    public function __construct(bool $private) {
      session_set_cookie_params(0, '/', null, true, true ); //TODO: set domain when deploying
      session_start();

      if( $this->isLoggedIn() ) {
        if( isset($_SESSION['last_activity']) ) {
          // Calculate how long the user has been "idle"
          $elapsed_time = time() - $_SESSION['last_activity'];
          
          if( $elapsed_time > $this->timeout_duration ) {
              // Session has expired! Clear and redirect
              $this->logout();
              if( $private ) {
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
              } else {
                header('Location: ' . ($_SERVER['REQUEST_URI'] ?? 'index.php'));
              }
              exit();
          }
        }
        $_SESSION['last_activity'] = time();
      }

      $this->messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
      unset($_SESSION['messages']);
    }

    public function addMessage(string $type, string $text) {
      $_SESSION['messages'][] = array('type' => $type, 'text' => $text);
    }

    public function getMessages() {
      return $this->messages;
    }

    public function isLoggedIn() {
      return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
    }

    public function login() {
      $_SESSION['loggedIn'] = true;
    }

    public function logout() {
      session_unset();
      session_destroy();
    }
  }
?>