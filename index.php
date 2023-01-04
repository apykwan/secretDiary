<?php
  session_start();
  $error = "";

  if(array_key_exists("logout", $_GET)) {
    // unset the session
    session_unset();
    // expired the cookie
    setCookie("id", "", time() - 60 * 60);
    $_COOKIE["id"] = "";
  } else if(array_key_exists("id", $_SESSION) OR array_key_exists("id", $_COOKIE)) {
    // go to the logggedinpage if you are still logged in
    header("Location: loggedinpage.php");
  }

  if(array_key_exists("submit", $_POST)) {
    include('connection.php');

    if(!$_POST['email']) {
      $error .= "An email address is required. <br>";
    }

    if(!$_POST['password']) {
      $error .= "A password is required. <br>";
    }

    if($error != "") {
      $error = "<p>There were error(s) in your form: </p>" . $error;
    } else {
      $emailAddress = mysqli_real_escape_string($link, $_POST['email']);
      $password = mysqli_real_escape_string($link, $_POST['password']);
      $password = password_hash($password, PASSWORD_DEFAULT);

      if($_POST['signUp'] == '1') {
        $query = "SELECT id from users WHERE email = '" . $emailAddress . "' LIMIT 1";
        $results = mysqli_query($link, $query);

        if(mysqli_num_rows($results) > 0) {
          $error = "That email address is taken!";
        } else {
          
          $query = "INSERT INTO users (email, password) VALUES('" . $emailAddress . "', '" . $password . "')";
          if(!mysqli_query($link, $query)) {
            $error .= "<p>Could not sign you up - Please try again later.</p>";
            $error .= "<p?>" . mysqli_error($link) . "</p>";
          } else {
            $id = mysqli_insert_id($link);

            $_SESSION['id'] = $id;

            if(isset($_POST['stayLoggedIn'])) {
              setCookie("id", $id, time() + 60 * 60 * 24 * 365);

              header("Location: loggedinpage.php");
            }
          }
        }
      } else {
        //logging in
        $query = "SELECT * from users WHERE email = '" . $emailAddress . "'";
        $results = mysqli_query($link, $query);
        $row = mysqli_fetch_array($results);
        $password = mysqli_real_escape_string($link, $_POST['password']);

        if(isset($row) AND array_key_exists("password", $row)) {
          $passwordMatches = password_verify($password, $row['password']);

          if($passwordMatches) {
            $_SESSION['id'] = $row['id'];

            if(isset($_POST['stayLoggedIn'])) {
              setcookie("id", $row['id'], time() + 60 * 60 * 24 * 365);
            }

            header("Location: loggedinpage.php");
          } else {
            $error = "Invalid email or password.";
          }
        } else {
          $error = "Invalid email or password.";
        }
      }
    }
  }
?>

<?php include('header.php'); ?>
    <div id="homePageContainer" class="container">
      <h1>Store Your Thought</h1>
      <div id="error">
        <?php 
          if($error != "") {
            echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
          }
        ?>
      </div>

      <!-- SIGN UP FORM -->
      <form method="post" id="signUpForm">
        <p>Interested? Sign Up Now</p>
        <input type="hidden" name="signUp" value="1">
        <fieldset class="form-group mb-3">
          <label for="email">Email Address</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
        </fieldset>
        <fieldset class="form-group mb-3">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
        </fieldset>
        <fieldset class="checkbox">
          Stay Logged In:
          <input type="checkbox" name="stayLoggedIn" value="1"> 
        </fieldset>
        <input type="submit" name="submit" class="submit-btn btn btn-dark w-100" value="Register">
        
        <p><a class="toggleForms">Log In</a></p>
      </form>

      <!-- LOG IN FORM -->
      <form method="post" id="logInForm">
        <p>Log in with your email and password</p>
        <input type="hidden" name="signUp" value="0">
        <fieldset class="form-group mb-3">
          <label for="email">Email Address</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
        </fieldset>
        <fieldset class="form-group mb-3">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
        </fieldset>
        <fieldset class="checkbox">
          Stay Logged In:
          <input type="checkbox" name="stayLoggedIn" value="1"> 
        </fieldset>
        <input type="submit" name="submit" class="submit-btn btn btn-dark w-100" value="Log In">

        <p><a class="toggleForms">Register</a></p>
      </form>
    </div>

<?php include('footer.php'); ?>